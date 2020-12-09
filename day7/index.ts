const ex = `light red bags contain 1 bright white bag, 2 muted yellow bags.
dark orange bags contain 3 bright white bags, 4 muted yellow bags.
bright white bags contain 1 shiny gold bag.
muted yellow bags contain 2 shiny gold bags, 9 faded blue bags.
shiny gold bags contain 1 dark olive bag, 2 vibrant plum bags.
dark olive bags contain 3 faded blue bags, 4 dotted black bags.
vibrant plum bags contain 5 faded blue bags, 6 dotted black bags.
faded blue bags contain no other bags.
dotted black bags contain no other bags.`.split("\n");

const input : string[] = (await Deno.readTextFile("./input"))
    .split("\n")

function parseContents(line: string) {
    if (line === 'no other bags') {
        return null;
    }
    const matches = line.match(/(\d+) ([a-z\s]+) bags?$/);
    if (matches === null) throw new Error(`Cannot parse contents: ${line}`);
    return { [matches[2]]: Number(matches[1])}
}

function parseBag(line: string) {
    const match = line.match(/^([a-z\s]+) bags contain (.*).$/);
    if (match === null) throw new Error(`Cannot parse bag: ${line}`);
    // if (match[1] === 'pale cyan') {
        // console.log(match[2].split(",").map(parseContents));
        // return
    // }
    const contents = match[2].split(",").map(parseContents);
    return [match[1], contents.filter(Boolean)];
}

const rules: rules = Object.fromEntries(input.map(parseBag));

// bag is target
// or contents some is target

const target = 'shiny gold';

type bagCount = {
    [color: string]: number
};

type rules = {
    [key: string]: Array<bagCount>
}

function nested(obj: bagCount): boolean {
    return obj.hasOwnProperty(target)
        || Object.entries(obj).some(([v, _count]) => rules[v].some(nested));
}

function canBagHave() {
    return Object.entries(rules)
        .filter(([_bag, contents]) => contents.some(nested))
        .map(([bag]) => bag)
}

// part 1 = 355
console.log(canBagHave().length);

// part 2

function goDeeper(acc: number, [bag, count]: [string, number]): number {
    // console.log({bag, count})
    if (rules[bag].length === 0) {
        return count;
    }
    return rules[bag].reduce((x, obj) => {
        return x + (count * Object.entries(obj).reduce(goDeeper, x));
    }, count)
}

console.log(goDeeper(0, ['shiny gold', 1]));

console.log(
    rules[target].reduce((acc, obj) =>
        acc + Object.entries(obj).reduce(goDeeper, 0)
    , 0)
);
