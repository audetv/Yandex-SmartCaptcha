<?php

declare(strict_types=1);

namespace Audetv\YandexSmartCaptcha\Command\CheckCaptcha;

class Handler
{
    public function handle(Command $command): bool
    {
        $token = $command->token;
        $ip = $command->ip;

        $args = http_build_query([
             "secret" => SMARTCAPTCHA_SERVER_KEY,
             "token" => $token,
             "ip" => $ip
        ]);

        $validateURL = "https://smartcaptcha.yandexcloud.net/validate";
        $url = "{$validateURL}?$args";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);

        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200) {
            echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
            return true;
        }

        $resp = json_decode($server_output);
        return $resp->status === "ok";
    }
}
