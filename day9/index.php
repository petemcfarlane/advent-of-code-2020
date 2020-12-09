<?php

declare(strict_types=1);

use JetBrains\PhpStorm\Pure;

$example = "35
20
15
25
47
40
62
55
65
95
102
117
150
182
127
219
299
277
309
576";

$input = file_get_contents(__DIR__ . '/input');

$numbers = explode("\n", $input);

class IntArray extends ArrayIterator
{
    public function __construct($array = array(), $flags = 0)
    {
        foreach ($array as $key => $value) {
            if (! is_int($key)) {
                throw new TypeError('Keys must be of type int');
            }
            if (! is_int($value)) {
                throw new TypeError('Values must be of type int');
            }
        }
        parent::__construct($array, $flags);
    }

    public function offsetSet($key, $value): void
    {
        if (! is_int($key)) {
            throw new TypeError("Key must be of type int");
        }
        if (! is_int($value)) {
            throw new TypeError("Value must be of type int");
        }
        parent::offsetSet($key, $value);
    }

    public function slice(int $offset, int $preambleLength = null): IntArray
    {
        return new IntArray(array_slice($this->getArrayCopy(), $offset, $preambleLength));
    }

    public function find(callable $f): ?int
    {
        foreach ($this->getArrayCopy() as $k => $v) {
            if ($f($v, $k)) {
                return $k;
            }
        }
        return null;
    }

    public function unset(int $key): IntArray
    {
        return new IntArray(array_diff_key($this->getArrayCopy(), [$key => null]));
    }

    public function contains(int $needle): bool
    {
        return in_array($needle, $this->getArrayCopy(), true);
    }

    public function tail(): IntArray
    {
        return $this->slice(1);
    }

    #[Pure] public function min(): int
    {
        return min(...$this);
    }

    #[Pure] public function max(): int
    {
        return max(...$this);
    }
}

$numbers = new IntArray(array_map(fn ($x) => (int) $x, $numbers));

function findInvalidNumber(IntArray $numbers, int $preambleLength): ?int
{
    $preamble = $numbers->slice(0, $preambleLength);
    $target = $numbers[$preambleLength];
    foreach ($preamble as $k => $v) {
        if ($preamble->unset($k)->contains($target - $v)) {
            return findInvalidNumber($numbers->tail(), $preambleLength);
        }
    }

    return $target;
}

// part 1
$preambleLength = 25;
$invalidNumber = findInvalidNumber($numbers, $preambleLength);
var_dump($invalidNumber);


// part 2
function findContiguousSetOfNumbers(IntArray $numbers, int $target): IntArray
{
    $acc = $numbers[0];
    $contiguousNumbers = [$numbers[0]];
    $tail = $numbers->tail();
    foreach ($tail as $n) {
        $acc += $n;
        $contiguousNumbers[] = $n;
        if ($acc === $target) {
            return new IntArray($contiguousNumbers);
        }
        if ($acc > $target) {
            return findContiguousSetOfNumbers($tail, $target);
        }
    }
    return new IntArray([]);
}

$contiguousSetOfNumbers = findContiguousSetOfNumbers($numbers, $invalidNumber);

var_dump($contiguousSetOfNumbers->min() + $contiguousSetOfNumbers->max());