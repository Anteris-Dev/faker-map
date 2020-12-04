<?php

namespace Anteris\Tests\FakerMap;

use Anteris\FakerMap\FakerDictionary;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Anteris\FakerMap\FakerDictionary
 */
class FakerDictionaryTest extends TestCase
{
    public function test_it_can_resolve_name()
    {
        $method = FakerDictionary::resolve('city');

        $this->assertEquals($method, ['city']);
    }

    public function test_it_returns_null_when_it_cannot_resolve_name()
    {
        $method = FakerDictionary::resolve('hooha!');

        $this->assertEquals($method, null);
    }
}
