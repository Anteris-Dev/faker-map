<?php

namespace Anteris\FakerMap;

use Anteris\FakerMap\DataTransferObjects\FakerMapData;
use Anteris\FakerMap\DataTransferObjects\FakerMethod;
use Faker\Generator;

/**
 * This class handles resolving a string to an actual Faker method.
 */
class FakerResolver
{
    protected Generator $faker;
    protected array $fakerMethods = [];
    
    /** @var mixed The value we default to in case we cannot guess the correct method. */
    protected $default = null;

    /** @var string The Faker method we are looking for. */
    protected string $query;

    /** @var int Determines what percentage of similarity the query must have to the method. */
    protected float $percentageSimilarRequirement = 0;

    /** @var int Determines how many letters can be changed in the query to make it the faker method. */
    protected int $letterDisparityRequirement = -1;

    public function __construct(Generator $faker, array $fakerMethods = [])
    {
        $this->faker        = $faker;
        $this->fakerMethods = $fakerMethods;
    }

    /**
     * Allows the creation of a new instance from the root one. This just lets
     * use make more efficient use of the faker methods array.
     */
    public function new()
    {
        return new static($this->faker, $this->fakerMethods);
    }

    /**
     * Returns an object with statistical data about the match.
     */
    public function analyze()
    {
        return $this->resolve();
    }

    /**
     * Returns the closest match to the passed query.
     */
    public function closest(string $query)
    {
        return $this->guess($query, 75, 10);
    }

    /**
     * Returns the first match made.
     */
    public function closeEnough(string $query)
    {
        return $this->guess($query, 0, -1);
    }

    /**
     * Allows the user to set a default value in case we don't come up with
     * something.
     */
    public function default($value)
    {
        $clone = clone $this;
        $clone->default = $value;
        return $clone;
    }

    /**
     * Sets our guess requirements.
     */
    public function guess(
        string $query,
        float $percentageSimilarRequirement,
        int $letterDisparityRequirement
    ) {
        $clone = clone $this;
        $clone->query = $query;
        $clone->percentageSimilarRequirement = $percentageSimilarRequirement;
        $clone->letterDisparityRequirement = $letterDisparityRequirement;
        return $clone;
    }

    /**
     * Performs the search and returns the generated value.
     */
    public function fake(...$parameters)
    {
        $result = $this->analyze();

        // No result, bail early
        if (! $result) {
            return $this->default;
        }

        // Sweet, call the faker method
        return call_user_func_array(
            [$this->faker, $result->method->name],
            $parameters ?? $result->method->parameters
        );
    }

    /**
     * Retrieves all the available provider methods from Faker.
     */
    public function getFakerMethods(): array
    {
        if (! empty($this->fakerMethods)) {
            return $this->fakerMethods;
        }

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

        foreach ($this->faker->getProviders() as $provider) {
            foreach (get_class_methods($provider) as $method) {
                if (in_array($method, $ignore)) {
                    continue;
                }

                $key = strtolower($method);

                $this->fakerMethods[$key] = $method;
            }
        }

        return $this->fakerMethods;
    }

    /**
     * Performs the actual resolution to a faker method. 
     */
    protected function resolve()
    {
        $revisedSearch = strtolower($this->query);
        $revisedSearch = preg_replace('/[-_\s]/', '', $revisedSearch);

        // These are some sure bets we've thrown into a "dictionary".
        // Let's check that first.
        if (
            (
                $this->percentageSimilarRequirement != 100 &&
                $this->letterDisparityRequirement != 0
            ) && $type = FakerDictionary::resolve($revisedSearch)
        ) {
            return new FakerMapData($this->query, new FakerMethod(
                array_shift($type),
                $type ?? null
            ));
        }

        // If there was nothing in the dictionary, we'll give it our best guess.
        $currentMethod     = null;
        $letterDisparity   = -1;
        $percentageSimilar = 0;

        foreach ($this->getFakerMethods() as $methodIndex => $method) {
            // Start by getting our similarity values
            $lev = levenshtein($revisedSearch, $methodIndex);
            similar_text($revisedSearch, $methodIndex, $percent);

            // We found an exact match!
            if ($lev == 0 && $percent == 100) {
                $letterDisparity    = $lev;
                $currentMethod      = $method;
                $percentageSimilar  = $percent;

                break;
            }

            if (
                $percent < $this->percentageSimilarRequirement ||
                (
                    $this->letterDisparityRequirement >= 0 &&
                    $lev >= $this->letterDisparityRequirement
                )
            ) {
                continue;
            }

            if (
                $percent >= $percentageSimilar &&
                ($lev <= $letterDisparity || $letterDisparity < 0)
            ) {
                $letterDisparity    = $lev;
                $currentMethod      = $method;
                $percentageSimilar  = $percent;
            }
        }

        if ($currentMethod != null) {
            return new FakerMapData(
                $this->query,
                new FakerMethod($currentMethod),
                $percentageSimilar,
                $letterDisparity
            );
        }

        return null;
    }
}
