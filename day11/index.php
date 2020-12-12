<?php

declare(strict_types=1);

use JetBrains\PhpStorm\Pure;

$example = 'L.LL.LL.LL
LLLLLLL.LL
L.L.L..L..
LLLL.LL.LL
L.LL.LL.LL
L.LLLLL.LL
..L.L.....
LLLLLLLLLL
L.LLLLLL.L
L.LLLLL.LL';

$input = file_get_contents(__DIR__ . '/input');

const OCCUPIED = '#';
const EMPTY_SEAT = 'L';
const FLOOR = '.';

$rows = explode("\n", $input);
$grid = new Grid(array_map('str_split', $rows));
//var_dump($grid);

// If a seat is empty (L) and there are no occupied seats adjacent to it, the seat becomes occupied.
// If a seat is occupied (#) and four or more seats adjacent to it are also occupied, the seat becomes empty.
// Otherwise, the seat's state does not change.
// Floor (.) never changes; seats don't move, and nobody sits on the floor.

class Dir {
    public const NW = 'NW';
    public const N = 'N';
    public const NE = 'NE';
    public const W = 'W';
    public const E = 'E';
    public const SW = 'SW';
    public const S = 'S';
    public const SE = 'SE';

    public const ALL = [
        self::NW,
        self::N,
        self::NE,
        self::W,
        self::E,
        self::SW,
        self::S,
        self::SE,
    ];
}

class Grid
{
    public function __construct(private array $rows)
    {
    }

    public function goUntilStable(): Grid
    {
        $newGrid = $this->nextIteration($this->compareNeighbours());

        return ($newGrid->rows === $this->rows) ? $newGrid : $newGrid->goUntilStable();
    }

    public function goUntilStable2(): Grid
    {
        $newGrid = $this->nextIteration($this->compareVisible());

        return ($newGrid->rows === $this->rows) ? $newGrid : $newGrid->goUntilStable2();
    }

    private function nextIteration(callable $lookup): Grid
    {
        $newGrid = [[]];
        foreach ($this->iterate() as $p) {
            $newGrid[$p->row][$p->col] = $lookup($p);
        }

        return new Grid($newGrid);
    }

    private function numberOfOccupiedNeighbours(Point $p): int
    {
        $surroundingPoints = array_map(
            fn ($dir) => $this->isOccupied($p->move($dir)),
            Dir::ALL
        );

        return array_sum($surroundingPoints);
    }

    public function numberOfOccupiedVisible(Point $p): int
    {
        $surroundingPoints = array_map(
            fn ($dir) => $this->canSeeOccupied($p, $dir),
            Dir::ALL
        );

        return array_sum($surroundingPoints);
    }

    #[Pure]
    public function countOccupiedSeats(): int
    {
        $counter = 0;
        foreach ($this->iterate() as $pointWithValue) {
            if ($pointWithValue->value === OCCUPIED) {
                $counter++;
            }
        }

        return $counter;
    }

    private function canSeeOccupied(Point $p, $dir): bool
    {
        $p = $p->move($dir);
        if ($this->isOutOfBounds($p)) {
            return false;
        }
        if ($this->isSeat($p)) {
            return $this->isOccupied($p);
        }
        return $this->canSeeOccupied($p, $dir);
    }

    private function isSeat(Point $p): bool
    {
        $seat = $this->rows[$p->row][$p->col] ?? null;

        return $seat === OCCUPIED || $seat === EMPTY_SEAT;
    }

    public function print(): self
    {
        foreach ($this->rows as $row) {
            foreach ($row as $col) {
                echo $col;
            }
            echo PHP_EOL;
        }
        echo PHP_EOL;

        return $this;
    }

    private function isOccupied(Point $p): bool
    {
        return ($this->rows[$p->row][$p->col] ?? null) === OCCUPIED;
    }

    #[Pure]
    private function iterate(): Generator
    {
        foreach ($this->rows as $r => $row) {
            foreach ($row as $c => $value) {
                yield new PointWithValue($r, $c, $value);
            }
        }
    }

    private function isOutOfBounds(Point $p): bool
    {
        return ($this->rows[$p->row][$p->col] ?? null) === null;
    }

    public function compareNeighbours(): callable
    {
        return fn(PointWithValue $p) => match (true) {
            $p->value === FLOOR => FLOOR,
            $p->value === EMPTY_SEAT && $this->numberOfOccupiedNeighbours($p) === 0 => OCCUPIED,
            $p->value === OCCUPIED && $this->numberOfOccupiedNeighbours($p) >= 4 => EMPTY_SEAT,
            default => $p->value
        };
    }

    public function compareVisible(): callable
    {
        return fn(PointWithValue $p) => match (true) {
            $p->value === FLOOR => FLOOR,
            $p->value === EMPTY_SEAT && $this->numberOfOccupiedVisible($p) === 0 => OCCUPIED,
            $p->value === OCCUPIED && $this->numberOfOccupiedVisible($p) >= 5 => EMPTY_SEAT,
            default => $p->value
        };
    }
}

class Point
{
    public function __construct(public int $row, public int $col)
    {
    }

    #[Pure]
    public function move($dir): Point
    {
        return match ($dir) {
            Dir::NW => new Point($this->row - 1, $this->col - 1),
            Dir::N => new Point($this->row - 1, $this->col),
            Dir::NE => new Point($this->row - 1, $this->col + 1),
            Dir::W => new Point($this->row, $this->col - 1),
            Dir::E => new Point($this->row, $this->col + 1),
            Dir::SW => new Point($this->row + 1, $this->col - 1),
            Dir::S => new Point($this->row + 1, $this->col),
            Dir::SE => new Point($this->row + 1, $this->col + 1),
        };
    }
}

class PointWithValue extends Point
{
    public function __construct(public int $row, public int $col, public string $value)
    {
    }
}


// part 1
var_dump($grid->goUntilStable()->countOccupiedSeats());

// part 2

$gridWith8 = '.......#.
...#.....
.#.......
.........
..#L....#
....#....
.........
#........
...#.....';

$rows = explode("\n", $gridWith8);
$gridWith8 = new Grid(array_map('str_split', $rows));

var_dump($gridWith8->numberOfOccupiedVisible(new Point (4, 3)));

$gridWith2 = '.............
.L.L.#.#.#.#.
.............';

$rows = explode("\n", $gridWith2);
$gridWith2 = new Grid(array_map('str_split', $rows));

var_dump($gridWith2->numberOfOccupiedVisible(new Point (1, 1)));

$noneOccupied = '.##.##.
#.#.#.#
##...##
...L...
##...##
#.#.#.#
.##.##.';

$rows = explode("\n", $noneOccupied);
$noneOccupied = new Grid(array_map('str_split', $rows));

var_dump($noneOccupied->numberOfOccupiedVisible(new Point (3, 3)));


var_dump($grid->goUntilStable2()->countOccupiedSeats());
