# Your Factory's Guide to Faking
[![Tests](https://github.com/Anteris-Dev/faker-map/workflows/Tests/badge.svg)](https://github.com/Anteris-Dev/faker-map/actions?query=workflow%3ATests)
[![Style](https://github.com/Anteris-Dev/faker-map/workflows/Style/badge.svg)](https://github.com/Anteris-Dev/faker-map/actions?query=workflow%3AStyle)

Have you ever wanted faker to just create some values for you automatically without having to instruct it on what they represent? Yeah, probably not, but at least you now know it's possible!

This package allows you to dynamically lookup the method you might be looking for by doing something like this: `FakerMap::new()->closest('male name')->fake()`.

So how do you install it? How does it work? Let's dive in!

## To Install

Run `composer require anteris-dev/faker-map`.

## Getting Started

```php

use Anteris\FakerMap\FakerMap;
use Faker\Factory;

// FakerMap will handle creating a faker instance for you here.
$map = FakerMap::new();

// Or you can pass in your own instance. This is handy for seeding!
$faker = Factory::create();
$faker->seed(123);

$map = new FakerMap($faker);

```

This `FakerMap` class comes with three useful methods for dynamically figuring out what value should be generated. The first of these methods is `closeEnough()`. This method takes a single string and finds the first possible Faker method that even comes close to matching it.

The second method is `closest()`. This also takes a single string but it tries to find the closest possible match to the Faker value you are looking for.

Finally, the `guess()` method allows you to determine how close or distant the match is. Its first parameter is the query and the second is an integer that defines how many letters may be changed in your query to make the match.

Each of these methods returns an instance of `Anteris\FakerMap\FakerMap` which gives you various addon methods that can be called to help you with your search for the perfect method. These methods are:

- `default($value)` - Lets you define a default value in case we cannot find an appropriate method on the Faker class.
- `type(string $type)` - Lets you define the type the returned value should have. If the faked value doesn't match the type, null or default will be returned instead.
- `fake(...$parameters)` - Takes all the information gathered and returns the end result. Any parameters passed will be used in the faker method.
- `faker()` - Returns the underlying instance of faker for any use you may have for it.

See examples of all of these below:

```php

FakerMap::new()->closeEnough('female name')->fake();

FakerMap::new()->closest('female name')->fake();

FakerMap::new()->guess('female name', 3)->fake();

// A guess that passes some parameters
// Returns: A number between 1 and 40
FakerMap::new()->closest('number between')->fake(1, 40);

// A guess that definitely won't have a match so it defaults to "Yikes!"
FakerMap::new()->closest('some unknown value')->default('Yikes!')->fake();

// Passes on to the underlying faker instance.
FakerMap::new()->faker()->firstNameFemale();

```

# But why?
Why did we choose to create this package? Well, we wanted to be able to fake realistic values based on the names of properties retrieved using PHPs reflection API. This would allow us to fill the following class with realistic data. Pretty cool right?

```php

class Person {
	public string $firstName;
	public string $lastName;
	public string $companyName;
	public string $email;
	public string $address1;
	public string $address2;
	public string $socialSecurityNumber;
}

```

# Reference

There are some cool packages out there that do some cool things with Faker. While it did not suit our needs, Jason McCreary put together a nice Faker Registry package that does some similar things for generating Laravel Factory files. It may be worth checking out [here](https://github.com/laravel-shift/faker-registry). His registry array inspired our `FakerDictionary` class.
