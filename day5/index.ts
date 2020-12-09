const input : string[] = (await Deno.readTextFile("./input"))
    .split("\n");



// FBFBBFFRLR 44 * 8 + 5 = 357
function getSeatId(boardingPass: string): number {
    const [rows, cols] = boardingPass.split("").reduce(([rows, cols], letter) => {
        if (letter === 'F') {
            const half = Math.ceil(rows.length / 2);
            return [rows.splice(0, half), cols];
        }
        if (letter === 'B') {
            const half = Math.ceil(rows.length / 2);
            return [rows.splice(-half), cols];
        }
        if (letter === 'L') {
            const half = Math.ceil(cols.length / 2);
            return [rows, cols.splice(0, half)];
        }
        if (letter === 'R') {
            const half = Math.ceil(cols.length / 2);
            return [rows, cols.splice(-half)];
        }
        return [rows, cols];
    }, [[...Array(128).keys()], [...Array(8).keys()]]);
    // console.log(rows, cols);

    return rows[0] * 8 + cols[0];
}


console.log(Math.max(...input.map(getSeatId)));