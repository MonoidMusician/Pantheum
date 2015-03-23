function jGrid() {
    this.grid = [];
    this.paths = [];
    this.content = {};
    this.keymap = {};
    this.ikeymap = [];
    this.pathmap = [];
    this.pathrounds = [];
    this.urls = [];
    this.loadpage = true;
    this.element = '';
    this.current = '';
    this.basepath = '';
    this.basetitle = '';
    this.pwidth = 0;
    this.pheight = 0;
    this.arrows = {};
    this.keybind = true;
    this.bindHandler = function() {};
    this.unbindHandler = function() {};
    this.imagePrefetch = [];
    
    
    
    /* CONFIG */
    this.init = function(element) {
        this.element = element;
    }
    
    this.setBasepath = function(path) {
        this.basepath = path;
    }
    
    this.setBasetitle = function(title) {
        this.basetitle = title;
    }
    
    this.setGrid = function(grid) {
        this.grid = grid;
    }
    
    this.setStarting = function(hash) {
        this.current = hash;
    }
    
    this.setArrows = function(arrows) {
        this.arrows = arrows;
    }
    
    this.enableKeys = function() {
        this.keybind = true;
    }
    
    this.disableKeys = function() {
        this.keybind = false;
    }
    
    this.setBindHandler = function(func) {
        this.bindHandler = func;
    }
    
    this.setUnbindHandler = function(func) {
        this.unbindHandler = func;
    }
    
    this.setImagePrefetch = function(array) {
        this.imagePrefetch = array;
    }
    
    
    
    /* LOCATION */
    this.getLocation = function(direction, location) {
        var result = location;
        switch(direction) {
            case 'left':
                if (location[1] > 0) {
                    result[1] -= 1;
                } else {
                    return false;
                }
                break;
            case 'right':
                if (location[1] < (this.grid[location[0]].length-1)) {
                    result[1] += 1;
                } else {
                    return false;
                }
                break;
            case 'up':
                if (location[0] > 0) {
                    result[0] -= 1;
                } else {
                    return false;
                }
                break;
            case 'down':
                if (location[0] < (this.grid.length-1)) {
                    result[0] += 1;
                } else {
                    return false;
                }
                break;
            default:
                break;
        }
        
        result[0] = parseInt(result[0]);
        result[1] = parseInt(result[1]);
        
        return result;
    }
    
    this.locationInArray = function(path, location) {
        
        for (var pos in path) {
            if ((location[0] == path[pos][0]) && (location[1] == path[pos][1])) {
                return true;
            }
        }
        
        return false;
    }
    
    this.isLocation = function(location) {
        if (location == false) {
            return false;
        }
        
        if (location[0] < 0) {
            return false;
        }
        
        if (location[0] > (this.grid.length-1)) {
            return false;
        }
        
        if (location[1] < 0) {
            return false;
        }
        
        if (location[1] > (this.grid[location[0]].length-1)) {
            return false;
        }
        
        return true;
    }
    
    this.directionTo = function(start, end) {
        var sx = start[0];
        var sy = start[1];
        var ex = end[0];
        var ey = end[1];
        
        if ((sx == ex) && (sy == ey)) {
            return true;
        }
        
        if ((sx == ex) && (sy+1 == ey)) {
            return 'E';
        } else if ((sx+1 == ex) && (sy+1 == ey)) {
            return 'SE';
        } else if ((sx+1 == ex) && (sy == ey)) {
            return 'S';
        } else if ((sx+1 == ex) && (sy-1 == ey)) {
            return 'SW';
        } else if ((sx == ex) && (sy-1 == ey)) {
            return 'W';
        } else if ((sx-1 == ex) && (sy-1 == ey)) {
            return 'NW';
        } else if ((sx-1 == ex) && (sy == ey)) {
            return 'N';
        } else if ((sx-1 == ex) && (sy+1 == ey)) {
            return 'NE';
        } else {
            return false;
        }
    }
    
    this.realDirection = function(direction) {
        switch(direction) {
            case 'E':
                return "right";
                break;
            case 'W':
                return "left";
                break;
            case 'N':
                return "up";
                break;
            case 'S':
                return "down";
                break;
            default:
                break;
        }
    }
    
    this.oppositeDirection = function(direction) {
        switch(direction) {
            case 'E':
                return "left";
                break;
            case 'W':
                return "right";
                break;
            case 'N':
                return "down";
                break;
            case 'S':
                return "up";
                break;
            default:
                break;
        }
    }
    
    
    
    /* DATA MAPPING */
    this.mapKeys = function() {
        for (var x = 0; x < this.grid.length; x++) {
            for (var y = 0; y < this.grid[x].length; y++) {
                var element = this.grid[x][y];
                if (element[0] == 'jgrid-blank') {
                    continue;
                } else {
                    this.keymap[element[1]] = [x, y];
                    
                    if (this.ikeymap[x] == undefined) {
                        this.ikeymap[x] = [];
                    }
                    
                    this.ikeymap[x][y] = element[1];
                    
                    this.urls.push([element[3], element[1]]);
                    if (element.length >= 5) {
                        if (element[4] != '') {
                            this.imagePrefetch.push(element[4]);
                        }
                    }
                }
            }
        }
    }
    
    this.mapPaths = function() {
        this.pathrounds[0] = [];
        this.pathrounds[1] = [];
        var trials = ['left', 'down', 'right', 'up'];
        for (var x = 0; x < this.grid.length; x++) {
            for (var y = 0; y < this.grid[x].length; y++) {
                if (this.grid[x][y][0] != 'jgrid-blank') {
                    for (var z in trials) {
                        var direction = trials[z];
                        var nlocation = this.getLocation(direction, [x, y]);
                        if ((nlocation) && (this.grid[nlocation[0]][nlocation[1]][0] != 'jgrid-blank')) {
                            this.addPath([x, y], nlocation, [[x, y], nlocation], 0);
                        }
                    }
                    this.processRound();
                }
            }
        }
    }
    
    this.addPath = function(origin, nlocation, visited, loc) {
        this.pathrounds[loc].push([origin, nlocation, visited]);
    }
    
    this.processRound = function() {
        for (var cloc in this.pathrounds[0]) {
            var cpath = this.pathrounds[0][cloc];
            var origin = cpath[0];
            var ox = origin[0];
            var oy = origin[1];
            
            var location = cpath[1];
            var lx = location[0];
            var ly = location[1];
            
            var visited = cpath[2];
            
            if (this.pathmap[ox] == undefined) {
                this.pathmap[ox] = [];
            }
            
            if (this.pathmap[ox][oy] == undefined) {
                this.pathmap[ox][oy] = [];
            }
            
            if (this.pathmap[ox][oy][lx] == undefined) {
                this.pathmap[ox][oy][lx] = [];
            }
            
            var cvisited = this.pathmap[ox][oy][lx][ly];
            
            if ((this.pathmap[ox][oy][lx][ly] == undefined) || (visited.length < cvisited.length)) {
                this.pathmap[ox][oy][lx][ly] = this.arrayCopy(visited);
                
                var trials = ['left', 'down', 'right', 'up'];
                for (var z in trials) {
                    var direction = trials[z];
                    var nlocation = this.getLocation(direction, [lx, ly]);
                    if ((nlocation) && (this.grid[nlocation[0]][nlocation[1]][0] != 'jgrid-blank') && (this.locationInArray(visited, [nlocation[0], nlocation[1]]) == false) && ((this.pathmap[ox][oy][nlocation[0]] == undefined) || (this.pathmap[ox][oy][nlocation[0]][nlocation[1]] == undefined) || ((visited.length+1) < this.pathmap[ox][oy][nlocation[0]][nlocation[1]].length))) {
                        var vtmp = this.arrayCopy(visited);
                        vtmp.push([nlocation[0], nlocation[1]]);
                        this.addPath([ox, oy], nlocation, vtmp, 1);
                    }
                }
            }
        }
        
        if (this.pathrounds[1].length > 0) {
            this.pathrounds[0] = this.pathrounds[1];
            this.pathrounds[1] = [];
            this.processRound();
        }
    }
    
    
    
    /* FETCHING */
    this.fetchData = function() {
        for (var i in this.urls) {
            var url = this.basepath + this.urls[i][0];
            var ddata = '';
            
            $.ajaxSetup({async:false});
            $.get(url, function(data) {
                ddata = data;
            });
            $.ajaxSetup({async:true});
            
            this.content[this.urls[i][1]] = ddata;
        }
        
        for (var j in this.imagePrefetch) {
            var url = this.basepath + this.imagePrefetch[j];
            
            $.ajaxSetup({async:false});
            $.get(url);
            $.ajaxSetup({async:true});
        }
    }
    
    
    
    /* NAVIGATION */
    this.toPageByLocation = function(location) {
        if (this.isLocation(location)) {
            var x = this.keymap[this.current][0];
            var y = this.keymap[this.current][1];
            var nx = location[0];
            var ny = location[1];
            
            if (this.grid[x][y][0] != 'jgrid-blank') {
                var paths = this.pathmap[x][y][nx][ny];
                
                this.switchPages(this.arrayCopy(paths), 0);
            }
        }
    }
    
    this.toPageByHash = function(hash) {
        if (hash in this.keymap) {
            this.toPageByLocation(this.keymap[hash]);
        }
    }
    
    
    
    /* PAGE */
    this.loadPage = function() {
        $('#' + this.element).html(this.content[this.current]);
        $('#' + this.element).hide();
    }
    
    this.changeTitle = function() {
        var x = this.keymap[this.current][0];
        var y = this.keymap[this.current][1];
        window.document.title = this.basetitle + this.grid[x][y][2];
    }
    
    this.pageRun = function() {
        var x = this.keymap[this.current][0];
        var y = this.keymap[this.current][1];
        if (this.grid[x][y][5] != undefined) {
            this.grid[x][y][5]();
        }
    }
    
    
    
    /* DISPLAY */
    this.displayPage = function() {
        this.changeTitle();
        this.loadPage();
        this.pageRun();
        this.drawArrows();
    }
    
    this.switchPages = function(path, loc) {
        var current = path[loc];
        var location = path[loc+1];
        
        this.current = this.ikeymap[location[0]][location[1]];
        window.location.hash = this.current;
        
        var direction = this.realDirection(this.directionTo(current, location));
        var oppositeDirection = this.oppositeDirection(this.directionTo(current, location));
        var dis = this;
        $('#' + this.element).hide('slide', { direction: oppositeDirection }, 150, function() {
            dis.displayPage();
            $('#' + dis.element).show('slide', { direction: direction }, 150, function() {
                if ((loc+2) < path.length) {
                    dis.switchPages(path, loc+1);
                } else {
                    dis.bindEvents();
                }
            });
        });
    }
    
    
    
    /* ARROWS */
    this.drawArrows = function() {
        for (var pos in  this.arrows) {
            var arrow = this.arrows[pos];
            $('#' + arrow[0]).html('<canvas id="' + arrow[0] + '-canvas" class="jgrid-arrow" width="50" height="50"></canvas>');
            var canvas = document.getElementById(arrow[0] + '-canvas');
            var ctx = canvas.getContext('2d');
            jCanvasDraw(canvas, ctx, arrow[1]);
            
            if (jQuery.support.opacity == false) {
                $('#' + arrow[0] + '-canvas').css('filter', 'alpha(opacity=50);');
            } else {
                $('#' + arrow[0] + '-canvas').css('opacity', '0.5');
            }
        }
    }
    
    
    
    /* EVENTS */
    this.eventArrowClick = function(event) {
        var cx = event.data.instance.keymap[event.data.instance.current][0];
        var cy = event.data.instance.keymap[event.data.instance.current][1];
        
        var nlocation = event.data.instance.getLocation(event.data.arrow, [cx, cy]);
        
        if (nlocation) {
            if (event.data.instance.grid[nlocation[0]][nlocation[1]][0] != 'jgrid-blank') {
                event.data.instance.toPageByLocation(nlocation);
            }
        }
    }
    
    this.eventArrowHoverIn = function(event) {
        if (jQuery.support.opacity == false) {
            $('#' + event.data.instance.arrows[event.data.arrow][0] + '-canvas').css('filter', 'alpha(opacity=80);');
        } else {
            $('#' + event.data.instance.arrows[event.data.arrow][0] + '-canvas').css('opacity', '0.8');
        }
    }
    
    this.eventArrowHoverOut = function(event) {
        if (jQuery.support.opacity == false) {
            $('#' + event.data.instance.arrows[event.data.arrow][0] + '-canvas').css('filter', 'alpha(opacity=50);');
        } else {
            $('#' + event.data.instance.arrows[event.data.arrow][0] + '-canvas').css('opacity', '0.5');
        }
    }
    
    this.eventKeyPress = function(event) {
        // Down: 40
        // Left: 37
        // Right: 39
        // Up: 38
        // #: 51
        
        if (event.data.instance.keybind) {
            if (event.which == 40) {
                var cx = event.data.instance.keymap[event.data.instance.current][0];
                var cy = event.data.instance.keymap[event.data.instance.current][1];
                
                var nlocation = event.data.instance.getLocation("down", [cx, cy]);
                
                if (nlocation) {
                    if (event.data.instance.grid[nlocation[0]][nlocation[1]][0] != 'jgrid-blank') {
                        event.data.instance.toPageByLocation(nlocation);
                    }
                }
            } else if (event.which == 38) {
                var cx = event.data.instance.keymap[event.data.instance.current][0];
                var cy = event.data.instance.keymap[event.data.instance.current][1];
                
                var nlocation = event.data.instance.getLocation("up", [cx, cy]);
                
                if (nlocation) {
                    if (event.data.instance.grid[nlocation[0]][nlocation[1]][0] != 'jgrid-blank') {
                        event.data.instance.toPageByLocation(nlocation);
                    }
                }
            } else if (event.which == 39) {
                var cx = event.data.instance.keymap[event.data.instance.current][0];
                var cy = event.data.instance.keymap[event.data.instance.current][1];
                
                var nlocation = event.data.instance.getLocation("right", [cx, cy]);
                
                if (nlocation) {
                    if (event.data.instance.grid[nlocation[0]][nlocation[1]][0] != 'jgrid-blank') {
                        event.data.instance.toPageByLocation(nlocation);
                    }
                }
            } else if (event.which == 37) {
                var cx = event.data.instance.keymap[event.data.instance.current][0];
                var cy = event.data.instance.keymap[event.data.instance.current][1];
                
                var nlocation = event.data.instance.getLocation("left", [cx, cy]);
                
                if (nlocation) {
                    if (event.data.instance.grid[nlocation[0]][nlocation[1]][0] != 'jgrid-blank') {
                        event.data.instance.toPageByLocation(nlocation);
                    }
                }
            } else if ((event.which == 51) && (event.shiftKey)) {
                
            }
        }
        
    }
    
    this.eventHashChange = function(event) {
        if (location.hash != event.data.instance.current) {
            event.data.instance.toPageByHash(location.hash);
        } else {
            console.log(location.hash + ' ' + event.data.instance.current);
        }
    }
    
    
    
    /* BINDING */
    this.bindEvents = function() {
        this.unbindEvents();
        
        // Arrows
        for (var pos in  this.arrows) {
            var arrow = this.arrows[pos];
            $(document).on('click', '#' + arrow[0] + '-canvas', { instance: this, arrow: pos }, this.eventArrowClick);
            $(document).on('mouseenter', '#' + arrow[0] + '-canvas', { instance: this, arrow: pos }, this.eventArrowHoverIn);
            $(document).on('mouseleave', '#' + arrow[0] + '-canvas', { instance: this, arrow: pos }, this.eventArrowHoverOut);
        }
        
        if ("onhashchange" in window) {
            $(window).on('hashchange', { instance: this }, this.eventHashChange);
        }
        
        $(document).on('keydown', 'html', { instance: this }, this.eventKeyPress);
        
        this.bindHandler();
    }
    
    this.unbindEvents = function() {
        this.unbindHandler();
        
        // Arrows
        for (var pos in  this.arrows) {
            var arrow = this.arrows[pos];
            $(document).off('click', '#' + arrow[0] + '-canvas');
            $(document).off('mouseenter', '#' + arrow[0] + '-canvas');
            $(document).off('mouseleave', '#' + arrow[0] + '-canvas');
        }
        $(window).off('hashchange');
        $(document).off('keydown', 'html');
    }
    
    /* UTILITIES */
    this.arrayCopy = function(arr) {
        return arr.concat();
    }
    
    /* START */
    this.load = function() {
        this.mapKeys();
        this.mapPaths();
        this.fetchData();
        this.displayPage();
        this.bindEvents();
        $('#' + this.element).show();
    }
}
