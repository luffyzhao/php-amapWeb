<?php
namespace luffyzhao\amapWeb;

/**
 *
 */
class Address
{
    // 原始数据
    private $_data = [];
    // 地址Key
    private $_config = [
        'addressKey' => 'address',
        'locationKey' => 'location',
    ];

    public function __construct(array $data, array $config)
    {
        $this->_data = $data;
        $this->_config = array_merge($this->_config, $config);
    }
    /**
     * 获取地址
     * @method   getAddress
     * @DateTime 2017-12-15T12:15:14+0800
     * @return   [type]                   [description]
     */
    public function getAddress()
    {
        return str_replace(['|', ' '], '', $this->_data[$this->_config['addressKey']]);
    }
    /**
     * 设置位置
     * @method   setLocation
     * @DateTime 2017-12-15T12:16:34+0800
     * @param    [type]                   $location [description]
     */
    public function setLocation(array $location)
    {
        $this->_data[$this->_config['locationKey']] = $location;
    }

    public function toArray()
    {
        return $this->_data;
    }

    public function __toString()
    {
        return $this->getAddress();
    }
}
