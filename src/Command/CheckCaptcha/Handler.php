<?php

declare(strict_types=1);

namespace Audetv\YandexSmartCaptcha\Command\CheckCaptcha;

use Audetv\YandexSmartCaptcha\Http\HttpRequest;

class Handler
{
    // const SMARTCAPTCHA_SERVER_KEY = "SMARTCAPTCHA_SERVER_KEY";
    private string $smartcaptcha_server_key;
    private string $validation_url;
    private HttpRequest $curlRequest;

    public function __construct(
        HttpRequest $curlRequest,
        string $smartcaptcha_server_key,
        string $validation_url = ""
    ) {
        $this->smartcaptcha_server_key = $smartcaptcha_server_key;
        if ($validation_url === "") {
            $this->validation_url = "https://smartcaptcha.yandexcloud.net/validate";
        } else {
            $this->validation_url = $validation_url;
        }
        $this->curlRequest = $curlRequest;
    }

    /**
     * Handles the given command.
     *
     * @param Command $command The command to handle.
     * @return bool Whether the handling was successful.
     */
    public function handle(Command $command): bool
    {
        $args = http_build_query([
                                     "secret" => $this->smartcaptcha_server_key,
                                     "token" => $command->token,
                                     "ip" => $command->ip
        ]);

        $url = "{$this->validation_url}?$args";

        $ch = $this->curlRequest;
        $ch->setOption(CURLOPT_URL, $url);
        $ch->setOption(CURLOPT_RETURNTRANSFER, true);
        $ch->setOption(CURLOPT_TIMEOUT, 1);
        $server_output = $ch->execute();
        $httpcode = $ch->getInfo(CURLINFO_HTTP_CODE);
        $ch->close();


        if ($httpcode !== 200) {
            echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
            return true;
        }

        $resp = json_decode($server_output);
        return $resp->status === "ok";
    }
}
