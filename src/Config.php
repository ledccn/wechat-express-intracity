<?php

namespace Ledc\IntraCity;

use Closure;
use JsonSerializable;

/**
 * 同城配送的配置
 */
class Config implements JsonSerializable
{
    /**
     * 同城配送全局开关
     * @var bool
     */
    protected bool $enable = false;
    /**
     * 启用沙箱环境
     * @var bool
     */
    protected bool $use_sandbox = false;
    /**
     * 小程序appid
     * @var string
     */
    protected string $appid;
    /**
     * 小程序密钥
     * @var string
     */
    protected string $secret = '';
    /**
     * 小程序验证TOKEN
     * @var string
     */
    protected string $token = '';
    /**
     * 小程序接口调用凭据
     * @var Closure
     */
    protected Closure $access_token;
    /**
     * 对称密钥的编号
     * @var string
     */
    protected string $aes_sn;
    /**
     * 对称密钥
     * @var string
     */
    protected string $aes_key;
    /**
     * 非对称密钥的编号
     * @var string
     */
    protected string $rsa_sn;
    /**
     * 非对称密钥的公钥
     * @var string
     */
    protected string $rsa_public_key;
    /**
     * 非对称密钥的私钥
     * @var string
     */
    protected string $rsa_private_key;
    /**
     * 开放平台证书的编号
     * @var string
     */
    protected string $cert_sn;
    /**
     * 开放平台证书
     * @var string
     */
    protected string $cert_key;
    /**
     * 配送单回调URL
     * @var string
     */
    protected string $callback_url = '';
    /**
     * 微信门店编号
     * @var string
     */
    protected string $wx_store_id = '';
    /**
     * 跳转商家订单页面路径
     * @var string
     */
    protected string $order_detail_path = '';

    /**
     * 构造函数
     * @param array $attributes
     */
    public function __construct(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            if (property_exists($this, $key) && !is_null($value)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * 获取：同城配送全局开关
     * @return bool
     */
    public function isEnable(): bool
    {
        return $this->enable;
    }

    /**
     * 获取：沙箱环境开关
     * @return bool
     */
    public function isUseSandbox(): bool
    {
        return $this->use_sandbox;
    }

    /**
     * 获取：小程序appid
     * @return string
     */
    public function getAppid(): string
    {
        return $this->appid;
    }

    /**
     * 获取：小程序密钥
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * 获取：小程序验证TOKEN
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * 获取：对称密钥的编号
     * @return string
     */
    public function getAesSn(): string
    {
        return $this->aes_sn;
    }

    /**
     * 获取：对称密钥
     * @return string
     */
    public function getAesKey(): string
    {
        return $this->aes_key;
    }

    /**
     * 获取：非对称密钥的编号
     * @return string
     */
    public function getRsaSn(): string
    {
        return $this->rsa_sn;
    }

    /**
     * 获取：非对称密钥的公钥
     * @return string
     */
    public function getRsaPublicKey(): string
    {
        return $this->rsa_public_key;
    }

    /**
     * 获取：非对称密钥的私钥
     * @return string
     */
    public function getRsaPrivateKey(): string
    {
        return $this->rsa_private_key;
    }

    /**
     * 获取：开放平台证书的编号
     * @return string
     */
    public function getCertSn(): string
    {
        return $this->cert_sn;
    }

    /**
     * 获取：开放平台证书
     * @return string
     */
    public function getCertKey(): string
    {
        return $this->cert_key;
    }

    /**
     * 获取：配送单回调URL
     * @return string
     */
    public function getCallbackUrl(): string
    {
        return $this->callback_url;
    }

    /**
     * 获取：微信门店编号
     * @return string
     */
    public function getWxStoreId(): string
    {
        return $this->wx_store_id;
    }

    /**
     * 获取：跳转商家订单页面路径
     * @return string
     */
    public function getOrderDetailPath(): string
    {
        return $this->order_detail_path;
    }

    /**
     * 获取：小程序接口调用凭据
     * @return string
     */
    public function getAccessToken(): string
    {
        return call_user_func($this->access_token, $this->getAppid());
    }

    /**
     * 设置：小程序接口调用凭据
     * @param Closure $access_token
     * @return Config
     */
    public function setAccessToken(Closure $access_token): self
    {
        $this->access_token = $access_token;
        return $this;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        $data = get_object_vars($this);
        unset($data['access_token']);
        return $data;
    }

    /**
     * 转数组
     * @return array
     */
    public function toArray(): array
    {
        return $this->jsonSerialize();
    }

    /**
     * 转字符串
     * @param int $options
     * @return string
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * 转字符串
     * @return string
     */
    public function __toString(): string
    {
        return $this->toJson();
    }
}
