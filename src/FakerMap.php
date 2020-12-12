<?php

namespace Anteris\FakerMap;

use Faker\Factory;
use Faker\Generator;

class FakerMap
{
    /** @var Generator An instance of faker. */
    protected Generator $faker;

    /** @var FakerResolver Resolves guesses to methods. */
    protected FakerResolver $resolver;

    public function __construct(Generator $faker = null, FakerResolver $resolver = null)
    {
        $this->faker    = $faker ?? Factory::create();
        $this->resolver = $resolver ?? new FakerResolver($this->faker);
    }

    /**
     * Directs class calls to other classes. Starts by attempting to pass it on
     * to Faker. If that fails, passes on to the resolver. If that fails, it attempts
     * to guess which method we are after.
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->faker, $name)) {
            return $this->faker->{$name}(...$arguments);
        }

        if (method_exists($this->resolver, $name)) {
            return $this->resolver->new()->{$name}(...$arguments);
        }

        return $this->resolver->new()
            ->closest($name)
            ->default(null)
            ->fake(...$arguments);
    }

    /**
     * Passes static calls on to a non-static call.
     */
    public static function __callStatic($name, $arguments)
    {
        return (new static)->{$name}(...$arguments);
    }
}
