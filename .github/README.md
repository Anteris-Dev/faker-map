# Your Factory's Guide to Faking
[![Tests](https://github.com/Anteris-Dev/faker-map/workflows/Tests/badge.svg)](https://github.com/Anteris-Dev/faker-map/actions?query=workflow%3ATests)
[![Style](https://github.com/Anteris-Dev/faker-map/workflows/Style/badge.svg)](https://github.com/Anteris-Dev/faker-map/actions?query=workflow%3AStyle)

Have you ever wanted faker to just create some values for you automatically without having to instruct it on what they represent? Yeah, probably not, but at least you now know it's possible!

This package makes something like this: `(new FakerMap)->closest('male name')->fake()` become your new favorite way to use faker.

So how do you install it? How does it work? Let's dive in!

## To Install

Run `composer require anteris-dev/faker-map`.

## Getting Started

First, you should start by creating a new instance of the faker map class. If you like, you can even pass an existing instance of Faker to it.

At this point you will now have two handy methods for retrieving faker providers. The first is `closeEnough()`. This will look at your search query and return the first provider that even slightly matches.

The second method is more strict. That would be `closest()` which will take your search query and try to make as tight a match to what you are looking for as it can. If a close match cannot be made, it will return `null` rather than attempt to provide an alternative.

Take a look at the example below.

```php
$faker = \Faker\Factory::create();
$map = new FakerMap($faker);

// Returns 'numerify'
$map->closeEnough('number');

// Returns null
$map->closest('number');

// Returns 'domainName'
$map->closeEnough('female name');

// Returns 'name("female")'!
$map->closest('female name');
```

By default, you are returned an object that contains the following properties:

- search - _The search query you passed to the method_
- method - _An object containing information about the method resolved_
	- name - _The method name_
	- properties - _Any properties that get passed to method (e.g. "female" gets passed to "name" when using "female name")
- percentageSimilar - _How similar the search query you passed is to the method name._
- letterDisparity - _How many letters would have to be changed to transform the method name into your search query._

You can of course also call the `fake()` method on this object to simply call the recommended method. This method accepts any parameters that you would like to send to the resolved method as well. For example:

```php
// Will generate a male name since we have overwritten the female paramter
$map->closest('female name')->fake('male');

// Will generate a number between 1 and 10
$map->closest('numberBetween')->fake(1, 10);

// Will generate a number between 1 and 100
$map->closest('number between')->fake(1, 100);
```

## Advanced Usage

Here's a little secret... You can control how strict this package is with resolving your method! You see, under the hood we use the php functions `levenshtein()` and `similar_text()` to find the method that matches your query. The `closeEnough()` method has two extra arguments you can pass to it. These are `$requiredPercentageSimilar` and `$requiredLetterDisparity`.

`$requiredPercentageSimilar` is a float which determines how close your query has to be to the faker method. A value of `0` for example, would equate to not close at all. A value of `100` would require an exact match! The closest method sets this to 75%.

`$requiredLetterDisparity` looks at how many letters would have to be changed in the search query to equal the method name. A value of `0` would mean that zero letters could be added, removed, or changed. A value of `10` would mean ten letters could be added, removed, or changed. The closest method sets this to 10.

Here's an example of how to customize the values here.

```php

// This would be pretty lenient!
$map->closeEnough('address', 25, 40);

```

There's one other method that you may want to know about. That is the `options()` method. This returns an array with all the methods we have pulled from Faker. This should pull in any custom providers you have added as well!

# Reference

There are some cool packages out there that do some cool things with Faker. While it did not suit our needs, @jasonmccreary put together a nice Faker Registry package that does some similar things for generating Laravel Factory files. It may be worth checking out [here](https://github.com/laravel-shift/faker-registry). His registry array inspired our `FakerDictionary` class.
