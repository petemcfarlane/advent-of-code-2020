<?php

function testIt(string $input): bool
{
    $reg = "/(\d+)-(\d+) ([a-z]): ([a-z]+)/";
    preg_match($reg, $input, $matches);
    [$_, $min, $max, $letter, $password] = $matches;

//    $count = substr_count($password, $letter);

//    return $count >= $min && $count <= $max;

    return $password[$min - 1] === $letter
        xor $password[$max - 1] === $letter;
}

$passwords = file_get_contents('input');

$totalValid = array_reduce(explode("\n", $passwords), fn ($numOfValid, $password) => testIt($password) ? ++$numOfValid : $numOfValid, 0);
var_dump($totalValid);
