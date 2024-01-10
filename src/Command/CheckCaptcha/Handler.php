<?php

declare(strict_types=1);

namespace Audetv\YandexSmartCaptcha\Command\CheckCaptcha;

use Audetv\YandexSmartCaptcha\Http\HttpRequest;

class Handler
{
    protected string $smartCaptchaServerKey;
    protected string $validationUrl;
    private HttpRequest $curlRequest;

    public function __construct(HttpRequest $curlRequest, string $smartCaptchaServerKey, string $validationUrl = "")
    {
        $this->curlRequest = $curlRequest;
        $this->smartCaptchaServerKey = $smartCaptchaServerKey;
        $this->validationUrl = $validationUrl === "" ? "https://smartcaptcha.yandexcloud.net/validate" : $validationUrl;
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

        $curl = $this->curlRequest;
        $curl->setOption(CURLOPT_URL, $url);
        $curl->setOption(CURLOPT_RETURNTRANSFER, true);
        $curl->setOption(CURLOPT_TIMEOUT, 1);

        $serverOutput = $curl->execute();
        $httpCode = $curl->getInfo(CURLINFO_HTTP_CODE);

        $curl->close();

        if ($httpCode !== 200) {
            return true;
        }

        $response = json_decode($serverOutput);
        return $response->status === "ok";
    }
}
