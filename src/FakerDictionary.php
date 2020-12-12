<?php

namespace Anteris\FakerMap;

class FakerDictionary
{
    public static function resolve(string $search)
    {
        foreach (static::$words as $word => $fakerMethod) {
            if (strpos($search, $word) !== false) {
                return $fakerMethod;
            }
        }

        return null;
    }

    /**
     * This array is based on the one built by Jason McCreary for Laravel Shift.
     * He did an excellent job with the work, though it did not fit our needs here.
     *
     * @see https://github.com/laravel-shift/faker-registry/
     * @var array An array of words that should be mapped to faker methods.
     */
    public static array $words = [
        'binary'        => ['sha256'],
        'biginteger'    => ['numberBetween', -100000, 100000],
        'char'          => ['randomLetter'],
        'datetimetz'    => ['datetimetz'],
        'float'         => ['randomFloat', 19, 2],
        'double'        => ['randomFloat', 19, 2],
        'geometry'      => ['word'],
        'integer'       => ['numberBetween', -10000, 10000],
        'ip'            => ['ipv4'],
        'ipaddress'     => ['ipv4'],
        'linestring'    => ['word'],
        'longtext'      => ['text'],
        'female'        => ['name', 'female'],
        'male'          => ['name', 'male'],
        'mac'           => ['macAddress'],
        'macaddress'    => ['macAddress'],
        'address1'      => ['streetAddress'],
        'address2'      => ['secondaryAddress'],
        'address'       => ['safeAddress'],
        'content'       => ['paragraphs', 3, true],
        'company'       => ['company'],
        'country'       => ['country'],
        'city'          => ['city'],
        'datetime'      => ['dateTime'],
        'date'          => ['date'],
        'time'          => ['time'],
        'town'          => ['city'],
        'state'         => ['state'],
        'street'        => ['street'],
        'password'      => ['password', 100, 150],
        'description'   => ['text'],
        'email'         => ['safeEmail'],
        'guid'          => ['uuid'],
        'latitude'      => ['latitude'],
        'longitude'     => ['longitude'],
        'phone'         => ['phoneNumber'],
        'security'      => ['ssn'],
        'social'        => ['ssn'],
        'summary'       => ['text'],
        'title'         => ['sentence', 4],
        'zip'           => ['postcode'],
    ];
}
