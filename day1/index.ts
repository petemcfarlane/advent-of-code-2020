const input : number[] = (await Deno.readTextFile("./input"))
    .split("\n")
    .map(Number);

function findNumbersThatSumTo(sum: number, list: number[]): [number, number] {
    const a = list.find(item => list.includes(sum - item)) || 0;
    const b = sum - a;
    return [a, b];
}

// part 1
const [a, b] = findNumbersThatSumTo(2020, input);
console.log(a * b);

// part 2

const x = input.find(x => {
    const [y, z] = findNumbersThatSumTo(2020 - x, input);
    // if (x != 0 && y != 0 && z != 0) {
    //     console.log(x, y, z);
    // }
    return x != 0 && y != 0 && z != 0;
}) || 0;
const [y, z] = findNumbersThatSumTo(2020 - x, input);

console.log(x * y * z);

