const example = `F10
N3
F7
R90
F11
`.split("\n");

const input : string[] = (await Deno.readTextFile("./input"))
  .split("\n");

/*
Action N means to move north by the given value.
Action S means to move south by the given value.
Action E means to move east by the given value.
Action W means to move west by the given value.
Action L means to turn left the given number of degrees.
Action R means to turn right the given number of degrees.
Action F means to move forward by the given value in the direction the ship is currently facing.*/

enum Action {
  N = 'N',
  S = 'S',
  E = 'E',
  W = 'W',
  L = 'L',
  R = 'R',
  F = 'F',
}

interface Point {
  x: number,
  y: number,
}

interface Santa extends Point {
  dir: Action.N | Action.E | Action.S | Action.W
}

const santa: Santa  = {
  x: 0,
  y: 0,
  dir: Action.E
};

function turnR(santa: Santa, turns: number): Santa {
  if (turns === 0) {
    return santa;
  }

  if (santa.dir === Action.N) {
     return turnR({...santa, dir: Action.E}, turns - 1);
  }
  if (santa.dir === Action.E) {
     return turnR({...santa, dir: Action.S}, turns - 1);
  }
  if (santa.dir === Action.S) {
     return turnR({...santa, dir: Action.W}, turns - 1);
  }
  if (santa.dir === Action.W) {
     return turnR({...santa, dir: Action.N}, turns - 1);
  }

  return santa;
}

function turnL(santa: Santa, turns: number): Santa {
  if (turns === 0) {
    return santa;
  }

  if (santa.dir === Action.N) {
    return turnL({...santa, dir: Action.W}, turns - 1);
  }
  if (santa.dir === Action.E) {
    return turnL({...santa, dir: Action.N}, turns - 1);
  }
  if (santa.dir === Action.S) {
    return turnL({...santa, dir: Action.E}, turns - 1);
  }
  if (santa.dir === Action.W) {
    return turnL({...santa, dir: Action.S}, turns - 1);
  }

  return santa;
}

const {x, y} = example
  .reduce((santa: Santa, instruction: string) => {
    const match = instruction.match(/([NSEWLRF])(\d+)/);
    if (!match) {
      return santa;
    }
    const [_, action, value] = match
    if (action === Action.F) {
      if (santa.dir === Action.N) {
        return { ...santa, y: santa.y + Number(value) };
      }
      if (santa.dir === Action.E) {
        return { ...santa, x: santa.x + Number(value) };
      }
      if (santa.dir === Action.S) {
        return { ...santa, y: santa.y - Number(value) };
      }
      if (santa.dir === Action.W) {
        return { ...santa, x: santa.x - Number(value) };
      }
    }
    if (action === Action.L) {
      return turnL(santa, Number(value) / 90);
    }
    if (action === Action.R) {
      return turnR(santa, Number(value) / 90);
    }
    if (action === Action.N) {
      return { ...santa, y: santa.y + Number(value) };
    }
    if (action === Action.E) {
      return { ...santa, x: santa.x + Number(value) };
    }
    if (action === Action.S) {
      return { ...santa, y: santa.y - Number(value) };
    }
    if (action === Action.W) {
      return { ...santa, x: santa.x - Number(value) };
    }
    return santa;
  }, santa);

// part 1
console.log(Math.abs(x) + Math.abs(y));

// part 2
const wp = { x: 10, y: 1 };

interface SantaWithWaypoint extends Santa {
  wp: Point
}

function rotateR({ x, y } : Point, deg: string): Point {
  if (deg === '90') {
    return { x: y, y: -x };
  }
  if (deg === '180') {
    return { x: -x, y: -y };
  }
  if (deg === '270') {
    return { x: -y, y: x };
  }

  throw new Error(deg);
}

function rotateL(p : Point, deg: string): Point {
  if (deg === '90') {
    return rotateR(p, '270');
  }
  if (deg === '180') {
    return rotateR(p, '180');
  }
  if (deg === '270') {
    return rotateR(p, '90');
  }

  throw new Error(deg);
}

const { x: x2, y: y2 }: SantaWithWaypoint = input.reduce((santa: SantaWithWaypoint, instruction: string) => {
  const match = instruction.match(/([NSEWLRF])(\d+)/);
  if (!match) {
    return santa;
  }
  const [_, action, value] = match
  if (action === Action.F) {
    // move forward to the waypoint a number of times equal to the given value.
    return { ...santa, x: santa.x + Number(value) * santa.wp.x, y: santa.y + Number(value) * santa.wp.y };
  }
  if (action === Action.N) {
    // move the waypoint north by the given value.
    return { ...santa, wp: { ...santa.wp, y: santa.wp.y + Number(value)} };
  }
  if (action === Action.E) {
    return { ...santa, wp: { ...santa.wp, x: santa.wp.x + Number(value)} };
  }
  if (action === Action.S) {
    return { ...santa, wp: { ...santa.wp, y: santa.wp.y - Number(value)} };
  }
  if (action === Action.W) {
    return { ...santa, wp: { ...santa.wp, x: santa.wp.x - Number(value)} };
  }
  if (action === Action.R) {
    // rotate the waypoint around the ship right (clockwise) the given number of degrees
    return { ...santa, wp: rotateR(santa.wp, value) };
  }
  if (action === Action.L) {
    // rotate the waypoint around the ship right (clockwise) the given number of degrees
    return { ...santa, wp: rotateL(santa.wp, value) };
  }
  return santa;
  }, { ...santa, wp });

// part 2
console.log(Math.abs(x2) + Math.abs(y2));
