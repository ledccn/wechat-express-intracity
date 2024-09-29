<?php

namespace Ledc\IntraCity;

use ErrorException;
use InvalidArgumentException;
use Ledc\IntraCity\Contracts\OrderPayload;
use Ledc\IntraCity\Contracts\OrderResponse;
use Ledc\IntraCity\Contracts\PreviewOrderPayload;
use Ledc\IntraCity\Contracts\PreviewOrderResponse;

/**
 * 同城配送
 * @link https://developers.weixin.qq.com/miniprogram/dev/platform-capabilities/industry/express/business/intracity_service.html
 */
class ExpressApi extends Wechat
{
    /**
     * API前缀
     */
    const API_PREFIX = "https://api.weixin.qq.com/cgi-bin/express/intracity";

    /**
     * 构造请求URL
     * @param string $url
     * @return string
     */
    protected function builderUrl(string $url): string
    {
        return self::API_PREFIX . $url . '?access_token=' . $this->getConfig()->getAccessToken();
    }

    /**
     * 开通门店权限（无加密，可直接调用）
     * @description 在使用门店相关接口前需要先开通门店权限，开通门店权限前可以使用页面或者使用下面的API开通门店。
     * @return array
     */
    public function apply(): array
    {
        return $this->curlPost($this->builderUrl('/apply'), "", []);
    }

    /**
     * 创建门店
     * @description 创建门店时需要传入自定义的门店编号，自定义的门店编号需要唯一，确保不重复创建 门店创建后系统生成全局唯一门店编号wx_store_id，后续创建运力订单时需要该门店编号
     * @param array $params
     * @return array
     * @throws ErrorException
     */
    public function createStore(array $params): array
    {
        return $this->request($this->builderUrl('/createstore'), $params);
    }

    /**
     * 查询门店
     * @description 获取本小程序所创建的门店，不传wx_store_id和out_store_id则返回本小程序所有门店信息
     * @param string $wx_store_id 微信门店编号
     * @param string $out_store_id 自定义门店编号
     * @return array
     * @throws ErrorException
     */
    public function queryStore(string $wx_store_id = '', string $out_store_id = ''): array
    {
        return $this->request($this->builderUrl('/querystore'), $this->filter(compact('wx_store_id', 'out_store_id')));
    }

    /**
     * 更新门店
     * @param array $keys
     * @param array $content
     * @return array
     * @throws ErrorException
     */
    public function updateStore(array $keys, array $content = []): array
    {
        $keys = $this->filter($keys);
        $content = $this->filter($content);
        return $this->request($this->builderUrl('/updatestore'), compact('keys', 'content'));
    }

    /**
     * 门店运费充值
     * @description 返回微信服务市场的充值页面地址，通过该页面可以为门店充值指定运力的运费，充值后运费有使用有效期，默认有效期为1个月，超期未使用的运费降原路退回
     * @param string $wx_store_id 微信门店编号（pay_mode = PAY_MODE_STORE时必传，不传pay_mode时必传wx_store_id）
     * @param string $service_trans_id 运力ID
     * @param int $amount 充值金额（单位：分, 50元起充）
     * @param string $pay_mode 充值主体（门店：PAY_MODE_STORE；小程序:PAY_MODE_APP；服务商：PAY_MODE_COMPONENT，不传pay_mode默认pay_mode=PAY_MODE_STORE）
     * @return array
     * @throws ErrorException
     */
    public function storeCharge(string $wx_store_id, string $service_trans_id, int $amount, string $pay_mode = 'PAY_MODE_STORE'): array
    {
        $params = compact('wx_store_id', 'service_trans_id', 'amount', 'pay_mode');
        return $this->request($this->builderUrl('/storecharge'), $this->filter($params));
    }

