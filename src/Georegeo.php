<?php
namespace luffyzhao\amapWeb;

/**
 *
 */
class Georegeo
{
    // 原始数据
    private $_data = [];
    // 地址集合
    private $_address = [];
    // 高德地图Key
    private $_amapKey = '';
    // 网关
    private $_gateway = '/v3/geocode/geo?';
    // 查询的城市
    private $_city = '深圳市';
    // 配置
    private $_config = [
        'addressKey' => 'address',
        'locationKey' => 'location',
    ];

    /**
     * [__construct description]
     * @method   __construct
     * @DateTime 2017-12-15T12:02:34+0800
     * @param    [type]                   $data [description]
     */
    public function __construct($data, $config)
    {
        $this->_data = $data;
        $this->_config = array_merge($this->_config, $config);
    }
    /**
     * 解决
     * @method   solve
     * @DateTime 2017-12-15T11:58:56+0800
     * @return   [type]                   [description]
     */
    public function solve()
    {
        $this->_initialiseAddress();
        $this->_iterate();

        return $this;
    }
    /**
     * 初始
     * @method   _initialiseAddress
     * @DateTime 2017-12-15T12:03:56+0800
     * @return   [type]                   [description]
     */
    private function _initialiseAddress()
    {
        foreach ($this->_data as $key => $value) {
            $this->_address[$key] = new Address($value, $this->_config);
        }
    }

    private function _iterate()
    {
        $batchArray = array_chunk($this->_address, 10, true);
        $addressBatch = [];
        $addressArray = [];
        $addressKey = 0;
        $count = count($this->_address);

        while ($count > $addressKey) {
            $addressArray[$addressKey] = $this->_address[$addressKey]->getAddress();
            $addressKey++;
            // 每十条做分隔一次和最后剩下的
            if ($addressKey % 10 === 0 || $count === $addressKey) {
                $addressBatch[] = [
                    'keys' => array_keys($addressArray),
                    'url' => $this->_georegeo($addressArray),
                ];
                $addressArray = [];
            }
        }

        $key = 0;
        $batchs = [];
        $count = count($addressBatch);
        while ($count > $key) {
            $batchs[] = $addressBatch[$key];
            $key++;
            if ($key % 20 === 0 || $count === $key) {
                $this->_batchrequest($batchs);
                $batchs = [];
            }
        }
    }
    /**
     * 批量请求接口
     * @method   _batchrequest
     * @DateTime 2017-12-15T13:50:18+0800
     * @return   [type]                   [description]
     */
    private function _batchrequest(array $batchs)
    {
        $data = [];
        $keys = [];
        $batch = new BatchRequest;
        foreach ($batchs as $value) {
            $batch->add($value['url']);
        }
        if (($content = $batch->solve()) != false && !(isset($content['status']) && $content['status'] == 0)) {
            foreach ($content as $index => $geores) {
                if ($geores['status'] === 200) {
                    foreach ($geores['body']['geocodes'] as $key => $value) {
                        if(!empty($value['location'])){
                            $this->_address[$batchs[$index]['keys'][$key]]->setLocation(explode(',', $value['location']));
                        }else{
                            $this->_address[$batchs[$index]['keys'][$key]]->setLocation([0,0]);
                        }
                    }
                }
            }
        }
    }
    /**
     * 获取地理编码 API 服务地址
     * @method   getGeoregeoUrl
     * @DateTime 2017-12-15T13:45:41+0800
     * @param    array                    $array [description]
     * @return   [type]                          [description]
     */
    private function _georegeo(array $array)
    {
        return $this->_gateway .
        'address=' . implode('|', $array) .
        '&key=' . $this->_amapKey .
        '&batch=true' .
        '&city=' . $this->_city;
    }
    /**
     * 设置高德地图Key
     * @method   setAmapKey
     * @DateTime 2017-12-15T12:00:18+0800
     * @param    [type]                   $key [description]
     */
    public function setAmapKey($key)
    {
        $this->_amapKey = $key;
        return $this;
    }

    /**
     * 设置查询的地市
     * @method   setAmapKey
     * @DateTime 2017-12-15T12:00:18+0800
     * @param    [type]                   $key [description]
     */
    public function setCity($key)
    {
        $this->_city = $key;
        return $this;
    }

    public function toArray()
    {
        $data = [];
        foreach ($this->_address as $key => $value) {
            $data[$key] = $value->toArray();
        }
        return $data;
    }
}
