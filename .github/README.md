# Your Factory's Guide to Faking <branch>
[![Tests](https://github.com/Anteris-Dev/faker-map/workflows/Tests/badge.svg)](https://github.com/Anteris-Dev/faker-map/actions?query=workflow%3ATests)
[![Style](https://github.com/Anteris-Dev/faker-map/workflows/Style/badge.svg)](https://github.com/Anteris-Dev/faker-map/actions?query=workflow%3AStyle)

Have you ever wanted faker to just create some values for you automatically without having to instruct it on what they represent? Yeah, probably not, but at least you now know it's possible!

This package lets you turn this faker command: `(\Faker\Factory::create())->name('female')` into this short thing: `FakerMap::femaleName()`. It also allows you to dynamically lookup the method you might be looking for by doing something like this: `(new FakerMap)->closest('male name')->fake()`. If you aren't careful, this could become your new favorite way to use faker.

So how do you install it? How does it work? Let's dive in!

## To Install

Run `composer require anteris-dev/faker-map`.

## Getting Started

You should know, every method on the `FakerMap` class can be called statically or non-statically. While creating a new instance of the class will generally be more performant, the performance hit by using static methods is so little that you are not likely to notice it.

To use the class non-statically, create a new instance of the class. If you like, you are welcome to pass an existing instance of Faker to it.

For example:

```php

use Anteris\FakerMap\FakerMap;
use Faker\Factory;

$faker = Factory::create();
$map   = new FakerMap($faker);

```

Now, this `FakerMap` class will by default attempt to pass any calls to it to Faker first. So if you want, you can use Faker normally with it. For example:

```php

// This still works!
$map->name('female');

// This also works!
FakerMap::name('female');

```

This `FakerMap` class also comes with three useful methods for dynamically figuring out what value should be generated. The first of these methods is `closeEnough()`. This method takes a single string and finds the first possible Faker method that even comes close to matching it.

The second method is `closest()`. This also takes a single string but it tries to find the closest possible match to the Faker value you are looking for.

Finally, the `guess()` method allows you to determine how close or distant the match is. Its first parameter is the query, the second is a float that determines the percentage of similarity the query and the faker method must share, and the final parameter is an integer that defines how many letters may be changed in your query to make the match.

Each of these methods returns an instance of `Anteris\FakerMap\FakerResolver` which gives you various addon methods that can be called to help you with your search for the perfect method. These methods are:

- `default($value)` - Lets you define a default value in case we cannot find an appropriate method on the Faker class.
- `fake(...$parameters)` - Takes all the information gathered and returns the end result. Any parameters passed will be used in the faker method.

See examples of all of these below:

```php

// Returns a domain name because the "domainName()" method is the first match
FakerMap::closeEnough('female name')->fake();

// Returns what we are looking for: a female name, because it is the closest match
FakerMap::closest('female name')->fake();

// A custom guess that allows as low as 50.5% similarity and 3 characters to be changed
FakerMap::guess('female name', 50.5, 3)->fake();

// A guess that passes some parameters
// Returns: A number between 1 and 40
FakerMap::closest('number between')->fake(1, 40);

// A guess that definitely won't have a match so it defaults to "Yikes!"
FakerMap::closest('some unknown value')->default('Yikes!')->fake();

```

There's one last thing you should be aware of. If you don't need access to the builder methods above, you can just use your query as a static method call to the `FakerMap` class. We'll take care of the rest for you. For example:

```php

// Runs: FakerMap::closest('femaleName')->default(null)->fake();
// Returns: A female name
FakerMap::femaleName();

// Runs: FakerMap::closest('someUnknownValue')->default(null)->fake();
// Returns: null
FakerMap::someUnknownValue();

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

There are some cool packages out there that do some cool things with Faker. While it did not suit our needs, @jasonmccreary put together a nice Faker Registry package that does some similar things for generating Laravel Factory files. It may be worth checking out [here](https://github.com/laravel-shift/faker-registry). His registry array inspired our `FakerDictionary` class.
