<?php

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

require_once __DIR__ . '/vendor/autoload.php';

$input = file_get_contents('input');

function within(int $min, int $max): callable
{
    return fn (string $val): bool => is_numeric($val) && $val >= $min && $val <= $max;
}

$codes = collect([
    'byr' => within(1920, 2002),
    'iyr' => within(2010, 2020),
    'eyr' => within(2020, 2030),
    'hgt' => function (string $val): bool {
        if (! preg_match("/^(\d+)(cm|in)/", $val, $matches)) {
            return false;
        }
        if ($matches[2] === 'cm') {
            return within(150, 193)($matches[1]);
        } elseif ($matches[2] === 'in') {
            return within(59, 76)($matches[1]);
        }
        return false;
    },
    'hcl' => fn (string $val): bool => preg_match("/#[0-9a-f]{6}/", $val),
    'ecl' => fn (string $val): bool => preg_match("/amb|blu|brn|gry|grn|hzl|oth/", $val),
    'pid' => fn (string $val): bool => preg_match("/^\d{9}$/", $val),
]);

/*
byr (Birth Year) - four digits; at least 1920 and at most 2002.
iyr (Issue Year) - four digits; at least 2010 and at most 2020.
eyr (Expiration Year) - four digits; at least 2020 and at most 2030.
hgt (Height) - a number followed by either cm or in:
If cm, the number must be at least 150 and at most 193.
If in, the number must be at least 59 and at most 76.
hcl (Hair Color) - a # followed by exactly six characters 0-9 or a-f.
ecl (Eye Color) - exactly one of: amb blu brn gry grn hzl oth.
pid (Passport ID) - a nine-digit number, including leading zeroes.
cid (Country ID) - ignored, missing or not.
*/

Str::of($input)
    ->explode("\n\n")
    ->map(fn ($passport) => Str::of($passport)->split("/\s+/")->mapWithKeys(
        fn ($val) => Str::of($val)->explode(":")->pipe(fn ($parts) => [$parts[0] => $parts[1]])
    ))
    ->reject(fn (Collection $parts) => $codes->contains(fn ($test, $code) => !$parts->has($code) || !$test($parts[$code])))
    ->pipe(fn ($validPassports) => dump($validPassports->count()));
