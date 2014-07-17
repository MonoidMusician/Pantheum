function drawHome(element, color) {
    var canvas = document.getElementById(element);
    var ctx = canvas.getContext('2d');
    
    var start = "cs,b,";
    var end = ",ss:" + color + ",w:4,c,s";
    var frame = start + "m:5:12.5,l:12.5:2.5,l:20:12.5,ss:" + color + ",w:3,c,s,fs:" + color + ",f,b,m:7.5:10,l:7.5:22.5,l:17.5:22.5,l:17.5:10,m:0:0" + end;
    jCanvasDraw(canvas, ctx, frame);
}

function drawPlus(element, color) {
    var canvas = document.getElementById(element);
    var ctx = canvas.getContext('2d');
    
    var start = "cs,b,";
    var end = ",ss:" + color + ",w:4,c,s";
    var frame = start + "m:5:0,l:5:10,m:0:5,l:10:5,m:0:0" + end;
    jCanvasDraw(canvas, ctx, frame);
}

function drawDarr(element, color) {
    var canvas = document.getElementById(element);
    var ctx = canvas.getContext('2d');
    
    var start = "cs,b,";
    var end = ",ss:" + color + ",w:3,c,s";
    var frame = start + "m:0:5,l:15:5,l:7.5:8.5,l:0:5,fs:" + color + ",f" + end;
    jCanvasDraw(canvas, ctx, frame);
}
