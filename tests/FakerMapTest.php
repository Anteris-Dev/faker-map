<?php

namespace Anteris\Tests\FakerMap;

use Anteris\FakerMap\FakerMap;
use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FakerMapTest extends TestCase
{
    public function test_it_returns_null_if_nothing_is_found()
    {
        $fakerMap = $this->getMap();

        $this->assertNull(
            $fakerMap->closest('supercalifragilisticexpialidocious')->fake()
        );
    }

    public function test_it_returns_default_if_nothing_is_found()
    {
        $fakerMap = $this->getMap();

        $this->assertSame(
            'my-special-value',
            $fakerMap->closest('supercalifragilisticexpialidocious')->default('my-special-value')->fake()
        );
    }

    public function test_it_returns_default_if_type_does_not_match()
    {
        $fakerMap = $this->getMap();

        $this->assertNotNull($fakerMap->closest('integer')->fake());
        $this->assertNull($fakerMap->closest('integer')->type('string')->fake());
    }

    public function test_it_can_detect_exact_match()
    {
        $fakerMap = $this->getMap();

        $match = $fakerMap->closest('domainName')->analyze();

        $this->assertSame(0, $match->letterDisparity);
        $this->assertSame('domainName', $match->method->name);
    }

    public function test_it_can_create_create_faker_instance()
    {
        $this->assertNotSame($this->getFaker(), FakerMap::new()->faker());
    }

    public function test_it_can_guess_from_default_dictionary()
    {
        $faker         = $this->getFaker();
        $streetAddress = $faker->streetAddress();

        $mapped = $this->getMap()->closest('address1');

        $this->assertSame('streetAddress', $mapped->analyze()->method->name);
        $this->assertSame($streetAddress, $mapped->fake());
    }

    public function test_it_must_pass_a_valid_map()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("The mapped value of [name] must be an array!");

        $map = $this->getMap();

        $map->map(['name' => 123]);
        $map->map(['name' => []]);
    }

    private function getFaker(): Generator
    {
        $faker = Factory::create();
        $faker->seed(1234);

        return $faker;
    }

    private function getMap(): FakerMap
    {
        $map = new FakerMap($this->getFaker());

        return $map;
    }
}
