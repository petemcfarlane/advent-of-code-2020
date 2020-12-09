<?php

declare(strict_types=1);

use JetBrains\PhpStorm\Pure;

$example = 'nop +0
acc +1
jmp +4
acc +3
jmp -3
acc -99
acc +1
jmp -4
acc +6';

$input = file_get_contents(__DIR__ . '/input');
$instructions = explode("\n", $input);

class Acc
{
    public int $value = 0;
    public int $offset = 0;
    public array $visited = [];
    private int $end;

    public function __construct(public array $instructions)
    {
        $this->end = count($instructions);
    }

    public function run(): int
    {
        if ($this->offset >= $this->end || $this->hasVisited()) {
            return $this->value;
        }

        return $this->next()->run();
    }

    public function terminates(): bool
    {
        if ($this->offset >= $this->end) {
            return true;
        }
        if ($this->hasVisited()) {
            return false;
        }

        return $this->next()->terminates();
    }

    private function acc(int $by): self
    {
        $this->value += $by;

        return $this->jmp(1);
    }

    private function jmp(int $by): self
    {
        $this->offset += $by;

        return $this;
    }

    private function nop(): self
    {
        return $this->jmp(1);
    }

    #[Pure]
    private function hasVisited(): bool
    {
        return in_array($this->offset, $this->visited, true);
    }

    private function next(): Acc
    {
        $this->visited[] = $this->offset;

        $line = $this->instructions[$this->offset];

        $operation = substr($line, 0, 3);
        $argument = (int) substr($line, 4);

        return match ($operation) {
            'nop' => $this->nop(),
            'acc' => $this->acc($argument),
            'jmp' => $this->jmp($argument),
        };
    }

}

// part 1
var_dump((new Acc($instructions))->run());

function generateNewInstructions(array $instructions): iterable
{
    foreach ($instructions as $offset => $line) {
        if (str_contains($line, 'acc')) {
            continue;
        }
        yield array_replace($instructions, [
            $offset => strtr($line, ['nop' => 'jmp', 'jmp' => 'nop'])
        ]);
    }
}

// part 2
foreach (generateNewInstructions($instructions) as $i => $newInstructions) {
    $acc = new Acc($newInstructions);
    if ($acc->terminates()) {
        var_dump($acc->run());
        break;
    }
}