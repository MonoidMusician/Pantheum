function jBracket() {
    this.element = '';
    this.type = '';
    this.data = {};
    this.duri = '';
    this.teams = 0;
    this.pwidth = undefined;
    this.pheight = undefined;
    this.maxwidth = 400;
    this.maxheight = 100;
    this.minwidth = 100;
    this.minheight = 24;
    this.ewidth = 0;
    this.eheight = 0;
    this.color = '#000000';
    this.editable = false;
    this.gamecount = 0;
    
    this.init = function(element, type, editable) {
        this.element = element;
        this.pwidth = $('#' + this.element).width();
        this.pheight = $('#' + this.element).height();
        this.type = type;
        this.editable = editable;
    }
    
    this.setColors = function(side) {
        this.color = side;
    }
    
    this.setSizes = function(minwidth, minheight, maxwidth, maxheight) {
        this.minwidth = minwidth;
        this.minheight = minheight;
        this.maxwidth = maxwidth;
        this.maxheight = maxheight;
    }
    
    this.setData = function(key, value) {
        this.data[key] = value;
    }
    
    this.setDataURI = function(duri) {
        this.duri = duri;
    }
    
    this.setTeams = function(teams) {
        teams = parseInt(teams);
        if (Math.floor(this.log2(teams)) == this.log2(teams)) {
            this.teams = teams;
        } else {
            this.teams = Math.pow(parseInt(this.log2(teams))+1, 2);
        }
        
        this.teams = parseInt(this.teams);
    }
    
    this.draw = function() {
        $('#' + this.element).html('');
        $('#' + this.element).css('position', 'relative');
        $('#' + this.element + '-clear').remove();
        
        switch(this.type) {
            case 'single':
                this.drawSingle();
                break;
            case 'double':
                this.drawDouble();
                break;
            case 'consolation':
                this.drawConsolation();
                
        }
    }
    
    this.drawSingle = function() {
        this.gamecount = this.log2(this.teams) + 1;
        this.ewidth = Math.floor(this.pwidth / ((this.gamecount)+2));
        this.eheight = Math.floor(this.pheight / (this.teams+1));
        
        if (this.ewidth > this.maxwidth) {
            this.ewidth = this.maxwidth;
        } else if (this.ewidth < this.minwidth) {
            this.ewidth = this.minwidth;
        }
        
        if (this.eheight > this.maxheight) {
            this.eheight = this.maxheight;
        } else if (this.eheight < this.minheight) {
            this.eheight = this.minheight;
        }
        
        var increment = new Array();
        for (var id = 1; id < this.teams*2; id++) {
            if ((id % 2) == 1) {
                var game = 0;
                if (increment[game]===undefined) {
                    increment[game] = 0;
                }
                
                $('#' + this.element).append('<div id="' + this.element + '-dT' + id + '" class="jbracket-team-div"><span id="' + this.element + '-sT' + id + '" class="jbracket-team-span">Team&nbsp;' + id + '</span></div>');
                $('#' + this.element + '-dT' + id).css('height', this.eheight - 2 + 'px');
                $('#' + this.element + '-dT' + id).css('border-bottom', this.color + ' solid 2px');
                $('#' + this.element + '-dT' + id).css('position', 'absolute');
                $('#' + this.element + '-dT' + id).css('left', this.ewidth + 'px');
                $('#' + this.element + '-dT' + id).css('top', (this.eheight * increment[game]) + 'px');
                $('#' + this.element + '-sT' + id).css('position', 'absolute');
                $('#' + this.element + '-sT' + id).css('bottom', '0px');
                if (((id+1) % 4) == 0) {
                    $('#' + this.element + '-dT' + id).css('width', this.ewidth - 2);
                    $('#' + this.element + '-dT' + id).css('border-right', this.color + ' solid 2px');
                } else {
                    $('#' + this.element + '-dT' + id).css('width', this.ewidth);
                }
                increment[game] += 1;
            } else {
                var game = 0;
                for (var x = 0; x < this.gamecount+2; x++) {
                    if ((id+Math.pow(2, x)) % Math.pow(2, (x + 1)) == 0) {
                        game = x;
                        break;
                    }
                }
                
                if (increment[game]===undefined) {
                    increment[game] = 0;
                }
                
                $('#' + this.element).append('<div id="' + this.element + '-dG' + id + '" class="jbracket-game-div"><span id="' + this.element + '-sG' + id + '" class="jbracket-game-span">Game&nbsp;' + id + '</span></div>');
                $('#' + this.element + '-dG' + id).css('border-bottom', this.color + ' solid 2px');
                $('#' + this.element + '-dG' + id).css('position', 'absolute');
                
                var zheight = (this.eheight * Math.pow(2, game));
                var pvheight = zheight * (increment[game]);
                var offset = (this.eheight * Math.pow(2, game-1)) + 0.5 * this.eheight - zheight;
                
                $('#' + this.element + '-dG' + id).css('height', (zheight - 2) + 'px');
                $('#' + this.element + '-dG' + id).css('top', (offset + pvheight) + 'px');
                $('#' + this.element + '-dG' + id).css('left', (this.ewidth*(game+1)) + 'px');
                $('#' + this.element + '-sG' + id).css('position', 'absolute');
                $('#' + this.element + '-sG' + id).css('bottom', '0px');
                $('#' + this.element + '-dG' + id).css('width', this.ewidth);
                if ((increment[game] % 2) == 1) {
                    $('#' + this.element + '-dG' + id).css('width', this.ewidth - 2);
                    $('#' + this.element + '-dG' + id).css('border-right', this.color + ' solid 2px');
                }
                
                increment[game] += 1;
            }
        }
        
        if (this.eheight == this.minheight) {
            $('#' + this.element).append('<div id="' + this.element + '-clear">&nbsp;<br></div>');
            $('#' + this.element + '-clear').css('position', 'absolute');
            $('#' + this.element + '-clear').css('top', parseInt(this.eheight) * (parseInt(this.teams)+1));
        }
    }
    
    this.drawDouble = function() {
        this.gamecount = (this.log2(this.teams)*3) + 1;
        this.ewidth = Math.floor(this.pwidth / ((this.gamecount)+2));
        this.eheight = Math.floor(this.pheight / (this.teams+1));
        
        if (this.ewidth > this.maxwidth) {
            this.ewidth = this.maxwidth;
        } else if (this.ewidth < this.minwidth) {
            this.ewidth = this.minwidth;
        }
        
        if (this.eheight > this.maxheight) {
            this.eheight = this.maxheight;
        } else if (this.eheight < this.minheight) {
            this.eheight = this.minheight;
        }
        
        var increment = new Array();
        for (var id = 1; id < this.teams*2; id++) {
            if ((id % 2) == 1) {
                var game = 0;
                var rgame = ((this.gamecount-1) - this.log2(this.teams)) + game;
                
                if (increment[rgame]===undefined) {
                    increment[rgame] = 0;
                }
                
                $('#' + this.element).append('<div id="' + this.element + '-dT' + id + '"><span id="' + this.element + '-sT' + id + '">Team&nbsp;' + id + '</span></div>');
                $('#' + this.element + '-dT' + id).css('height', this.eheight - 2 + 'px');
                $('#' + this.element + '-dT' + id).css('border-bottom', this.color + ' solid 2px');
                $('#' + this.element + '-dT' + id).css('position', 'absolute');
                $('#' + this.element + '-dT' + id).css('left', (this.ewidth * rgame) + 'px');
                $('#' + this.element + '-dT' + id).css('top', (this.eheight * increment[rgame]) + 'px');
                $('#' + this.element + '-sT' + id).css('position', 'absolute');
                $('#' + this.element + '-sT' + id).css('bottom', '0px');
                if (((id+1) % 4) == 0) {
                    $('#' + this.element + '-dT' + id).css('width', this.ewidth - 4);
                    $('#' + this.element + '-dT' + id).css('border-right', this.color + ' solid 2px');
                    $('#' + this.element + '-dT' + id).css('border-left', this.color + ' solid 2px');
                } else {
                    $('#' + this.element + '-dT' + id).css('width', this.ewidth);
                }
                increment[rgame] += 1;
            } else {
                if (true) {
                    var game = 0;
                    for (var x = 0; x < ((this.gamecount/2)+2); x++) {
                        if ((id+Math.pow(2, x)) % Math.pow(2, (x + 1)) == 0) {
                            game = x;
                            break;
                        }
                    }
                    var rgame = (this.gamecount - this.log2(this.teams)) + (game-1);
                    
                    if (increment[rgame]===undefined) {
                        increment[rgame] = 0;
                    }
                    
                    $('#' + this.element).append('<div id="' + this.element + '-dRG' + id + '"><span id="' + this.element + '-sRG' + id + '">Game&nbsp;' + id + '</span></div>');
                    $('#' + this.element + '-dRG' + id).css('border-bottom', this.color + ' solid 2px');
                    $('#' + this.element + '-dRG' + id).css('position', 'absolute');
                    
                    var zheight = (this.eheight * Math.pow(2, game));
                    var pvheight = zheight * (increment[rgame]);
                    var offset = (this.eheight * Math.pow(2, game-1)) + 0.5 * this.eheight - zheight;
                    
                    $('#' + this.element + '-dRG' + id).css('height', (zheight - 2) + 'px');
                    $('#' + this.element + '-dRG' + id).css('top', (offset + pvheight) + 'px');
                    $('#' + this.element + '-dRG' + id).css('left', (this.ewidth*rgame) + 'px');
                    $('#' + this.element + '-sRG' + id).css('position', 'absolute');
                    $('#' + this.element + '-sRG' + id).css('bottom', '0px');
                    $('#' + this.element + '-dRG' + id).css('width', this.ewidth);
                    if ((increment[rgame] % 2) == 1) {
                        $('#' + this.element + '-dRG' + id).css('width', this.ewidth - 2);
                        $('#' + this.element + '-dRG' + id).css('border-right', this.color + ' solid 2px');
                    }
                    
                    increment[rgame] += 1;
                }
                
                if (id == this.teams) {
                    if (true) {
                        var game = 0;
                        for (var x = 0; x < ((this.gamecount/2)+2); x++) {
                            if ((id+Math.pow(2, x)) % Math.pow(2, (x + 1)) == 0) {
                                game = x;
                                break;
                            }
                        }
                        var rgame = (this.gamecount - this.log2(this.teams)) + (game-1);
                        
                        if (increment[rgame]===undefined) {
                            increment[rgame] = 0;
                        }
                        
                        $('#' + this.element).append('<div id="' + this.element + '-dSG' + id + '"><span id="' + this.element + '-sSG' + id + '">Game&nbsp;' + id + '</span></div>');
                        $('#' + this.element + '-dSG' + id).css('border-bottom', this.color + ' solid 2px');
                        $('#' + this.element + '-dSG' + id).css('position', 'absolute');
                        
                        var zheight = (this.eheight * Math.pow(2, game-1));
                        var pvheight = zheight * (increment[rgame]);
                        var offset = (this.eheight * Math.pow(2, game-1)) + 0.5 * this.eheight - zheight;
                        
                        $('#' + this.element + '-dSG' + id).css('height', (zheight - 2) + 'px');
                        $('#' + this.element + '-dSG' + id).css('top', (offset + pvheight) + 'px');
                        $('#' + this.element + '-dSG' + id).css('left', (this.ewidth*rgame) + 'px');
                        $('#' + this.element + '-sSG' + id).css('position', 'absolute');
                        $('#' + this.element + '-sSG' + id).css('bottom', '0px');
                        $('#' + this.element + '-dSG' + id).css('width', this.ewidth);
                        if ((increment[rgame] % 2) == 1) {
                            $('#' + this.element + '-dSG' + id).css('width', this.ewidth - 2);
                            $('#' + this.element + '-dSG' + id).css('border-right', this.color + ' solid 2px');
                        }
                    }
                    if (true) {
                        var game = 0;
                        for (var x = 0; x < ((this.gamecount/2)+2); x++) {
                            if ((id+Math.pow(2, x)) % Math.pow(2, (x + 1)) == 0) {
                                game = x;
                                break;
                            }
                        }
                        game += 1;
                        var rgame = (this.gamecount - this.log2(this.teams)) + (game - 1);
                        
                        if (increment[rgame]===undefined) {
                            increment[rgame] = 0;
                        }
                        
                        $('#' + this.element).append('<div id="' + this.element + '-dCG' + id + '"><span id="' + this.element + '-sCG' + id + '">Game&nbsp;' + id + '</span></div>');
                        $('#' + this.element + '-dCG' + id).css('border-bottom', this.color + ' solid 2px');
                        $('#' + this.element + '-dCG' + id).css('position', 'absolute');
                        
                        var zheight = (this.eheight * Math.pow(2, game-1));
                        var pvheight = zheight * (increment[rgame]);
                        var offset = ((this.eheight * Math.pow(2, game-1)) + 0.5 * this.eheight - zheight) - ((this.eheight * Math.pow(2, game-2)/2));
                        
                        $('#' + this.element + '-dCG' + id).css('height', (zheight - 2) + 'px');
                        $('#' + this.element + '-dCG' + id).css('top', (offset + pvheight) + 'px');
                        $('#' + this.element + '-dCG' + id).css('left', (this.ewidth*rgame) + 'px');
                        $('#' + this.element + '-sCG' + id).css('position', 'absolute');
                        $('#' + this.element + '-sCG' + id).css('bottom', '0px');
                        $('#' + this.element + '-dCG' + id).css('width', this.ewidth);
                    }
                }
                
                if (true) {
                    if (true) {
                        var game = 0;
                        for (var x = 0; x < ((this.gamecount/2)+2); x++) {
                            if ((id+Math.pow(2, x)) % Math.pow(2, (x + 1)) == 0) {
                                game = x;
                                break;
                            }
                        }
                        
                        var rgame = (this.log2(this.teams)*2) - ((2*game)+1);
                        rgame += 2;
                        
                        if (increment[rgame]===undefined) {
                            increment[rgame] = 0;
                        }
                        
                        $('#' + this.element).append('<div id="' + this.element + '-dLG' + id + '"><span id="' + this.element + '-sLG' + id + '">Lose&nbsp;' + id + '</span></div>');
                        $('#' + this.element + '-dLG' + id).css('border-bottom', this.color + ' solid 2px');
                        $('#' + this.element + '-dLG' + id).css('position', 'absolute');
                        
                        var zheight = (this.eheight * Math.pow(2, game));
                        var pvheight = zheight * (increment[rgame]);
                        var offset = (this.eheight * Math.pow(2, game-1)) + 0.5 * this.eheight - zheight;
                        
                        if (game >= 2) {
                            offset += (this.eheight * Math.pow(2, game-1))/2;
                        }
                        
                        $('#' + this.element + '-dLG' + id).css('height', (zheight - 2) + 'px');
                        $('#' + this.element + '-dLG' + id).css('top', (offset + pvheight) + 'px');
                        $('#' + this.element + '-dLG' + id).css('left', (this.ewidth*rgame) + 'px');
                        $('#' + this.element + '-sLG' + id).css('position', 'absolute');
                        $('#' + this.element + '-sLG' + id).css('bottom', '0px');
                        $('#' + this.element + '-dLG' + id).css('width', this.ewidth);
                        if ((increment[rgame] % 2) == 1) {
                            $('#' + this.element + '-dLG' + id).css('width', this.ewidth - 2);
                            $('#' + this.element + '-dLG' + id).css('border-left', this.color + ' solid 2px');
                        }
                        
                        increment[rgame] += 1;
                    }
                    
                    if ((id % 4) == 0) {
                        if (true) {
                            var game = 0;
                            for (var x = 0; x < ((this.gamecount/2)+2); x++) {
                                if ((id+Math.pow(2, x)) % Math.pow(2, (x + 1)) == 0) {
                                    game = x;
                                    break;
                                }
                            }
                            var rgame = (this.log2(this.teams)*2) - ((2*game));
                            rgame += 2;
                            
                            if (increment[rgame]===undefined) {
                                increment[rgame] = 0;
                            }
                            
                            $('#' + this.element).append('<div id="' + this.element + '-dGG' + id + '"><span id="' + this.element + '-sGG' + id + '">Ghost&nbsp;' + id + '</span></div>');
                            $('#' + this.element + '-dGG' + id).css('border-bottom', this.color + ' solid 2px');
                            $('#' + this.element + '-dGG' + id).css('position', 'absolute');
                            
                            var zheight = (this.eheight * Math.pow(2, game-1));
                            var pvheight = zheight * (increment[rgame]);
                            var offset = (this.eheight * Math.pow(2, game-1)) + 0.5 * this.eheight - zheight;
                            
                            $('#' + this.element + '-dGG' + id).css('height', (zheight - 2) + 'px');
                            $('#' + this.element + '-dGG' + id).css('top', (offset + pvheight) + 'px');
                            $('#' + this.element + '-dGG' + id).css('left', (this.ewidth*rgame) + 'px');
                            $('#' + this.element + '-sGG' + id).css('position', 'absolute');
                            $('#' + this.element + '-sGG' + id).css('bottom', '0px');
                            $('#' + this.element + '-dGG' + id).css('width', this.ewidth);
                            
                            increment[rgame] += 1;
                        }
                        if (true) {
                            var game = 0;
                            for (x = 0; x < ((this.gamecount/2)+2); x++) {
                                if ((id+Math.pow(2, x)) % Math.pow(2, (x + 1)) == 0) {
                                    game = x;
                                    break;
                                }
                            }
                            var rgame = (this.log2(this.teams)*2) - ((2*game));
                            rgame += 2;
                            
                            if (increment[rgame]===undefined) {
                                increment[rgame] = 0;
                            }
                            
                            var tid = id;
                            if (game < (this.log2(this.teams))) {
                                if ((game % 2) == 1) {
                                    tid = this.teams-id;
                                    if (tid < 0) {
                                        tid += (this.teams*2);
                                    }
                                } else {
                                    tid = (this.teams*2) - id;
                                }
                            }
                            
                            $('#' + this.element).append('<div id="' + this.element + '-dPG' + tid + '"><span id="' + this.element + '-sPG' + tid + '">Pull&nbsp;' + tid + '</span></div>');
                            $('#' + this.element + '-dPG' + tid).css('border-bottom', this.color + ' solid 2px');
                            $('#' + this.element + '-dPG' + tid).css('position', 'absolute');
                            
                            var zheight = (this.eheight * Math.pow(2, game-1));
                            var pvheight = zheight * (increment[rgame]);
                            var offset = (this.eheight * Math.pow(2, game-1)) + 0.5 * this.eheight - zheight;
                            
                            $('#' + this.element + '-dPG' + tid).css('height', (zheight - 2) + 'px');
                            $('#' + this.element + '-dPG' + tid).css('top', (offset + pvheight) + 'px');
                            $('#' + this.element + '-dPG' + tid).css('left', (this.ewidth*rgame) + 'px');
                            $('#' + this.element + '-sPG' + tid).css('position', 'absolute');
                            $('#' + this.element + '-sPG' + tid).css('bottom', '0px');
                            $('#' + this.element + '-dPG' + tid).css('width', this.ewidth);
                            if ((increment[rgame] % 2) == 1) {
                                $('#' + this.element + '-dPG' + tid).css('width', this.ewidth - 2);
                                $('#' + this.element + '-dPG' + tid).css('border-left', this.color + ' solid 2px');
                            }
                            
                            increment[rgame] += 1;
                        }
                    }
                }
            }
        }
        
        if (this.eheight == this.minheight) {
            $('#' + this.element).append('<div id="' + this.element + '-clear">&nbsp;<br></div>');
            $('#' + this.element + '-clear').css('position', 'absolute');
            $('#' + this.element + '-clear').css('top', parseInt(this.eheight) * (parseInt(this.teams)+1));
        }
    }
    
    this.drawConsolation = function() {
        this.gamecount = (this.log2(this.teams)*2)+1;
        this.ewidth = Math.floor(this.pwidth / ((this.gamecount)+2));
        this.eheight = Math.floor(this.pheight / (this.teams+1));
        if (this.ewidth > this.maxwidth) {
            this.ewidth = this.maxwidth;
        } else if (this.ewidth < this.minwidth) {
            this.ewidth = this.minwidth;
        }
        
        if (this.eheight > this.maxheight) {
            this.eheight = this.maxheight;
        } else if (this.eheight < this.minheight) {
            this.eheight = this.minheight;
        }
        
        var increment = new Array();
        for (var id = 1; id < this.teams*2; id++) {
            if ((id % 2) == 1) {
                var game = this.log2(this.teams)+1;
                if (increment[game]===undefined) {
                    increment[game] = 0;
                }
                
                $('#' + this.element).append('<div id="' + this.element + '-dT' + id + '"><span id="' + this.element + '-sT' + id + '">Team&nbsp;' + id + '</span></div>');
                $('#' + this.element + '-dT' + id).css('height', this.eheight - 2 + 'px');
                $('#' + this.element + '-dT' + id).css('border-bottom', this.color + ' solid 2px');
                $('#' + this.element + '-dT' + id).css('position', 'absolute');
                $('#' + this.element + '-dT' + id).css('left', (this.ewidth * game) + 'px');
                $('#' + this.element + '-dT' + id).css('top', (this.eheight * increment[game]) + 'px');
                $('#' + this.element + '-sT' + id).css('position', 'absolute');
                $('#' + this.element + '-sT' + id).css('bottom', '0px');
                if (((id+1) % 4) == 0) {
                    $('#' + this.element + '-dT' + id).css('width', this.ewidth - 4);
                    $('#' + this.element + '-dT' + id).css('border-right', this.color + ' solid 2px');
                    $('#' + this.element + '-dT' + id).css('border-left', this.color + ' solid 2px');
                } else {
                    $('#' + this.element + '-dT' + id).css('width', this.ewidth);
                }
                increment[game] += 1;
            } else {
                if (true) {
                    var game = 0;
                    for (var x = 0; x < ((this.gamecount/2)+2); x++) {
                        if ((id+Math.pow(2, x)) % Math.pow(2, (x + 1)) == 0) {
                            game = x;
                            break;
                        }
                    }
                    var rgame = ((this.log2(this.teams))+1) + (game);
                    
                    if (increment[rgame]===undefined) {
                        increment[rgame] = 0;
                    }
                    
                    $('#' + this.element).append('<div id="' + this.element + '-dWG' + id + '"><span id="' + this.element + '-sWG' + id + '">Game&nbsp;' + id + '</span></div>');
                    $('#' + this.element + '-dWG' + id).css('border-bottom', this.color + ' solid 2px');
                    $('#' + this.element + '-dWG' + id).css('position', 'absolute');
                    
                    var zheight = (this.eheight * Math.pow(2, game));
                    var pvheight = zheight * (increment[rgame]);
                    var offset = (this.eheight * Math.pow(2, game-1)) + 0.5 * this.eheight - zheight;
                    
                    $('#' + this.element + '-dWG' + id).css('height', (zheight - 2) + 'px');
                    $('#' + this.element + '-dWG' + id).css('top', (offset + pvheight) + 'px');
                    $('#' + this.element + '-dWG' + id).css('left', (this.ewidth*rgame) + 'px');
                    $('#' + this.element + '-sWG' + id).css('position', 'absolute');
                    $('#' + this.element + '-sWG' + id).css('bottom', '0px');
                    $('#' + this.element + '-dWG' + id).css('width', this.ewidth);
                    if ((increment[rgame] % 2) == 1) {
                        $('#' + this.element + '-dWG' + id).css('width', this.ewidth - 2);
                        $('#' + this.element + '-dWG' + id).css('border-right', this.color + ' solid 2px');
                    }
                    
                    increment[rgame] += 1;
                }
                
                if (true) {
                    var game = 0;
                    for (var x = 0; x < ((this.gamecount/2)+2); x++) {
                        if ((id+Math.pow(2, x)) % Math.pow(2, (x + 1)) == 0) {
                            game = x;
                            break;
                        }
                    }
                    var rgame = (this.log2(this.teams)) - game;
                    rgame += 1;
                    
                    if (increment[rgame]===undefined) {
                        increment[rgame] = 0;
                    }
                    
                    $('#' + this.element).append('<div id="' + this.element + '-dLG' + id + '"><span id="' + this.element + '-sLG' + id + '">Game&nbsp;' + id + '</span></div>');
                    $('#' + this.element + '-dLG' + id).css('border-bottom', this.color + ' solid 2px');
                    $('#' + this.element + '-dLG' + id).css('position', 'absolute');
                    
                    var zheight = (this.eheight * Math.pow(2, game));
                    var pvheight = zheight * (increment[rgame]);
                    var offset = (this.eheight * Math.pow(2, game-1)) + 0.5 * this.eheight - zheight;
                    
                    $('#' + this.element + '-dLG' + id).css('height', (zheight - 2) + 'px');
                    $('#' + this.element + '-dLG' + id).css('top', (offset + pvheight) + 'px');
                    $('#' + this.element + '-dLG' + id).css('left', (this.ewidth*rgame) + 'px');
                    $('#' + this.element + '-sLG' + id).css('position', 'absolute');
                    $('#' + this.element + '-sLG' + id).css('bottom', '0px');
                    $('#' + this.element + '-dLG' + id).css('width', this.ewidth);
                    if ((increment[rgame] % 2) == 1) {
                        $('#' + this.element + '-dLG' + id).css('width', this.ewidth - 2);
                        $('#' + this.element + '-dLG' + id).css('border-left', this.color + ' solid 2px');
                    }
                    
                    increment[rgame] += 1;
                }
            }
        }
        
        if (this.eheight == this.minheight) {
            $('#' + this.element).append('<div id="' + this.element + '-clear">&nbsp;<br></div>');
            $('#' + this.element + '-clear').css('position', 'absolute');
            $('#' + this.element + '-clear').css('top', parseInt(this.eheight) * (parseInt(this.teams)+1));
        }
    }
    
    this.log2 = function(number) {
        return Math.log(number) / Math.log(2);
    }
}
