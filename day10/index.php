<?php

declare(strict_types=1);

$example = '16
10
15
5
1
11
7
19
6
12
4';

$example2 = '28
33
18
42
31
14
46
20
48
47
24
23
49
45
19
38
39
11
1
32
25
35
8
17
7
9
4
2
34
10
3';

$input = file_get_contents(__DIR__ . '/input');

$numbers = array_map(fn(string $x): int => (int) $x, explode("\n", $input));

sort($numbers);

$differencesOf1 = 0;
$differencesOf2 = 0;
$differencesOf3 = 0;

$voltage = 0;

foreach ($numbers as $number) {
    match ($number - $voltage) {
        1 => $differencesOf1++,
        2 => $differencesOf2++,
        3 => $differencesOf3++,
    };
    $voltage = $number;
}
$voltage += 3;
$differencesOf3++;

// part 1
//var_dump($differencesOf1 * $differencesOf3);

// part 2

$cache = [];

function countPaths(int $voltage, int $target, array $numbers)
{
    global $cache;
    if (array_key_exists($voltage, $cache)) {
        return $cache[$voltage];
    }

    if ($voltage === $target) {
        return 1;
    }

    if ($numbers === []) {
        return 0;
    }

    $check = array_slice($numbers, 0, 3);

    $count = array_reduce([1, 2, 3], static fn($count, $diff) => (false !== $offset = array_search($voltage + $diff, $check, true)) ?
         $count + countPaths($voltage + $diff, $target, array_slice($numbers, $offset + 1))
    : $count, 0);

    $cache[$voltage] = $count;

    return $count;
}


var_dump(countPaths(0, end($numbers), $numbers));
