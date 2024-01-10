<?php

declare(strict_types=1);

namespace Audetv\YandexSmartCaptcha\Tests;

use Audetv\YandexSmartCaptcha\Command\CheckCaptcha\Command;
use Audetv\YandexSmartCaptcha\Command\CheckCaptcha\Handler;
use Audetv\YandexSmartCaptcha\Http\CurlRequest;
use PHPUnit\Framework\TestCase;

class CheckCaptchaTest extends TestCase
{
    public function testHandleValidToken()
    {
        $command = new Command();
        $command->token = "valid_token";
        $command->ip = "127.0.0.1";
        $curlRequestMock = $this->createMock(CurlRequest::class);
        $curlRequestMock->expects($this->once())
            ->method('execute')
            ->willReturn('{"status": "ok"}');
        $curlRequestMock->expects($this->once())
            ->method('getInfo')
            ->willReturn(200);
        $handler = new Handler($curlRequestMock, 'smartcaptcha_server_key');
        $result = $handler->handle($command);
        $this->assertTrue($result);
    }

    public function testHandleInvalidToken()
    {
        $command = new Command();
        $command->token = "invalid_token";
        $command->ip = "127.0.0.1";
        $curlRequestMock = $this->createMock(CurlRequest::class);
        $curlRequestMock->expects($this->once())
            ->method('execute')
            ->willReturn('{"status":"failed","message":"Invalid or expired Token."}');
        $curlRequestMock->expects($this->once())
            ->method('getInfo')
            ->willReturn(200);
        $handler = new Handler($curlRequestMock, 'smartcaptcha_server_key');
        $result = $handler->handle($command);
        $this->assertFalse($result);
    }

    public function testHandleInvalidServerKey()
    {
        $command = new Command();
        $command->token = "valid_token";
        $command->ip = "127.0.0.1";
        $curlRequestMock = $this->createMock(CurlRequest::class);
        $curlRequestMock->expects($this->once())
            ->method('execute')
            ->willReturn('{"status":"failed","message":"Authentication failed. Invalid secret."}');
        $curlRequestMock->expects($this->once())
            ->method('getInfo')
            ->willReturn(403);
        $handler = new Handler($curlRequestMock, 'invalid_smartcaptcha_server_key');
        $result = $handler->handle($command);
        $this->assertTrue($result);
    }
}