    /**
     * 门店运费退款
     * @description 该接口可以将门店指定运力的运费余额退还，如果门店有在途的配送订单，需要等配送完成或者取消配送订单之后才可以操作退款；操作退款后，退款金额五分钟内到账。
     * @param string $wx_store_id 微信门店编号（pay_mode = PAY_MODE_STORE时必传，不传pay_mode时必传wx_store_id）
     * @param string $service_trans_id 运力ID
     * @param string $pay_mode 充值/扣费主体（门店：PAY_MODE_STORE；小程序:PAY_MODE_APP；服务商：PAY_MODE_COMPONENT，不传pay_mode默认pay_mode=PAY_MODE_STORE）
     * @return array
     * @throws ErrorException
     */
    public function storeRefund(string $wx_store_id, string $service_trans_id, string $pay_mode = 'PAY_MODE_STORE'): array
    {
        $params = compact('wx_store_id', 'service_trans_id', 'pay_mode');
        return $this->request($this->builderUrl('/storerefund'), $this->filter($params));
    }

    /**
     * 门店运费流水查询
     * @description 查询门店运力资金流水
     * @param string $wx_store_id 微信门店编号
     * @param int $flow_type 流水类型（1:充值流水， 2:消费流水，3:退款流水）
     * @param string $service_trans_id 运力ID
     * @param int|null $begin_time 开始时间戳（不传默认返回最近90天的数据）
     * @param int|null $end_time 结束时间戳（不传默认返回最近90天的数据）
     * @return array
     * @throws ErrorException
     */
    public function queryFlow(string $wx_store_id, int $flow_type = 1, string $service_trans_id = '', ?int $begin_time = null, ?int $end_time = null): array
    {
        $params = compact('wx_store_id', 'flow_type', 'service_trans_id', 'begin_time', 'end_time');
        return $this->request($this->builderUrl('/queryflow'), $this->filter($params));
    }

    /**
     * 门店余额查询
     * @description 查询门店运力余额
     * @param string $wx_store_id 微信门店编号（pay_mode = PAY_MODE_STORE时必传，不传pay_mode时必传wx_store_id）
     * @param string $service_trans_id 运力ID（查询门店在指定运力ID充值的余额，不指定则查询全部）
     * @param string $pay_mode 充值/扣费主体（门店：PAY_MODE_STORE；小程序:PAY_MODE_APP；服务商：PAY_MODE_COMPONENT，不传pay_mode默认pay_mode=PAY_MODE_STORE）
     * @return array
     * @throws ErrorException
     */
    public function balanceQuery(string $wx_store_id, string $service_trans_id, string $pay_mode = 'PAY_MODE_STORE'): array
    {
        $params = compact('wx_store_id', 'service_trans_id', 'pay_mode');
        return $this->request($this->builderUrl('/balancequery'), $this->filter($params));
    }

    /**
     * 查询运费
     * @description 商家通过该接口传入配送单相关信息，同城配送后台将根据配送信息向运力查询并返回实时的运费和配送距离。
     * @description 同城配送会根据门店设置的运力偏好（价格优先/运力优先）进行预下单，如果没有设置偏好，则默认返回低价运力的预下单结果，商家可以根据该接口的返回价格作为配送的预估价格。
     * @tips （注意：接口返回实时计价结果，可能存在预下单和下单价格不一致的情况，具体费用应以下单接口为准。以达达为例，询价结果一般3分钟内有效，顺丰平台没有做说明）
     * @param PreviewOrderPayload $payload
     * @return PreviewOrderResponse
     * @throws ErrorException
     */
    public function previewAddOrder(PreviewOrderPayload $payload): PreviewOrderResponse
    {
        $response = $this->request($this->builderUrl('/preaddorder'), $payload->jsonSerialize());
        return new PreviewOrderResponse($response);
    }

    /**
     * 创建配送单
     * @description 创建同城配送单，会根据门店设置的运力偏好来选择运力公司下单。如果没有设置偏好，则默认优先下单低价运力。
     * @param OrderPayload $payload
     * @return OrderResponse
     * @throws ErrorException
     */
    public function addOrder(OrderPayload $payload): OrderResponse
    {
        $response = $this->request($this->builderUrl('/addorder'), $payload->jsonSerialize());
        return new OrderResponse($response);
    }

