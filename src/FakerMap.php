<?php

namespace Anteris\FakerMap;

use Anteris\FakerMap\DataTransferObjects\FakerMapData;
use Anteris\FakerMap\DataTransferObjects\FakerMethod;
use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;

class FakerMap
{
    private static bool $booted = false;

    private static Generator $faker;

    private static array $fakerMethods = [];

    private static array $resolveCache = [];

    private $default = null;

    private string $query;
    
    private string $queryMetaphone;

    private string $queryKey;

    private string $type;

    private int $disparityRequirement = -1;

    public function __construct(Generator $faker)
    {
        static::$faker = $faker;

        if (! static::$booted) {
            static::boot();
        }
    }

    private static function boot(): void
    {
        // Load in some dictionary words we have manually mapped.
        static::map(FakerDictionary::$words);

        // Load in Faker's methods for comparison later.
        $ignore  = [
            '__construct',
            'getDefaultTimezone',
            'setDefaultTimezone',
            'toLower',
            'toUpper',
            'optional',
            'unique',
            'valid',
            'shuffle',
            'shuffleArray',
            'shuffleString',
        ];

        foreach (static::$faker->getProviders() as $provider) {
            foreach (get_class_methods($provider) as $method) {
                if (in_array($method, $ignore)) {
                    continue;
                }

                $key = metaphone($method);

                static::$fakerMethods[$key] = $method;
            }
        }

        static::$booted = true;
    }

    public static function new(): static
    {
        return new static(static::faker());
    }

    public static function faker(): Generator
    {
        if (! isset(static::$faker)) {
            static::$faker = Factory::create();
        }

        return static::$faker;
    }

    public static function map(array $mappings)
    {
        foreach ($mappings as $key => $definition) {
            $niceKey = str_replace(['-', '_', ''], '', strtoupper($key));

            if (! is_array($definition) || count($definition) <= 0) {
                throw new InvalidArgumentException(
                    "The mapped value of [{$key}] must be an array!"
                );
            }

            static::$resolveCache[$niceKey] = new FakerMapData(
                $key,
                new FakerMethod(
                    array_shift($definition),
                    $definition
                )
            );
        }
    }

    public function analyze(): ?FakerMapData
    {
        if (! isset(static::$resolveCache[$this->queryKey])) {
            static::$resolveCache[$this->queryKey] = $this->resolve();
        }

        return static::$resolveCache[$this->queryKey];
    }

    public function closest(string $query): static
    {
        return $this->guess($query, 10);
    }

    public function closeEnough(string $query): static
    {
        return $this->guess($query, -1);
    }

    public function default($value): static
    {
        $this->default = $value;

        return $this;
    }

    public function guess(string $query, int $disparityRequirement): static
    {
        $this->query                = $query;
        $this->queryMetaphone       = metaphone($query);
        $this->queryKey             = str_replace(['-', '_', ' '], '', strtoupper($query));
        $this->disparityRequirement = $disparityRequirement;

        return $this;
    }

    public function type(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function fake(...$parameters)
    {
        $result = $this->analyze();

        // No result, bail early
        if (! $result) {
            return $this->default;
        }

        // Sweet, call the faker method
        $generated = call_user_func_array(
            [static::faker(), $result->method->name],
            $parameters ?? $result->method->parameters
        );

        // Double check the type
        if (isset($this->type) && gettype($generated) != $this->type) {
            return $this->default;
        }

        return $generated;
    }

    private function resolve(): ?FakerMapData
    {
        $currentMethod   = null;
        $letterDisparity = -1;

        foreach (static::$fakerMethods as $methodIndex => $method) {
            $lev = levenshtein($this->queryMetaphone, $methodIndex);

            // We found an exact match!
            if ($lev == 0) {
                $letterDisparity = $lev;
                $currentMethod   = $method;

                break;
            }

            if ($this->disparityRequirement >= 0 && $lev >= $this->disparityRequirement) {
                continue;
            }

            // This is a comparison against previous matches.
            if ($lev <= $letterDisparity || $letterDisparity < 0) {
                $letterDisparity = $lev;
                $currentMethod   = $method;
            }
        }

        if ($currentMethod != null) {
            return new FakerMapData(
                $this->query,
                new FakerMethod($currentMethod),
                $letterDisparity
            );
        }

        return null;
    }
}
