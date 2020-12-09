// const map = `..##.......
// #...#...#..
// .#....#..#.
// ..#.#...#.#
// .#...##..#.
// ..#.##.....
// .#.#.#....#
// .#........#
// #.##...#...
// #...##....#
// .#..#...#.#`.split("\n");

const map : string[] = (await Deno.readTextFile("./input"))
    .split("\n");


const width = map[0].length;
console.log({width})

function* move(right: number, down: number): Generator<[number, number]> {
    let x = 0, y = 0;
    while (true) {
        yield [x, y];
        x = x + down;
        y = (y + right) % width;
    }
}

// part 1
const moves = move(3, 1);
const result1 = map.reduce(acc => {
    const [x, y] = (moves.next().value);
    return map[x][y] === '#' ? acc + 1 : acc;
}, 0);

console.log({result1})

// part 2
const r1d1 = move(1,1);
const r3d1 = move(3,1);
const r5d1 = move(5,1);
const r7d1 = move(7,1);
const r1d2 = move(1,2);

const result2 = [r1d1, r3d1, r5d1, r7d1, r1d2].reduce((acc, m) => {
    return acc * map.reduce(acc => {
        const [x, y] = (m.next().value);
        return (map[x]?.[y] ?? '') === '#' ? acc + 1 : acc;
    }, 0)
}, 1);

console.log({result2});