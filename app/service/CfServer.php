<?php

namespace app\service;

class CfServer
{
    /**
     * @var object 对象实例
     */
    protected static $instance;

    private static $headers = [];

    /**
     * 初始化
     * @access public
     * @return CfServer|object
     */
    public static function instance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new static();
        }

        self::$headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . env('cf.auth_key'),
        ];

        return self::$instance;
    }

    public function addZone($zone)
    {
        $result = curl_http(
            "https://api.cloudflare.com/client/v4/zones",
            'POST',
            [
                'type' => 'full',
                'name' => $zone,
                'account' => [
                    'id' => env('cf.account_id')
                ]
            ],
            self::$headers
        );
        return json_decode($result, true);
    }

    public function delZone($zoneIdentifier)
    {
        $result = curl_http("https://api.cloudflare.com/client/v4/zones/{$zoneIdentifier}", 'DELETE', [], self::$headers);
        return json_decode($result, true);
    }


    public function addDns($zoneIdentifier, $dns)
    {
        $result = curl_http(
            "https://api.cloudflare.com/client/v4/zones/{$zoneIdentifier}/dns_records",
            'POST',
            [
                'type' => empty($dns['type']) ? 'A' : $dns['type'],
                'name' => $dns['name'],
                'content' => $dns['content'],
                'comment' => $dns['comment'],
                'proxied' => true,
            ],
            self::$headers
        );
        return json_decode($result, true);
    }

    public function editDns($zoneIdentifier, $identifier, $dns)
    {
        $result = curl_http(
            "https://api.cloudflare.com/client/v4/zones/{$zoneIdentifier}/dns_records/{$identifier}",
            'PUT',
            [
                'type' => empty($dns['type']) ? 'A' : $dns['type'],
                'name' => $dns['name'],
                'content' => $dns['content'],
                'comment' => $dns['comment'],
                'proxied' => true,
            ],
            self::$headers
        );
        return json_decode($result, true);
    }

    public function delDns($zoneIdentifier, $identifier)
    {
        $result = curl_http("https://api.cloudflare.com/client/v4/zones/{$zoneIdentifier}/dns_records/{$identifier}", 'DELETE', [], self::$headers);
        return json_decode($result, true);
    }

}