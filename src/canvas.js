const canvas = document.getElementById("canvas");
const ctx = canvas.getContext("2d");
const width = canvas.width;
const height = canvas.height;

ctx.fillRect(0, 0, width, height);
ctx.clearRect(5, 5, width - 10, height - 10);
ctx.beginPath();
ctx.moveTo(0, height / 2);
ctx.lineTo(width, height / 2);
ctx.lineTo((width / 2), 0);
ctx.moveTo(width / 2, height);
ctx.lineTo(0, height / 2);
ctx.lineTo(width, height / 2);
ctx.moveTo()
ctx.fill();
ctx.closePath();

