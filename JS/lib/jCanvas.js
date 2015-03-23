/**
 * jCanvas v2.0 - A 2D HTML5 Canvas API
 * 
 * Copyright (C) 2011, 2013 Alex Scheel
 * All rights reserved.
 * Licensed under BSD 2 Clause License:
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice, 
 *   this list of conditions and the following disclaimer in the documentation 
 *   and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
**/

/** 
 * Usage:
 *  jCanvasDraw(canvas, ctx, frame);
 * Where frame consists of a string with one of the following drawing commands:
 * 
 * Drawing Commands:
 *   a  - arc
 *   at - arcTo
 *   b  - beginPath
 *   bt - bezierCurveTo
 *   c  - closePath
 *   cs - clearRect
 *   cw - clear screen (via resetting width)
 *   f  - fill
 *   fs - fillStyle
 *   j  - lineJoin
 *   l  - lineTo
 *   m  - moveTo
 *   q  - quadraticCurveTo
 *   r  - fillRect
 *   re - restore
 *   ro - rotate
 *   s  - stroke
 *   sa - save
 *   sc - scale
 *   ss - strokeStyle
 *   t  - translate
 *   w  - lineWidth
 *   :  - Separate arguments
 *   ,  - Separate commands
 *   /  - Comment until next comma
 * 
 * Example farme (blue circle):
 * frame = "b,a:5:5:3:0:" + 2*Math.PI + ",fs:#00F,f,c";
**/

function jCanvasDraw(canvas, ctx, frame) {
    var commands = frame.split(',');
    for (var i = 0; i < commands.length; i++) {
        var temp = commands[i].split(':');
        var command = temp[0];
        
        var arguments = new Array();
        
        for (var j = 1; j < temp.length; j++) {
            arguments[j-1] = temp[j];
        }
        
        if (command == 'a') {
            if (arguments[5] == '') {
                ctx.arc(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4]);
            } else {
                ctx.arc(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4], arguments[5]);
            }
        } else if (command == 'at') {
            ctx.arcTo(arguments[0], arguments[1], arguments[2], arguments[3], arguments[4]);
        } else if (command == 'b') {
            ctx.beginPath();
        } else if (command == 'bt') {
            ctx.bezierCurveTo(arguments[0],arguments[1],arguments[2],arguments[3],arguments[4],arguments[5]);
        } else if (command == 'c') {
            ctx.closePath();
        } else if (command == 'cs') {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        } else if (command == 'cw') {
            ctx.canvas.width = ctx.canvas.width;
        } else if (command == 'f') {
            ctx.fill();
        } else if (command == 'fs') {
            ctx.fillStyle = arguments[0];
        } else if (command == 'j') {
            ctx.lineJoin = arguments[0];
        } else if (command == 'l') {
            ctx.lineTo(arguments[0], arguments[1]);
        } else if (command == 'm') {
            ctx.moveTo(arguments[0], arguments[1]);
        } else if (command == 'q') {
            ctx.quadraticCurveTo(arguments[0], arguments[1], arguments[2], arguments[3]);
        } else if (command == 'r') {
            ctx.fillRect(arguments[0], arguments[1], arguments[2], arguments[3]);
        } else if (command == 're') {
            ctx.restore();
        } else if (command == 'ro') {
            ctx.rotate(arguments[0]);
        } else if (command == 'ss') {
            ctx.strokeStyle = arguments[0];
        } else if (command == 's') {
            ctx.stroke();
        } else if (command == 'sa') {
            ctx.save();
        } else if (command == 'sc') {
            ctx.scale(arguments[0], arguments[1]);
        } else if (command == 't') {
            ctx.translate(arguments[0], arguments[1]);
        } else if (command == 'w') {
            ctx.lineWidth = arguments[0];
        } else if (command == '/') {
            // Comment - ignore.
        } else if (command == '') {
        
        }
    }
}
