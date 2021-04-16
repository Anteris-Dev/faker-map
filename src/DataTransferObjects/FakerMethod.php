<?php

namespace Anteris\FakerMap\DataTransferObjects;

class FakerMethod
{
    public function __construct(
        public string $name,
        public array $parameters = []
    ) {
    }
}