    /**
     * 查询配送单
     * @description 通过该接口查询订单是否创建成功，以及订单创建后的状态更新
     * @param array|string $params 必须条件（wx_store_id和store_order_id需要成对出现；可以单独使用wx_order_id取消订单）
     * @return array
     * @throws ErrorException
     */
    public function queryOrder($params): array
    {
        if (is_string($params)) {
            if (empty($params)) {
                throw new InvalidArgumentException('微信订单号为空');
            }
            $params = ['wx_order_id' => $params];
        } elseif (is_array($params)) {
            if (empty($params['wx_store_id'])) {
                throw new InvalidArgumentException('微信门店编号为空');
            }
            if (empty($params['store_order_id'])) {
                throw new InvalidArgumentException('门店订单号为空');
            }
        } else {
            throw new InvalidArgumentException('查询配送单，缺少入口参数');
        }

        //TODO... 返回响应对象
        return $this->request($this->builderUrl('/queryorder'), $params);
    }

    /**
     * 取消配送单
     * @description 通过该接口可以取消已创建的订单，取消配送中的订单需要扣减违约金。顺丰配送员接单后2分钟取消订单，收取¥2违约金；达达配送员接单后1分钟取消订单，收取¥2违约金。
     * @param array|string $condition 必须条件（wx_store_id和store_order_id需要成对出现；可以单独使用wx_order_id取消订单）
     * @param int $cancel_reason_id 取消原因（1:不需要了、2：信息填错、3：无人接单、99：其他）
     * @param string $cancel_reason 取消原因描述
     * @return array
     * @throws ErrorException
     */
    public function cancelOrder($condition, int $cancel_reason_id, string $cancel_reason = ''): array
    {
        if (is_string($condition)) {
            if (empty($condition)) {
                throw new InvalidArgumentException('微信订单号为空');
            }
            $params = ['wx_order_id' => $condition];
        } elseif (is_array($condition)) {
            if (empty($condition['wx_store_id'])) {
                throw new InvalidArgumentException('微信门店编号为空');
            }
            if (empty($condition['store_order_id'])) {
                throw new InvalidArgumentException('门店订单号为空');
            }
            $params = $condition;
        } else {
            throw new InvalidArgumentException('查询配送单，缺少入口参数');
        }

        $params['cancel_reason_id'] = $cancel_reason_id;
        $params['cancel_reason'] = $cancel_reason;

        //TODO... 返回响应对象
        return $this->request($this->builderUrl('/cancelorder'), $this->filter($params));
    }

    /**
     * 模拟回调接口
     * @description 由于测试订单没有快递员接单，不会自动推送订单状态变化回调，为了方便开发者在沙箱环境联调，我们提供了模拟回调接口，针对测试订单开发者可以通过该接口触发3.1的回调。
     * @param array|string $condition 必须条件（wx_store_id和store_order_id需要成对出现；可以单独使用wx_order_id取消订单）
     * @param int $order_status 订单状态（详见订单状态列表）
     * @return array
     * @throws ErrorException
     */
    public function mockNotify($condition, int $order_status): array
    {
        if (is_string($condition)) {
            if (empty($condition)) {
                throw new InvalidArgumentException('微信订单号为空');
            }
            $params = ['wx_order_id' => $condition];
        } elseif (is_array($condition)) {
            if (empty($condition['wx_store_id'])) {
                throw new InvalidArgumentException('微信门店编号为空');
            }
            if (empty($condition['store_order_id'])) {
                throw new InvalidArgumentException('门店订单号为空');
            }
            $params = $condition;
        } else {
            throw new InvalidArgumentException('模拟回调接口，缺少入口参数');
        }

        $params['order_status'] = $order_status;

        //TODO... 返回响应对象
        return $this->request($this->builderUrl('/mocknotify'), $this->filter($params));
    }
}
