<?php

declare(strict_types=1);

namespace Audetv\YandexSmartCaptcha\Command\CheckCaptcha;

use Audetv\YandexSmartCaptcha\Http\CurlRequest;
use Audetv\YandexSmartCaptcha\Http\HttpRequest;

class Handler
{
    protected string $smartCaptchaServerKey;
    protected string $validationUrl;
    private CurlRequest $curl;

    public function __construct(string $smartCaptchaServerKey, string $validationUrl = "")
    {
        $this->smartCaptchaServerKey = $smartCaptchaServerKey;
        $this->validationUrl = $validationUrl === "" ? "https://smartcaptcha.yandexcloud.net/validate" : $validationUrl;
        $this->setCurl(new CurlRequest());
    }

    /**
     * Handles the command.
     *
     * @param Command $command The command to be handled.
     * @return bool Returns true if the command was handled successfully, false otherwise.
     */
    public function handle(Command $command): bool
    {
        $queryParams = http_build_query(
            [
                "secret" => $this->smartCaptchaServerKey,
                "token" => $command->token,
                "ip" => $command->ip
            ]);

        $url = "{$this->validationUrl}?$queryParams";

        $curl = new CurlRequest();

        $this->curl->setOption(CURLOPT_URL, $url);
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->curl->setOption(CURLOPT_TIMEOUT, 1);

        $serverOutput = $this->curl->execute();
        $httpCode = $this->curl->getInfo(CURLINFO_HTTP_CODE);

        $curl->close();

        if ($httpCode !== 200) {
            return true;
        }

        $response = json_decode($serverOutput);
        return $response->status === "ok";
    }

    public function setCurl(HttpRequest $curl): void
    {
        $this->curl = $curl;
    }
}
