<?php

declare(strict_types=1);

namespace Audetv\YandexSmartCaptcha\Test\Unit;

use Audetv\YandexSmartCaptcha\Command\CheckCaptcha\Command;
use Audetv\YandexSmartCaptcha\Command\CheckCaptcha\Handler;
use Audetv\YandexSmartCaptcha\Http\CurlRequest;
use PHPUnit\Framework\TestCase;

class CheckCaptchaTest extends TestCase
{
    public function testHandleValidCommand()
    {
        // Arrange
        $command = new Command();
        $command->token = "valid_token";
        $command->ip = "127.0.0.1";

        $mockCurlRequest = $this->createMock(CurlRequest::class);
        $mockCurlRequest->expects($this->any())
            ->method('setOption')
            ->withConsecutive(
                [$this->equalTo(CURLOPT_URL), $this->equalTo("validation_url?secret=smartcaptcha_server_key&token=valid_token&ip=127.0.0.1")],
                [$this->equalTo(CURLOPT_RETURNTRANSFER), $this->equalTo(true)],
                [$this->equalTo(CURLOPT_TIMEOUT), $this->equalTo(1)]
            );
        $mockCurlRequest->expects($this->once())
            ->method('execute')
            ->willReturn('{"status": "ok"}');
        $mockCurlRequest->expects($this->once())
            ->method('getInfo')
            ->with($this->equalTo(CURLINFO_HTTP_CODE))
            ->willReturn(200);
        $mockCurlRequest->expects($this->once())
            ->method('close');

        $handler = new Handler($mockCurlRequest, "smartcaptcha_server_key", "validation_url");

        // Act
        $result = $handler->handle($command);

        // Assert
        $this->assertTrue($result);
    }

    public function testHandleInvalidCommand()
    {
        // Arrange
        $command = new Command();
        $command->token = "invalid_token";
        $command->ip = "127.0.0.1";

        $mockCurlRequest = $this->createMock(CurlRequest::class);
        $mockCurlRequest->expects($this->any())
            ->method('setOption')
            ->withConsecutive(
                [$this->equalTo(CURLOPT_URL), $this->equalTo("validation_url?secret=smartcaptcha_server_key&token=invalid_token&ip=127.0.0.1")],
                [$this->equalTo(CURLOPT_RETURNTRANSFER), $this->equalTo(true)],
                [$this->equalTo(CURLOPT_TIMEOUT), $this->equalTo(1)]
            );
        $mockCurlRequest->expects($this->once())
            ->method('execute')
            ->willReturn('{"status": "invalid"}');
        $mockCurlRequest->expects($this->once())
            ->method('getInfo')
            ->with($this->equalTo(CURLINFO_HTTP_CODE))
            ->willReturn(200);
        $mockCurlRequest->expects($this->once())
            ->method('close');

        $handler = new Handler($mockCurlRequest, "smartcaptcha_server_key", "validation_url");

        // Act
        $result = $handler->handle($command);

        // Assert
        $this->assertFalse($result);
    }
}
