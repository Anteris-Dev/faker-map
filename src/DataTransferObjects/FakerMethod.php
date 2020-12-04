<?php

namespace Anteris\FakerMap\DataTransferObjects;

class FakerMethod
{
    /** @var string The faker method we came up with. */
    public string $name;

    /** @var array The parameters that should be passed to the faker method. */
    public array $parameters;

    public function __construct(string $name, array $parameters = [])
    {
        $this->name       = $name;
        $this->parameters = $parameters;
    }
}
