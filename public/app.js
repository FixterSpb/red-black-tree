const API = 'https://run.mocky.io/v3/a5bac1c5-9593-4b58-8c31-d7fab97d5863';

const RADIUS = 30;
const SPACE = 2 * RADIUS;
const nodes = [];
const lines = [];

function fillNodesLines(tree, x = 0, y = SPACE, maxX = 0, maxY = 0){
    let leftX = 0, rightX = 0;

    if(tree.value){
        let maxLeftY, maxRightY;
        if (tree.left){
            [leftX, maxX, maxLeftY] = fillNodesLines(tree.left, x, y + SPACE, maxY);
        }

        x = x + leftX;

        if (tree.right){
            [rightX, maxX, maxRightY] = fillNodesLines(tree.right, maxX + SPACE, y + SPACE, maxY);
        }

        maxY = maxLeftY > maxRightY ? maxLeftY : maxRightY;

        x = (rightX + leftX) / 2;
        addLine({x, y}, {x: leftX, y: y + SPACE});
        addLine({x, y}, {x: rightX, y: y + SPACE});
    }else{
        x += SPACE;
    }

    addNode(tree, x, y);
    maxY = maxY > y ? maxY : y;
    maxX = maxX > x ? maxX : x;
    return [x, maxX, maxY];
}

function addNode(node, x, y){
    nodes.push({node, x, y});
}

function addLine(start, end){
    lines.push({start, end});
}

function renderLines(canvasContext, lines){
    lines.forEach(line => {
        canvasContext.beginPath();
        canvasContext.moveTo(line.start.x, line.start.y);
        canvasContext.lineTo(line.end.x, line.end.y);
        canvasContext.stroke();
    })
}

function renderNodes(canvasContext, nodes){
    nodes.forEach(item => {
        canvasContext.beginPath();
        canvasContext.fillStyle = item.node.color;
        canvasContext.arc(item.x, item.y, RADIUS, 0, 2 * Math.PI);
        canvasContext.fill();
        canvasContext.beginPath();
        canvasContext.fillStyle = "#fff";
        canvasContext.textAlign = "center";
        canvasContext.textBaseline = "middle";
        canvasContext.font = "20px serif";
        canvasContext.fillText(item.node.value, item.x, item.y,);
        canvasContext.fill();
    });
}

(async function run() {

    const tree =  await ( await fetch(API) ).json();

    const canvasContext = document.querySelector("canvas").getContext("2d");

    let canvasWidth, canvasHeight;

    [, canvasWidth, canvasHeight] = fillNodesLines(tree);
    canvasContext.canvas.width = canvasWidth + SPACE;
    canvasContext.canvas.height = canvasHeight + SPACE;

    renderLines(canvasContext, lines);
    renderNodes(canvasContext, nodes);
})();
