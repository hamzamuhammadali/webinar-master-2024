<?php

defined( 'ABSPATH' ) || exit; 

class CurlHelper {

    /*
     * @param $args e.g. ['url' => 'https://example.com', 'method' => 'get', 'data' => [], 'response_is_json' => false]
     * */
    private function curl($args)
    {
        $default_args = [
            'url' => '',
            'method' => '',
            'data' => [],
            'response_is_json' => true,
        ];

        $args = array_merge($default_args, $args);
        $url = $args['url'];

        //Create request data string
        $data = http_build_query($args['data']);

        //Execute cURL request
        $ch = curl_init();

        if ($args['method'] === 'post') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        } else {
            $url = $url . "?" . $data;
        }
        curl_setopt($ch, CURLOPT_URL, $url);

        // make sure curl returns a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($ch);
        curl_close($ch);

        return $args['response_is_json'] ? json_decode($output) : $output;
    }

    public function get($url, $response_is_json=true)
    {
        $args = [
            'url' => $url,
            'method' => 'get',
            'response_is_json' => $response_is_json,
        ];
        return $this->curl($args);
    }

    public function post($url, $data, $response_is_json=true)
    {
        $args = [
            'url' => $url,
            'method' => 'get',
            'data' => $data,
            'response_is_json' => $response_is_json,
        ];
        return $this->curl($args);
    }

}
