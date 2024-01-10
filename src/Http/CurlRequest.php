<?php

declare(strict_types=1);

namespace Audetv\YandexSmartCaptcha\Http;

use Audetv\YandexSmartCaptcha;

class CurlRequest implements HttpRequest
{
    public $handle = null;

    public function __construct() {
        $this->handle = curl_init();
    }

    public function setOption($name, $value) {
        curl_setopt($this->handle, $name, $value);
    }

    public function execute() {
        return curl_exec($this->handle);
    }

    public function getInfo($name) {
        return curl_getinfo($this->handle, $name);
    }

    public function close() {
        curl_close($this->handle);
    }
}
