<?php

namespace Ledc\IntraCity;

use ErrorException;
use Exception;
use LogicException;
use phpseclib3\Crypt\RSA;

/**
 * 微信安全API
 */
abstract class Wechat
{
    /**
     * 配置管理类
     * @var Config
     */
    private Config $config;

    /**
     * 构造函数
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->initialize();
    }

    /**
     * 子类初始化
     * @return void
     */
    public function initialize(): void
    {
    }

    /**
     * @return Config
     */
    final public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * 对外方法用于所有微信api的请求方法
     * @param string $url
     * @param array $req
     * @return mixed|null
     * @throws ErrorException
     */
    final public function request(string $url, array $req = [])
    {
        $time = time();
        $urls = parse_url($url);
        $url_path = $urls['scheme'] . '://' . $urls['host'] . $urls['path'];

        $reqData = $this->encryptRequest($time, $url_path, $req);
        $reqJson = json_encode($reqData);
        $signature = $this->getSignature($time, $url_path, $reqJson);

        $_header = [
            "Content-Type:application/json;charset=utf-8",
            "Accept:application/json",
            'Wechatmp-Appid:' . $this->getConfig()->getAppid(),
            'Wechatmp-TimeStamp:' . $time,
            'Wechatmp-Signature:' . $signature,
        ];
        [$body, $header] = $this->curlPost($url, $reqJson, $_header);
        $headers = $this->parseHeaders($header);
        $result = json_decode($body, true);
        if (isset($result['errcode'])) {
            throw new ErrorException('请求微信平台报错[' . $result['errcode'] . '] ' . ($result['errmsg'] ?? ''));
        }

        $success = $this->verifySignature($url_path, $body, $headers);
        if (!$success) {
            throw new LogicException("使用微信平台证书验签响应参数（公钥验签）失败");
        }

        $response = $this->decryptToString($url_path, $headers['Wechatmp-TimeStamp'], $result);
        if (empty($response)) {
            throw new ErrorException('请求微信平台报错：响应为空');
        }

        $this->throwErrorException($response);
        return $response;
    }

    /**
     * 检查响应，如果失败则抛出异常
     * @param array $response
     * @return void
     * @throws ErrorException
     */
    protected function throwErrorException(array $response): void
    {
        if (isset($response['errcode']) && $response['errcode'] != 0) {
            $errmsg = '请求微信平台报错[' . $response['errcode'] . '] ' . ($response['errmsg'] ?? '');
            throw new ErrorException($errmsg);
        }
    }

    /**
     * 对称密钥加密请求数据
     * @return void
     * @throws ErrorException
     */
    private function encryptRequest(int $time, string $url_path, array $req = []): array
    {
        $appId = $this->getConfig()->getAppid();

        try {
            //16位随机字符
            $nonce = rtrim(base64_encode(random_bytes(16)), '=');
            //12位随机字符
            $iv = random_bytes(12);
        } catch (Exception $e) {
            throw new ErrorException('对称密钥加密请求数据时异常：' . $e->getMessage());
        }

        $addReq = ['_n' => $nonce, '_appid' => $appId, '_timestamp' => $time];
        $realReq = array_merge($addReq, $req);
        $realReq = json_encode($realReq);
        //额外认证数据
        $aad = $url_path . '|' . $appId . '|' . $time . '|' . $this->getConfig()->getAesSn();

        $cipher = openssl_encrypt($realReq, "aes-256-gcm", base64_decode($this->getConfig()->getAesKey()), OPENSSL_RAW_DATA, $iv, $tag, $aad);
        return ["iv" => base64_encode($iv), "data" => base64_encode($cipher), "authtag" => base64_encode($tag)];
    }

    /**
     * 非对称私钥加签请求数据
     * @param int $time
     * @param string $url_path
     * @param string $reqJson
     * @return string
     */
    private function getSignature(int $time, string $url_path, string $reqJson): string
    {
        $payload = $url_path . "\n" . $this->getConfig()->getAppid() . "\n" . $time . "\n" . $reqJson;

        $rsa = RSA::loadPrivateKey($this->getConfig()->getRsaPrivateKey());
        $signature = $rsa->withPadding(RSA::SIGNATURE_PSS)
            ->withHash('sha256')
            ->withMGFHash('sha256')
            ->sign($payload);
        return base64_encode($signature);
    }

