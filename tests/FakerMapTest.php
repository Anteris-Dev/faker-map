<?php

namespace Anteris\Tests\FakerMap;

use Anteris\FakerMap\DataTransferObjects\FakerMapData;
use Anteris\FakerMap\DataTransferObjects\FakerMethod;
use Anteris\FakerMap\FakerMap;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Anteris\FakerMap\FakerMap
 * @covers \Anteris\FakerMap\FakerDictionary
 * @covers \Anteris\FakerMap\DataTransferObjects\FakerMapData
 * @covers \Anteris\FakerMap\DataTransferObjects\FakerMethod
 */
class FakerMapTest extends TestCase
{
    public function test_it_can_boot_faker_methods()
    {
        $map        = new FakerMap;
        $methods    = $map->options();

        $this->assertIsArray($methods);
        $this->assertContains('firstName', $methods);
    }

    public function test_it_will_fallback_to_dictionary()
    {
        $city = (new FakerMap)->closest('homeCity');

        $this->assertInstanceOf(FakerMapData::class, $city);
        $this->assertInstanceOf(FakerMethod::class, $city->method);
        $this->assertNull($city->letterDisparity);
        $this->assertNull($city->percentageSimilar);
    }

    public function test_it_will_guess_if_not_found()
    {
        $color = (new FakerMap)->closest('color');

        $this->assertInstanceOf(FakerMapData::class, $color);
        $this->assertInstanceOf(FakerMethod::class, $color->method);
        $this->assertIsInt($color->letterDisparity);
        $this->assertIsFloat($color->percentageSimilar);
    }

    public function test_it_will_match_exactly()
    {
        $color = (new FakerMap)->closest('hslColor');

        $this->assertInstanceOf(FakerMapData::class, $color);
        $this->assertInstanceOf(FakerMethod::class, $color->method);
        $this->assertIsInt($color->letterDisparity);
        $this->assertIsFloat($color->percentageSimilar);
    }

    public function test_it_will_return_null_if_not_found()
    {
        $color = (new FakerMap)->closest('hooha!');

        $this->assertNull($color);
    }

    public function test_calling_fake_will_execute_faker()
    {
        $color = (new FakerMap)->closest('hslColor');
        $this->assertNotNull($color->fake());
    }
}
