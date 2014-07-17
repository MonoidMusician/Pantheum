/**
 * jSuggest v0.8 - Just another dynamic suggestion library using jQuery
 * Depends: jQuery >= 1.5
 * 
 * Copyright (C) 2013 Alex Scheel
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

function jSuggest() {
    this.element = '';
    this.data = [];
    this.duri = '';
    this.splits = ',';
    this.casing = false;
    this.display = 6;
    this.displaying = [];
    this.datatype = 'linear';
    this.filter = 'quick';
    this.error = 'error';
    this.matching = 'contains';
    this.focus = -1;
    this.sdata = [];
    
    this.init = function(element) {
        this.element = element;
    }
    
    this.setCasing = function(casing) {
        this.casing = casing;
    }
    
    this.setFilter = function(filter) {
        this.filter = filter;
    }
    
    this.setMatching = function(matching) {
        this.matching = matching;
    }
    
    this.setDataURI = function(uri) {
        this.duri = uri;
    }
    
    this.setData = function(data) {
        this.data = data;
    }
    
    this.setDelimiters = function(delimiter) {
        this.splits = delimiter;
    }
    
    this.setError = function(error) {
        this.error = error;
    }
    
    this.setDisplay = function(display) {
        this.display = display;
    }
    
    this.addData = function(data) {
        this.data.push(data);
    }
    
    this.getData = function() {
        var ddata = '';
        $.ajaxSetup({async:false});
        $.get(this.duri, function(data) {
            ddata = data;
        });
        $.ajaxSetup({async:true});
        
        if (ddata != this.error) {
            this.data = ddata.split(this.splits);
        }
    }
    
    
    
    this.sortMatrix = function(array, column) {
        this.sdata = array;
        
        if (this.isSorted(column) == false) {
            tmp = this.doQuickSort(0, this.sdata.length-1, 1);
        }
        
        return this.sdata;
    }
    
    this.isSorted = function(column) {
        for (var id = 1; id < this.sdata.length; id++) {
            if (this.sdata[id][column] > this.sdata[id][column]) {
                return false;
            }
        }
    }
    
    this.doDataSwap = function(pos1, pos2) {
        var tmp = this.sdata[pos1];
        this.sdata[pos1] = this.sdata[pos2];
        this.sdata[pos2] = tmp;
    }
    
    this.doSortCompare = function(a, b) {
        if ((isNaN(a) == true) && (isNaN(b) == true)) {
            return (a < b);
        } else {
            return (parseInt(a) < parseInt(b));
        }
    }
    
    this.doQuickSort = function(bpos, epos, cid) {
        if (epos > bpos) {
            var ppos = bpos + Math.ceil((epos - bpos) * 0.5);
            ppos = this.doQuickSortPartition(bpos, ppos, epos, cid);
            this.doQuickSort(bpos, ppos-1, cid);
            this.doQuickSort(ppos + 1, epos, cid);
        }
    }
    
    this.doQuickSortPartition = function(bpos, ppos, epos, cid) {
        var tpvar = this.sdata[ppos][cid];
        var slocation = bpos;
        this.doDataSwap(ppos, epos);
        for (var i = bpos; i < epos; ++i) {
            if (this.doSortCompare(this.sdata[i][cid], tpvar) == true) {
                this.doDataSwap(i, slocation);
                slocation += 1;
            }
        }
        this.doDataSwap(epos, slocation);
        
        return slocation;
    }
    
    this.doSearch = function(term) {
        if (this.filter == 'quick') {
            this.doContainsQuick(term);
        } else if (this.filter == 'ranked') {
            this.doContainsRanked(term);
        }
    }
    
    this.doContainsQuick = function(term) {
        this.displaying = [];
        
        for (var pos in this.data) {
            var loc = '';
            if (this.casing == true) {
                loc = this.data[pos].indexOf(term);
            } else {
                loc = this.data[pos].toLowerCase().indexOf(term.toLowerCase());
            }
            
            if (loc != -1) {
                this.displaying.push(pos);
                if (this.displaying.length == this.display) {
                    break;
                }
            }
        }
    }
    
    this.doContainsRanked = function(term) {
        this.displaying = [];
        var tmp = [];
        var zeros = [];
        for (var pos in this.data) {
            var loc = '';
            if (this.casing == true) {
                loc = this.data[pos].indexOf(term);
            } else {
                loc = this.data[pos].toLowerCase().indexOf(term.toLowerCase());
            }
            
            if (loc != -1) {
                tmp.push([pos, loc]);
                if (loc == 0) {
                    zeros.push(pos);
                    if (zeros.length >= this.display) {
                        break;
                    }
                }
            }
        }
        
        if (zeros.length >= this.display) {
            for (var i = 0; i < this.display; i++) {
                this.displaying.push(zeros[i]);
            }
        } else {
            tmp = this.sortMatrix(tmp, 1);
            
            var end = this.display;
            if (tmp.length < end) {
                end = tmp.length;
            }
            
            for (var i = 0; i < end; i++) {
                this.displaying.push(tmp[i][0]);
            }
        }
    }
    
    this.update = function() {
        var term = $('#' + this.element).val();
        if (term != '') {
            this.doSearch(term);
            this.showResults();
            this.focus = -1;
        } else {
            this.hide();
            this.displaying = [];
        }
    }
    
    this.showResults = function() {
        this.show();
        $('#' + this.element + '-suggest').html('');
        for (var pos in this.displaying) {
            $('#' + this.element + '-suggest').append('<div id="' + this.element + '-suggest-' + pos + '" class="jsuggest-suggestion">' + this.data[this.displaying[pos]] + '</div>');
        }
        
        this.bindDisplayEvents();
        
        if (this.displaying.length == 0) {
            this.hide();
        }
    }
    
    this.focusNext = function() {
        this.focus = (this.focus+1) % this.display;
        this.updateFocus();
    }
    
    this.focusPrev = function() {
        this.focus -= 1;
        if (this.focus < 0) {
            this.focus = 0;
        }
        
        this.updateFocus();
    }
    
    this.updateFocus = function() {
        $('.jsuggest-suggestion').removeClass('jsuggest-active');
        if (this.displaying != []) {
            $('#' + this.element + '-suggest-' + this.focus).addClass('jsuggest-active');
        }
    }
    
    this.click = function(pos) {
        if (pos > -1) {
            var dpos = this.displaying[pos];
            this.focus = -1;
            this.displaying = [];
            this.hide();
            
            $('#' + this.element).val(this.data[dpos]);
        }
    }
    
    this.eventTriggerChange = function(event) {
        if (event.which == 40) {
            event.data.instance.focusNext();
        } else if (event.which == 38) {
            event.data.instance.focusPrev();
        } else if (event.which == 13) {
            event.data.instance.click(event.data.instance.focus);
        } else {
            event.data.instance.update();
        }
    }
    
    this.eventTriggerClick = function(event) {
        event.data.instance.click(event.data.position);
    }
    
    this.eventTriggerHover = function(event) {
        event.data.instance.focus = -1;
        $('.jsuggest-suggestion').removeClass('jsuggest-active');
    }
    
    this.bindEvents = function() {
        this.unbindEvents();
        
        $(document).on('keyup', '#' + this.element, { instance: this }, this.eventTriggerChange);
        $(document).on('mouseenter', '#' + this.element + '-suggest', { instance: this }, this.eventTriggerHover);
    }
    
    this.unbindEvents = function() {
        this.unbindDisplayEvents();
        $(document).off('change', '#' + this.element, { instance: this }, this.eventTriggerChange);
        $(document).off('mouseenter', '#' + this.element + '-suggest');
    }
    
    this.bindDisplayEvents = function() {
        this.unbindDisplayEvents();
        
        for (var pos in this.displaying) {
            $(document).on('click', '#' + this.element + '-suggest-' + pos, { instance: this, position: pos }, this.eventTriggerClick);
        }
    }
    
    this.unbindDisplayEvents = function() {
        for (var pos = 0; pos < this.display; pos++) {
            $(document).off('click', '#' + this.element + '-suggest-' + pos);
        }
    }
    
    this.wrap = function() {
        $('#' + this.element).wrap('<div id="' + this.element + '-wrapper" class="jsuggest-wrapper" />');
        $('#' + this.element + '-wrapper').append('<div id="' + this.element + '-suggest" class="jsuggest-suggest"></div>');
        $('#' + this.element + '-wrapper').css('position', 'relative');
        $('#' + this.element + '-wrapper').css('display', 'inline');
        $('#' + this.element + '-suggest').css('position', 'absolute');
        $('#' + this.element + '-suggest').css('left', '0px');
        $('#' + this.element + '-suggest').css('width', $('#' + this.element).width() + 'px');
        
        this.hide();
    }
    
    this.hide = function() {
        this.unbindDisplayEvents();
        $('#' + this.element + '-suggest').hide();
    }
    
    this.show = function() {
        $('#' + this.element + '-suggest').show();
        this.bindDisplayEvents();
    }
    
    this.load = function() {
        if (this.duri != '') {
            this.getData();
        }
        
        this.wrap();
        this.bindEvents();
    }
}