    /**
     * post请求
     * @param string $url
     * @param string $fields
     * @param array $headers
     * @return array
     */
    public function curlPost(string $url, string $fields, array $headers): array
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $fields);
        //输出响应头部
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        $str = curl_exec($curl);
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($str, 0, $headerSize);
        $body = substr($str, $headerSize);
        curl_close($curl);
        return [$body, $header];
    }

    /**
     * 非对称公钥验签响应数据
     * @description 从微信开放平台证书内提取公钥，公钥验签
     * @param string $url_path
     * @param string $body
     * @param array $headers
     * @return bool
     * @throws ErrorException
     */
    private function verifySignature(string $url_path, string $body, array $headers): bool
    {
        $reTime = $headers['Wechatmp-TimeStamp'];

        $appId = $this->getConfig()->getAppid();
        $cert_sn = $this->getConfig()->getCertSn();
        $cert_key = $this->getConfig()->getCertKey();

        if ($appId !== $headers['Wechatmp-Appid'] || time() - $reTime > 300) {
            throw new ErrorException('公钥验签：返回值安全字段校验失败');
        }
        if (isset($headers['Wechatmp-Serial']) && $cert_sn === $headers['Wechatmp-Serial']) {
            $signature = $headers['Wechatmp-Signature'];
        } elseif (isset($headers['Wechatmp-Serial-Deprecated']) && $cert_sn === $headers['Wechatmp-Serial-Deprecated']) {
            $signature = $headers['Wechatmp-Signature-Deprecated'];
        } else {
            throw new ErrorException('公钥验签：返回值sn不匹配');
        }

        $payload = $url_path . "\n" . $appId . "\n" . $reTime . "\n" . $body;
        $payload = utf8_encode($payload);
        $signature = base64_decode($signature);

        $pkey = openssl_pkey_get_public($cert_key);
        $keyData = openssl_pkey_get_details($pkey);

        $rsa = RSA::loadPublicKey($keyData['key']);
        return $rsa->withPadding(RSA::SIGNATURE_PSS)
            ->withHash('sha256')
            ->withMGFHash('sha256')
            ->verify($payload, $signature);
    }

    /**
     * 对称密钥解密响应数据
     * @param string $url_path
     * @param string $ts
     * @param array $body
     * @return mixed|null
     * @throws ErrorException
     */
    private function decryptToString(string $url_path, string $ts, array $body)
    {
        $appId = $this->getConfig()->getAppid();
        $sn = $this->getConfig()->getAesSn();
        $aad = $url_path . '|' . $appId . '|' . $ts . '|' . $sn;
        $iv = base64_decode($body['iv']);
        $data = base64_decode($body['data']);
        $authTag = base64_decode($body['authtag']);
        $result = openssl_decrypt($data, "aes-256-gcm", base64_decode($this->getConfig()->getAesKey()), OPENSSL_RAW_DATA, $iv, $authTag, $aad);
        if (!$result) {
            throw new ErrorException('对称密钥解密响应数据：加密字符串使用 aes-256-gcm 解析失败');
        }
        return json_decode($result, true);
    }

    /**
     * 解析响应头部信息
     * @param $headerString
     * @return array
     */
    private function parseHeaders($headerString): array
    {
        $headers = [];
        $lines = explode("\r\n", $headerString);
        foreach ($lines as $line) {
            $line = trim($line);
            if (!empty($line)) {
                $parts = explode(':', $line, 2);
                $key = trim($parts[0]);
                $value = isset($parts[1]) ? trim($parts[1]) : '';
                $headers[$key] = $value;
            }
        }
        return $headers;
    }

    /**
     * 过滤参数
     * @param array $data
     * @return array
     */
    final protected function filter(array $data): array
    {
        return array_filter($data, function ($v) {
            return null !== $v && '' !== $v;
        });
    }
}
