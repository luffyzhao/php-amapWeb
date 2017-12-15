<?php
namespace luffyzhao\amapWeb;

use Wenpeng\Curl\Curl;

/**
 *
 */
class BatchRequest
{
    // 网关
    private $_gateway = 'http://restapi.amap.com/v3/batch?';
    private $_url = [];
    private $_retry = 3;

    public function __construct(array $url = [])
    {
        $this->_url = $url;
    }

    public function solve()
    {
        $data = [];
        foreach ($this->_url as $value) {
            $data['ops'][]['url'] = $value;
        }
        $curl = Curl::init();
        $curl->set('CURLOPT_HTTPHEADER', ['Content-Type：application/json'])
            ->post(json_encode($data))->retry($this->_retry)
            ->url($this->_gateway);

        if ($curl->error()) {
            return false;
        } else {
            return json_decode($curl->data(), true);
        }
    }

    public function add($url)
    {
        $this->_url[] = $url;
        return $this;
    }

    public function remove($url)
    {
        if (($key = array_search($url, $this->_url)) !== false) {
            unset($this->_url[$key]);
        }

        return $this;
    }
}
