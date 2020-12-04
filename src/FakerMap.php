<?php

namespace Anteris\FakerMap;

use Anteris\FakerMap\DataTransferObjects\FakerMapData;
use Anteris\FakerMap\DataTransferObjects\FakerMethod;
use Faker\Factory;
use Faker\Generator;

class FakerMap
{
    /** @var array An array of methods that Faker has registered. */
    protected array $fakerMethods = [];

    /** @var Generator An instance of faker. */
    protected Generator $faker;

    public function __construct(Generator $faker = null)
    {
        $this->faker = $faker ?? Factory::create();
        $this->bootFakerMethods();
    }

    /**
     * Finds the closest match we dare.
     */
    public function closest(string $search)
    {
        return $this->closeEnough($search, 75, 10);
    }

    /**
     * By default, finds the closest match without caring at all to maintain
     * integrity. Search parameters can be refined by adjusting the arguments.
     */
    public function closeEnough(
        string $search,
        float $requiredPercentageSimilar = 0,
        int $requiredLetterDisparity = -1
    ) {
        $revisedSearch = strtolower($search);
        $revisedSearch = preg_replace('/[-_\s]/', '', $revisedSearch);

        // If there was nothing in the dictionary, we'll give it our best guess.
        $currentMethod     = null;
        $letterDisparity   = -1;
        $percentageSimilar = 0;

        foreach ($this->fakerMethods as $methodIndex => $method) {
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
                $percent < $requiredPercentageSimilar ||
                ($lev >= $requiredLetterDisparity && $requiredLetterDisparity >= 0)
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
                $this->faker,
                $search,
                new FakerMethod($currentMethod),
                $percentageSimilar,
                $letterDisparity
            );
        }

        // These are some sure bets we've thrown into a "dictionary".
        // Let's check that if we still haven't found anything.
        if (
            ($requiredPercentageSimilar != 100 && $requiredLetterDisparity != 0) &&
            $type = FakerDictionary::resolve($revisedSearch)
        ) {
            return new FakerMapData($this->faker, $search, new FakerMethod(
                array_shift($type),
                $type ?? null
            ));
        }

        return null;
    }

    public function options()
    {
        return array_values($this->fakerMethods);
    }

    protected function bootFakerMethods()
    {
        if (! empty($this->fakerMethods)) {
            return;
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
    }
}
