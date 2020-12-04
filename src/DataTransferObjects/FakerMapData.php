<?php

namespace Anteris\FakerMap\DataTransferObjects;

use Faker\Generator;

class FakerMapData
{
    protected Generator $faker;

    /** @var string The text we searched faker for. */
    public string $search;

    /** @var FakerMethod The faker method we came up with. */
    public FakerMethod $method;

    /** @var float A percentage that describes how similar the texts are. */
    public ?float $percentageSimilar;

    /** @var int An integer that describes how many letters would have to change to make the search become the method text. */
    public ?int $letterDisparity;

    public function __construct(
        Generator $faker,
        string $search,
        FakerMethod $method,
        ?float $percentageSimilar = null,
        ?int $letterDisparity = null
    ) {
        $this->faker             = $faker;
        $this->search            = $search;
        $this->method            = $method;
        $this->percentageSimilar = $percentageSimilar;
        $this->letterDisparity   = $letterDisparity;
    }

    public function fake(...$customizations)
    {
        return call_user_func_array(
            [$this->faker, $this->method->name],
            $customizations ?? $this->method->parameters
        );
    }
}
