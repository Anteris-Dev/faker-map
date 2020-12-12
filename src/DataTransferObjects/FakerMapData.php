<?php

namespace Anteris\FakerMap\DataTransferObjects;

class FakerMapData
{
    /** @var string The text we searched faker for. */
    public string $search;

    /** @var FakerMethod The faker method we came up with. */
    public FakerMethod $method;

    /** @var float A percentage that describes how similar the texts are. */
    public ?float $percentageSimilar;

    /** @var int An integer that describes how many letters would have to change to make the search become the method text. */
    public ?int $letterDisparity;

    public function __construct(
        string $search,
        FakerMethod $method,
        ?float $percentageSimilar = null,
        ?int $letterDisparity = null
    ) {
        $this->search            = $search;
        $this->method            = $method;
        $this->percentageSimilar = $percentageSimilar;
        $this->letterDisparity   = $letterDisparity;
    }
}
