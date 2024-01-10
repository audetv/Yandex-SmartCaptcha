<?php

declare(strict_types=1);

namespace Audetv\YandexSmartCaptcha\Test\Unit;

use Audetv\YandexSmartCaptcha\Http\CurlRequest;
use PHPUnit\Framework\TestCase;

const CURLINFO_RETURNTRANSFER = 19913;

class CurlRequestTest extends TestCase
{
    public function testSetOption()
    {

        $ch = new CurlRequest();
        // Test setting a valid option
        $ch->setOption(CURLOPT_URL, 'https://example.com');
        $this->assertEquals('https://example.com', curl_getinfo($ch->handle, CURLINFO_EFFECTIVE_URL));

        // Test setting an invalid option
//        $ch->setOption(9999999, 'value');
//        $this->assertFalse(curl_getinfo($ch->handle, CURLINFO_EFFECTIVE_URL));

        // Test setting a boolean option
        $ch->setOption(CURLOPT_RETURNTRANSFER, true);
        $this->assertTrue(curl_getinfo($ch->handle, CURLINFO_RETURNTRANSFER));
    }
}
