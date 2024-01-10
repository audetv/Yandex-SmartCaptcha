<?php

declare(strict_types=1);

namespace Audetv\YandexSmartCaptcha\Http;

interface HttpRequest
{
    public function setOption($name, $value);
    public function execute();
    public function getInfo($name);
    public function close();
}
