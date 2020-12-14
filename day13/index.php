<?php declare(strict_types=1);

$example = '939
7,13,x,x,59,x,31,19';

$input = file_get_contents(__DIR__ . '/input');
[$target, $busses] = explode("\n", $input);
$target = (int) $target;


$busses = array_filter(array_map(static fn($bus): ?int => is_numeric($bus) ? (int) $bus : null, explode(",", $busses)));

[$head] = $busses;

class Bus
{
    public function __construct(public int $bus, public int $time)
    {
    }
}

usort($busses, function (int $a, int $b) use ($target) {
    return (int) ceil($target / $a) * $a <=> (int) ceil($target / $b) * $b;
});

$busNo = current($busses);

// part 1

var_dump(((int) ceil($target / $busNo) * $busNo - $target) * $busNo);


// part 2

[$_, $busses] = explode("\n", $example);
$busses = (array_map(static fn($bus): ?int => is_numeric($bus) ? (int) $bus : null, explode(",", $busses)));

$x = false;
//$lcm = array_reduce(array_filter($busses), fn ($acc, $bus) => $acc * $bus, 1);
// 0 1  2 3 4  5 6  7
// 7,13,x,x,59,x,31,19';
// 7 12     55   25 12
// 7 2^2 3  5 * 11 5^2
$lcm = 23100;

$total = 0;
$firstBus = current($busses);
$i = 1;

$unique = [];
foreach ($busses as $offset => $bus) {
    if (is_numeric($bus) && !in_array($bus - $offset, $unique)) {
        //        echo json_encode([$bus - $offset, $offset]) . PHP_EOL;
        $unique[] = $bus - $offset;
        $i *= $bus - $offset;
    }
}
//var_dump($i);
//die;
//var_dump($unique);


$bussesProduct = array_reduce($busses, fn($acc, $bus) => is_numeric($bus) ? $bus * $acc : $acc, 1);

function inverseModulo(int $a, int $mod): int
{
    $b = $a % $mod;
    for ($i = 1; $i < $mod; $i++) {
        if (($b * $i) % $mod === 1) {
            return $i;
        }
    }

    return 1;
}

$sum = 0;
foreach ($busses as $offset => $bus) {
    if (!is_numeric($bus)) {
        continue;
    }

    $remainder = ($bus - $offset) % $bus;

    $nU = $bussesProduct / $bus;
    $inverse = inverseModulo($nU, $bus);
    $sum += ($remainder * $nU * $inverse);
}

// part 2

var_dump($sum % $bussesProduct);
