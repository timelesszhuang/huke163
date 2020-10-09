<?php

namespace qiangbi\huke163;


class HuKe163
{
    private $appkey;
    private $appSecret;
    private $baseUrl = 'https://huke.163.com/openapi';

    /**
     * HuKe163 constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $config_env = config('huke');
        $this->appkey = $config['appkey'] ?? $config_env['appkey'];
        $this->appSecret = $config['appSecret'] ?? $config_env['appSecret'];
    }

    public function __call(string $name, array $arguments)
    {
        $urlArray = explode('_', $name);
        $url = implode('/', $urlArray);
        return $this->post($url, $arguments[0] ?? []);
    }

    /**
     * @param string $url
     * @param array $post
     * @return mixed
     */
    private function post(string $url, array $post)
    {
        $appkey = $this->appkey;
        $appSecret = $this->appSecret;
        $appSecret = substr(openssl_digest(openssl_digest($appSecret, 'sha1', true), 'sha1', true), 0, 16);
        $post = json_encode($post);
        $time = time();
        $ur_sign = $post ? md5($post) : '';
        $ur_checksum = openssl_encrypt($appkey . $ur_sign . $time, 'AES-128-ECB', $appSecret, OPENSSL_RAW_DATA);
        $ur_checksum = bin2hex($ur_checksum);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->baseUrl . $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_POST, 1);//设置为POST方式
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);//POST数据
        curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json;', 'ur-appkey: ' . $appkey, 'ur-sign: ' . $ur_sign, 'ur-curtime: ' . $time, 'ur-checksum: ' . $ur_checksum, 'eid: 433'));
        $data = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($code == 200) {
            return $data;
        }
        return $code;
    }
}