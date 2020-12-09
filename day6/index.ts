const ex = `abc

a
b
c

ab
ac

a
a
a
a

b`;

const input : string = (await Deno.readTextFile("./input"))

interface Array<T> {
  intersect(array: Array<T>): Array<T>;
  sum(): number;
}

Array.prototype.intersect = function<T>(b: Array<T>): Array<T> {
  return this.filter((x: T) => b.includes(x));
};

Array.prototype.sum = function(): number {
  return this.reduce((acc: number, v: number) => acc + v);
}

// part 1
console.log(
  input
    .split("\n\n")
    .map((str: string) =>
      new Set(
        str
          .replace(/\n/g, "")
          .split("")
      )
      .size
    )
  .sum()
);


// part 2
console.log(
  input
    .split("\n\n")
    .map(x => x.split("\n")
      .map(x => [...new Set(x)])
      .reduce((acc, xs) => acc.intersect(xs))
      .length
    )
    .sum()
);
