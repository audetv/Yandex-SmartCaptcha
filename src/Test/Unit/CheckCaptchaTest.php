<?php

declare(strict_types=1);

namespace Audetv\YandexSmartCaptcha\Test\Unit;

use Audetv\YandexSmartCaptcha\Command\CheckCaptcha\Command;
use Audetv\YandexSmartCaptcha\Command\CheckCaptcha\Handler;
use PHPUnit\Framework\TestCase;

class CheckCaptchaTest extends TestCase
{
    public function testHandle()
    {
        // Case 1: Successful handling
        $command = new Command();
        $command->token = "validToken";
        $command->ip = "127.0.0.1";

        $handler = new Handler();

        $this->assertTrue($handler->handle($command));

        // Case 2: Handling with an invalid token
        $command->token = "invalidToken";
        $this->assertFalse($handler->handle($command));

        // Case 3: Handling with an invalid IP
        $command->token = "validToken";
        $command->ip = "invalidIP";
        $this->assertFalse($handler->handle($command));

        // Case 4: Handling with an HTTP error
        // Mock the curl_exec() function to return false
        $this->mockCurlExec(false);
        $command->token = "validToken";
        $command->ip = "127.0.0.1";
        $this->assertTrue($handler->handle($command));

        // Case 5: Handling with a non-200 HTTP code
        // Mock the curl_getinfo() function to return a non-200 code
        $this->mockCurlGetInfo(404);
        $command->token = "validToken";
        $command->ip = "127.0.0.1";
        $this->assertTrue($handler->handle($command));

        // Case 6: Handling with a non-"ok" status response
        // Mock the curl_exec() function to return a JSON response with status "error"
        $this->mockCurlExec('{"status": "error"}');
        $command->token = "validToken";
        $command->ip = "127.0.0.1";
        $this->assertFalse($handler->handle($command));
    }

    private function mockCurlExec(bool $returnValue) {
        // Mock the curl_exec() function to return the provided value
        $this->getMockBuilder(Handler::class)
            ->addMethods(['curl_exec'])
            ->getMock()
            ->method('curl_exec')
            ->willReturn($returnValue);
    }
}
