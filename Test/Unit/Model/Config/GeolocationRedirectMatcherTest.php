<?php

namespace Fastly\Cdn\Test\Unit\Model\Config;

use Fastly\Cdn\Model\Config\GeolocationRedirectMatcher;
use PHPUnit\Framework\TestCase;

class GeolocationRedirectMatcherTest extends TestCase
{
    private GeolocationRedirectMatcher $matcher;

    public function setUp(): void
    {
        $this->matcher = new GeolocationRedirectMatcher();
    }

    public function testWithOldEntries(): void
    {
        $map = [
            [
                'country_id' => 'HR',
                'store_id' => '5'
            ],
        ];

        $this->assertEquals(5, $this->matcher->execute($map, 'HR', 42));
    }

    public function testWithWildcardCountry(): void
    {
        $map = [
            [
                'country_id' => '*',
                'store_id' => '6'
            ],
        ];

        $this->assertEquals(6, $this->matcher->execute($map, 'US', 42));
    }

    public function testWithSpecificWebsite(): void
    {
        $map = [
            [
                'country_id' => 'US',
                'origin_website_id' => '5',
                'store_id' => '123'
            ],
            [
                'country_id' => 'US',
                'origin_website_id' => '2',
                'store_id' => '321'
            ],
        ];

        $this->assertEquals(321, $this->matcher->execute($map, 'US', 2));
        $this->assertEquals(123, $this->matcher->execute($map, 'US', 5));
    }

    public function testWithVariousSpecificity(): void
    {
        $map = [
            [
                'country_id' => '*',
                'origin_website_id' => '',
                'store_id' => '111'
            ],
            [
                'country_id' => 'US',
                'origin_website_id' => '',
                'store_id' => '222'
            ],
            [
                'country_id' => 'US',
                'origin_website_id' => '33',
                'store_id' => '333'
            ],
        ];

        $this->assertEquals(111, $this->matcher->execute($map, 'DE', 123));
        $this->assertEquals(222, $this->matcher->execute($map, 'US', 123));
        $this->assertEquals(333, $this->matcher->execute($map, 'US', 33));
    }

    public function testWithVariousCountrySpecificity(): void
    {
        $map = [
            [
                'country_id' => '*',
                'origin_website_id' => '1',
                'store_id' => '1'
            ],
            [
                'country_id' => 'US',
                'origin_website_id' => '1',
                'store_id' => '2'
            ],
        ];

        $this->assertEquals(1, $this->matcher->execute($map, 'DE', 1));
        $this->assertEquals(2, $this->matcher->execute($map, 'US', 1));
    }
}
