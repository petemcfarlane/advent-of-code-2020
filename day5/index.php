<?php

$input = file_get_contents(__DIR__ . "/input");

function getSeat(string $boardingPass): int
{
    $rowsCols = array_reduce(str_split($boardingPass), fn ($rowsCols, $letter) => match ($letter) {
        'F' => ['rows' => array_slice($rowsCols['rows'], 0, round(count($rowsCols['rows']) / 2)), 'cols' => $rowsCols['cols']],
        'B' => ['rows' => array_slice($rowsCols['rows'], round(count($rowsCols['rows']) / 2)), 'cols' => $rowsCols['cols']],
        'L' => ['rows' => $rowsCols['rows'], 'cols' => array_slice($rowsCols['cols'], 0, round(count($rowsCols['cols']) / 2))],
        'R' => ['rows' => $rowsCols['rows'], 'cols' => array_slice($rowsCols['cols'], round(count($rowsCols['cols']) / 2))],
    }, ['rows' => range(0, 127), 'cols' => range(0, 7)]);

    return $rowsCols['rows'][0] * 8 + $rowsCols['cols'][0];
}

var_dump(getSeat('FBFBBFFRLR'));

explode("\n", $input);
$seats = array_map('getSeat', explode("\n", $input));
// part 1
var_dump(max(...($seats)));

// part 2
sort($seats);
$init = reset($seats);

do {
    $init++;
} while (in_array($init, $seats, true));

var_dump($init);