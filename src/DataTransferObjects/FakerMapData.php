<?php

namespace Anteris\FakerMap\DataTransferObjects;

class FakerMapData
{
    public function __construct(
        public string $search,
        public FakerMethod $method,
        public ?int $letterDisparity = null
    ) {
    }
}
