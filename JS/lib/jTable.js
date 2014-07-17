/**
 * jTable v1.2 - A HTML5/jQuery Table library with Canvas support
 * Depends: jQuery >= v1.5 ; could be replaced if needed
 * Depends: jCavnas v2.0 ; canvas support, optional if not using canvases
 * Depends: jSuggest.js v0.8 ; Suggested text support
 * 
 * Copyright (C) 2012, 2013 Alex Scheel
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
 *   var table = new jTable();
 *   table.init('div-jtable-id', '/path/to/form/information', false, false, true);
 *   table.setSplits('[,]', '{,}');
 *   table.setUControls(['refresh', 'search']);
 *   table.setLControls(['refresh', 'search']);
 *   table.setTColumns([['ID', 0, 'number'], ['Name', 1, 'text'], ['Score', 2, 'number']]);
 *   table.setKeyField(0);
 *   table.load();
 *   
 * API:
 *   Main:
 *     init(tableid, datauri, editable, deletable, sortable) - initializes
 *                                                             jTable
 *     load() - Starts jTable, loads data, displays
 * 
 *  Config:
 *     setSaveURI(uri) - if editable, location to push changes to
 *     
 *     setCreateURI(uri) - if able to add new rows, location to push changes to
 *     
 *     setDeleteURI(uri) - if able to delete rows, location to push changes to
 *     
 *     setSplits(line-split, row-split) - delimiter to split data by
 *     
 *     setControIDs(upper, lower) - IDs of control elements for table
 *     
 *     setUControls(controls) - Sets array of controls in upper controls area
 *     
 *     setLControls(controls) - Sets array of controls in lower controls area
 *     
 *     setKeyField(id) - Key position in data array
 *     
 *     setBindHandler(handler) - Function to be called when binding events
 *     
 *     setUnbindHandler(handler) - Function to be called when unbinding events
 *     
 *   Getters:
 *     getUControls() - Returns HTML of upper controls
 *     
 *     getLControls() - Returns HTML of lower controls
 *     
 *     getTableHead() - Returns HTML of table head
 *     
 *     getTableContent() - Returns HTML of contents of table
 *     
 *     getTableFoot() - Returns HTML of table foot
 *     
 *     getTable() - Gets entire table
 *     
 *     getColumnFromID() - Get name of column from ID
 *     
 *     getIDFromColumn() - Get ID of column from name
 *     
 *   Update Commands:  
 *     doSort(cid) - Sorts table based on column id (cid)
 *     
 *     dataSorted(cid) - Checks if data is sorted by column (cid)
 *     
 *     doSearchSubmit(element) - Searches based on value of input #element
 *     
 *     newRow() - Adds a row to table for creation of new row
 *     
 *     editRows() - Turns checked rows into editable fields
 *     
 *     editRow(rowid) - Turns row at [rowid] into editable fields
 *     
 *     saveRow(rowid) - Saves updates to row at [rowid]
 *     
 *     deleteRow(rowid) - Deletes row at [rowid]
 *     
 *     refreshTable() - redraw table
 *     
 *     updateData() - (re)loads data from server
 *     
 *     showTable() - Shows table
 *     
 *     hideTable() - Hides table
 *     
 *     drawTable() - Renders table/contents, places in element
 *     
 *   Internal Commands:  
 *     Quicksort functions:
 *       doDataSwap(pos1, pos2)
 *       doSortCompare(a, b)
 *       doQuickSort(bpos, epos, cid)
 *       doQuickSortPartition(bpos, ppos, epos, cid)
 *     
 *     Search functions:
 *       arrayContainsRe(array, regex)
 *       stringDistanceBetween(s1, s2)
 *       arrayContainsFuzzy(array, term)
 *       calculateFuzzyThreshold(string)
 *       searchStiffMatch(bits)
 *       searchKeyValue(bits)
 *       searchLooseMatch(bits)
 *     
 *     Events:
 *       eventCheckAll(event)
 *       eventTriggerNew(event)
 *       eventTriggerEdit(event)
 *       eventTriggerEditRow(event)
 *       eventTriggerDeleteRow(event)
 *       eventTriggerSaveRow(event)
 *       eventTriggerRefresh(event)
 *       eventTriggerSort(event)
 *       eventTriggerSearch(event)
 *       bindEvents()
 *       unbindEvents()
 *       
 *     newRowDrawCanvases()
 *     
 *     newRowBindEvents()
 *     
 *     drawCanvases()
 *     
 *     unhex(text)
 *     
 *     genRange(lower, upper)
**/

function jTable() {
    this.data = [];
    this.duri = "";
    this.dlsplit = "{[,]}";
    this.drsplit = "{(,)}";
    this.telement = "";
    this.tcolumns = [];
    this.ucelement = "";
    this.uecontrols = [];
    this.lcelement = "";
    this.lecontrols = [];
    this.dcount = 0;
    this.editable = false;
    this.saveuri = [];
    this.createuri = [];
    this.deletable = false;
    this.deleteuri = [];
    this.keyfield = 0;
    this.sortable = false;
    this.ldevent = 0;
    this.sdata = [];
    this.drows = [];
    this.bindHandler = function() {};
    this.unbindHandler = function() {};
    
    this.init = function(tableid, datauri, editable, deletable, sortable) {
        this.telement = tableid;
        this.duri = datauri;
        this.editable = editable;
        this.deletable = deletable;
        this.sortable = sortable;
    }

    this.setSaveURI = function(suri) {
        this.saveuri = suri;
    }

    this.setCreateURI = function(curi) {
        this.createuri = curi;
    }

    this.setDeleteURI = function(curi) {
        this.deleteuri = curi;
    }
    
    this.setSplits = function(lsplit, rsplit) {
        this.dlsplit = lsplit;
        this.drsplit = rsplit;
    }
    
    this.setControlIDs = function(upper, lower) {
        this.ucelement = upper;
        this.lcelement = lower;
    }
    
    this.setUControls = function(controls) {
        this.uecontrols = controls;
    }
    
    this.setLControls = function(controls) {
        this.lecontrols = controls;
    }
    
    this.setTColumns = function(columns) {
        this.tcolumns = columns;
    }
    
    this.setKeyField = function(field) {
        this.keyfields = field;
    }
    
    this.setBindHandler = function(handler) {
        this.bindHandler = handler;
    }
    
    this.setUnbindHandler = function(handler) {
        this.unbindHandler = handler;
    }
    
    this.getUControls = function() {
        var result = '';
        for (var control in this.uecontrols) {
            var item = this.uecontrols[control];
            switch (item) {
                case 'new':
                    result += '<button id="' + this.ucelement + '-new" class="jTableUControlsButton">New</button>';
                    break;
                case 'edit':
                    result += '<button id="' + this.ucelement + '-edit" class="jTableUControlsButton">Edit</button>';
                    break;
                case 'refresh':
                    result += '<button id="' + this.ucelement + '-refresh" class="jTableUControlsButton">Refresh</button>';
                    break;
                case 'search':
                    result += '<input id="' + this.ucelement + '-search" class="jTableUControlsInput" type="text" placeholder="Search">';
                    break;
                default:
                    result += '<button id="' + this.ucelement + '-' + item[0] + '" class="jTableUControlsButton">' + item[1] + '</button>';
                    break;
            }
        }
        return result;
    }
    
    this.getLControls = function() {
        var result = '';
        for (var control in this.lecontrols) {
            var item = this.lecontrols[control];
            switch (item) {
                case 'new':
                    result += '<button id="' + this.lcelement + '-new" class="jTableLControlsButton">New</button>';
                    break;
                case 'edit':
                    result += '<button id="' + this.lcelement + '-edit" class="jTableLControlsButton">Edit</button>';
                    break;
                case 'refresh':
                    result += '<button id="' + this.lcelement + '-refresh" class="jTableLControlsButton">Refresh</button>';
                    break;
                case 'search':
                    result += '<input id="' + this.lcelement + '-search" class="jTableLControlsInput" type="text" placeholder="Search">';
                    break;
                default:
                    result += '<button id="' + this.ucelement + '-' + item[0] + '" class="jTableUControlsButton">' + item[1] + '</button>';
                    break;
            }
        }
        return result;
    }
    
    this.getTableHead = function() {
        var result = ' ';
        for (var column in this.tcolumns) {
            var name = this.tcolumns[column][0];
            switch (name) {
                case 'table-checkbox':
                    result += '<td id="' + this.telement + '-thtr-td-checkbox" class="jTableHeadTdCheckbox"><input id="' + this.telement + '-thtr-checkbox" type="checkbox" class="jTableHeadCheckbox"></td>';
                    break;
                case 'jcanvas':
                    result += '<td id="' + this.telement + '-thtr-td-' + column + '-canvas" class="jTableHeadTdCanvas">' + this.tcolumns[column][1] + '</td>';
                    break;
                default:
                    result += '<td id="' + this.telement + '-thtr-td-' + name + '" class="jTableHeadTd"><span id="' + this.telement + '-thtr-td-' + name + '-sort" clas="jTableHeadSpan">' + name + '</span></td>';
                    break;
            }
        }
        return result;
    }
    
    this.getTableContent = function() {
        var result = ' ';
        for (var drpos in this.drows) {
            var rowid = this.drows[drpos];
            var row = this.data[rowid];
            result += '<tr id="' + this.telement + '-tbtr-' + rowid + '" class="jTableBodyTr">';
            
            for (var column in this.tcolumns) {
                var name = this.tcolumns[column][0];
                switch (name) {
                    case 'table-checkbox':
                        result += '<td id="' + this.telement + '-tbtr-' + rowid + '-td-checkbox" class="jTableBodyTdCheckbox"><input id="' + this.telement + '-tbtr-' + rowid + '-checkbox" type="checkbox" class="jTableBodyCheckbox"></td>';
                        break;
                    case 'jcanvas':
                        result += '<td id="' + this.telement + '-tbtr-' + rowid + '-td-' + column + '-canvas" class="jTableBodyTdCanvas"><canvas id="' + this.telement + '-tbtr-' + rowid + '-' + column + '-canvas" width="' + this.tcolumns[column][2][0] + '" height="' + this.tcolumns[column][2][1] + '" class="jTableBodyCanvas">' + this.tcolumns[column][1] + '</canvas></td>';
                        break;
                    default:
                        var pos = this.tcolumns[column][1];
                        var type = this.tcolumns[column][2];
                        var regex = /^\d+$/;
                        if (regex.test(pos)) {
                            if (type != 'hextext') {
                                result += '<td id="' + this.telement + '-tbtr-' + rowid + '-td-' + pos + '" class="jTableBodyTd">' + row[pos].replace('<', '&lt;').replace('>', '&gt;') + '</td>' + "\n";
                            } else {
                                result += '<td id="' + this.telement + '-tbtr-' + rowid + '-td-' + pos + '" class="jTableBodyTd">' + this.unhex(row[pos]).replace('<', '&lt;').replace('>', '&gt;') + '</td>' + "\n";
                            }
                        }
                        break;
                }
            }
            
            result += '</tr>' + "\n";
        }
        if (result == ' ') {
            result = 'No rows to show';
        }
        return result;
    }
    
    this.getTableFoot = function() {
        var result = ' ';
        for (var column in this.tcolumns) {
            var name = this.tcolumns[column][0];
            switch (name) {
                case 'table-checkbox':
                    result += '<td id="' + this.telement + '-tftr-td-checkbox" class="jTableFootTdCheckbox"><input id="' + this.telement + '-tftr-checkbox" type="checkbox" class="jTableFootCheckbox"></td>';
                    break;
                case 'jcanvas':
                    result += '<td id="' + this.telement + '-tftr-td-' + column + '-canvas" class="jTableFootTdCanvas">' + this.tcolumns[column][1] + '</td>';
                    break;
                default:
                    result += '<td id="' + this.telement + '-tftr-td' + name + '" class="jTableFootTd"><span id="' + this.telement + '-tftr-td' + name + '-sort" class="jTableFootSpan">' + name + '</span></td>';
                    break;
            }
        }
        return result;
    }
    
    this.getTable = function() {
        var result = '<table id="' + this.telement + '-table" class="jTable"><thead id="' + this.telement + '-thead" class="jTableHead"><tr id="' + this.telement + '-thtr" class="jTableHeadTr">' + this.getTableHead() + '</tr></thead><tbody id="' + this.telement + '-tbody" class="jTableBody">' + this.getTableContent() + '</tbody><tfoot id="' + this.telement + '-tfoot" class="jTableFoot"><tr id="' + this.telement + '-tftr" class="jTableFootTr">' + this.getTableFoot() + '</tr></tfoot>';
        return result;
    }
    
    this.getColumnFromID = function(id) {
        for (var pos in this.tcolumns) {
            if (this.tcolumns[pos][1] == id) {
                return this.tcolumns[pos][0];
            }
        }
        
        return false;
    }
    
    this.getIDFromColumn = function(column) {
        for (var pos in this.tcolumns) {
            if (this.tcolumns[pos][0].toLowerCase() == column.toLowerCase()) {
                return this.tcolumns[pos][1];
            }
        }
        
        return false;
    }
    
    this.doSort = function(cid) {
        this.unbindEvents();
        this.sdata = [];
        
        var srow = 0;
        for (var drpos in this.drows) {
            var rowid = this.drows[drpos];
            var column = this.data[rowid][cid];
            if (this.tcolumns[cid][2] == 'hextext') {
                column = unhash(column);
            }
            this.sdata[srow] = [rowid, column];
            srow += 1;
        }
        
        if (this.dataSorted(1) == false) {
            this.doQuickSort(0, this.sdata.length-1, 1);
        }
        
        this.drows = [];
        var drow = 0;
        for (var pos in this.sdata) {
            this.drows[drow] = this.sdata[pos][0];
            drow += 1;
        }
        
        this.drawTable();
        this.drawCanvases();
        this.bindEvents();
    }
    
    this.dataSorted = function(cid) {
        for (var rowid = 1; rowid < this.sdata.length; rowid++) {
            if (this.sdata[rowid-1][cid] > this.sdata[rowid][cid]) {
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
    
    this.doSearchSubmit = function(element) {
        this.unbindEvents();
        var query = $('#' + element).val();
        if (query != '') {
            var bits = query.split(' ');
            var rows = [];
            
            if (this.arrayContainsRe(bits, /[:=><]/) == true) {
                rows = this.searchKeyValue(bits);
            } else if (this.arrayContainsRe(bits, /[+-]/) == true) {
                rows = this.searchStiffMatch(bits);
            } else {
                rows = this.searchLooseMatch(bits);
            }
            
            if (rows.length > 0) {
                this.sdata = rows;
                this.doQuickSort(0, this.sdata.length-1, 1);
                rows = this.sdata;
                
                this.drows = [];
                
                for (var pos in rows) {
                    this.drows.push(rows[pos][0]);
                }
            }
        } else {
            this.drows = this.genRange(0, this.dcount-1);
        }
        
        this.drawTable();
        this.drawCanvases();
        this.bindEvents();
        $('#' + element).val(query);
    }
    
    this.arrayContainsRe = function(array, regex) {
        for (var pos in array) {
            if (array[pos].match(regex)) {
                return true;
            }
        }
        return false;
    }
    
    this.stringDistanceBetween = function(s1, s2) {
        s1l = s1.length;
        s2l = s2.length;
        if (s1l < s2l) {
            return this.stringDistanceBetween(s2, s1);
        }
        
        if (s1l == 0) {
            return s2l;
        }
        if (s1l == 0) {
            return s1l;
        }
        
        s1a = s1.split('');
        s2a = s2.split('');
        
        var cost = 0;
        var array = new Array();
        var x = 0;
        var y = 0;
        
        for (x = 0; x <= s1l; x++) {
            array[x] = new Array();
            array[x][0] = x;
        }
        
        for (y = 0; y <= s2l; y++) {
            array[0][y] = [y];
        }
        
        for (x = 1; x <= s1l; x++) {
            for (y = 1; y <= s2l; y++) {
                if (s1a[x-1] == s2a[y-1]) {
                    cost = 0;
                } else {
                    cost = 1;
                }
                array[x][y] = Math.min(array[x-1][y] + 1, array[x][y-1] + 1, array[x-1][y-1] + cost);
                
                if ((x > 1) && (y > 1) && (s1a[x-1] == s2a[y-2]) && (s1a[x-2] == s2a[y-1])) {
                    array[x][y] == Math.min(array[x][y], array[x-2][y-2] + cost);
                }
            }
        }
        
        return array[s1l][s2l];
    }
    
    this.arrayContainsFuzzy = function(array, term) {
        var lowest = 99999;
        var threshold = Math.ceil(Math.sqrt(term.length));
        for (var pos in array) {
            if (array[pos] == term) {
                return 0;
            } else if (array[pos].indexOf(term) != -1) {
                return 0;
            } else {
                var distance = this.stringDistanceBetween(array[pos], term);
                if (distance <= threshold) {
                    if (lowest > distance) {
                        lowest = distance;
                    }
                }
            }
        }
        
        if (lowest === 99999) {
            return false;
        } else {
            return lowest;
        }
    }
    
    this.calculateFuzzyThreshold = function(string) {
        return Math.ceil(Math.sqrt(string.length))+1;
    }
    
    this.searchStiffMatch = function(bits) {
        var rows = [];
        var rpos = 0;
        for (var rowid in this.data) {
            var row = this.data[rowid];
            var rdata = new Array();
            for (var pos in row) {
                rdata[pos] = row[pos];
                for (var tpos in this.tcolumns) {
                    if (this.tcolumns[tpos][1] == pos) {
                        if (this.tcolumns[tpos][2] == 'hextext') {
                            rdata[pos] = this.unhex(row[pos]);
                            break;
                        }
                    }
                }
            }
                        
            var rscore = 0;
            var rscount = 0;
            
            for (var bitpos in bits) {
                var bit = bits[bitpos];
                if (bit.substr(0, 1) == '+') {
                    var score = this.arrayContainsFuzzy(rdata, bit.substr(1));
                    if (score === false) {
                        rscore = -1;
                        break;
                    } else {
                        rscore += score;
                    }
                } else if (bit.substr(0, 1) == '-') {
                    var score = this.arrayContainsFuzzy(rdata, bit.substr(1));
                    if (score !== false) {
                        rscore = -1;
                        break;
                    }
                } else {
                    var score = this.arrayContainsFuzzy(rdata, bit.substr(1));
                    if (score !== false) {
                        rscore += score;
                        rscount -= 10;
                    }
                }
            }
            if (rscore != -1) {
                rscore -= rscount;
                rows[rpos] = [rowid, rscore];
                rpos += 1;
            }
        }
        return rows;
    }
    
    this.searchKeyValue = function(bits) {
        var rows = [];
        var rpos = 0;
        for (var rowid in this.data) {
            var row = this.data[rowid];
            var rdata = new Array();
            for (var pos in row) {
                rdata[pos] = row[pos];
                for (var tpos in this.tcolumns) {
                    if (this.tcolumns[tpos][1] == pos) {
                        if (this.tcolumns[tpos][2] == 'hextext') {
                            rdata[pos] = this.unhex(row[pos]);
                            break;
                        }
                    }
                }
            }
                        
            var rscore = -1;
            var rscount = 0;
            
            for (var bitpos in bits) {
                var bit = bits[bitpos];
                var column = bit.split(/[:=<>]/)[0];
                var cid = this.getIDFromColumn(column);
                if (bit.match(/[:=<>]/)) {
                    if (cid != false) {
                        var svalue = bit.split(/[:=<>]/)[1];
                        if (bit.match(/:/)) {
                            if (rdata[cid] == svalue) {
                                if (rscore == -1) {
                                    rscore = 0;
                                }
                            } else if (rdata[cid].indexOf(svalue) != -1) {
                                if (rscore == -1) {
                                    rscore = 0;
                                }
                                rscore += 1;
                            } else {
                                var score = this.stringDistanceBetween(rdata[cid], svalue);
                                if (score < this.calculateFuzzyThreshold(svalue)) {
                                    if (rscore == -1) {
                                        rscore = 0;
                                    }
                                    rscore += this.stringDistanceBetween(rdata[cid], svalue);
                                } else {
                                    rscore = -1;
                                    break;
                                }
                            }
                        } else if (bit.match(/=/)) {
                            if (rdata[cid] == svalue) {
                                if (rscore == -1) {
                                    rscore = 0;
                                }
                            } else {
                                rscore = -1;
                                break;
                            }
                        } else if (bit.match(/</)) {
                            if ((parseInt(rdata[cid]) == rdata[cid]) && (parseInt(svalue) == svalue)) {
                                var rdint = parseInt(rdata[cid]);
                                var svint = parseInt(svalue);
                                if (rdint <= svalue) {
                                    if (rscore == -1) {
                                        rscore = 0;
                                    }
                                } else {
                                    rscore = -1;
                                    break;
                                }
                            } else {
                                if (rdata[cid].length < svalue.length) {
                                    if (rscore == -1) {
                                        rscore = 0;
                                    }
                                    rscore += (svalue.length - rdata[cid].length);
                                } else if (rdata[cid].length == svalue.length) {
                                    if (rdata[cid] == svalue) {
                                        if (rscore == -1) {
                                            rscore = 0;
                                        }
                                    } else {
                                        if (parseInt(rdata[cid], 256) <= parseInt(svalue, 256)) {
                                            if (rscore == -1) {
                                                rscore = 0;
                                            }
                                        } else {
                                            rscore = -1;
                                            break;
                                        }
                                    }
                                } else {
                                    rscore = -1;
                                    break;
                                }
                            }
                        } else if (bit.match(/>/)) {
                            if ((parseInt(rdata[cid]) == rdata[cid]) && (parseInt(svalue) == svalue)) {
                                var rdint = parseInt(rdata[cid]);
                                var svint = parseInt(svalue);
                                if (rdint >= svalue) {
                                    if (rscore == -1) {
                                        rscore = 0;
                                    }
                                } else {
                                    rscore = -1;
                                    break;
                                }
                            } else {
                                if (rdata[cid].length > svalue.length) {
                                    if (rscore == -1) {
                                        rscore = 0;
                                    }
                                    rscore += (svalue.length - rdata[cid].length);
                                } else if (rdata[cid].length == svalue.length) {
                                    if (rdata[cid] == svalue) {
                                        if (rscore == -1) {
                                            rscore = 0;
                                        }
                                    } else {
                                        if (parseInt(rdata[cid], 256) >= parseInt(svalue, 256)) {
                                            if (rscore == -1) {
                                                rscore = 0;
                                            }
                                        } else {
                                            rscore = -1;
                                            break;
                                        }
                                    }
                                } else {
                                    rscore = -1;
                                    break;
                                }
                            }
                        }
                    }
                } else {
                    if (bit.match(/[+-]/)) {
                        if (bit.substr(0, 1) == '+') {
                            var score = this.arrayContainsFuzzy(rdata, bit.substr(1));
                            if (score === false) {
                                rscore = -1;
                                break;
                            } else {
                                rscore += score;
                            }
                        } else if (bit.substr(0, 1) == '-') {
                            var score = this.arrayContainsFuzzy(rdata, bit.substr(1));
                            if (score !== false) {
                                rscore = -1;
                                break;
                            }
                        } else {
                            var score = this.arrayContainsFuzzy(rdata, bit.substr(1));
                            if (score !== false) {
                                rscore += score;
                                rscount -= 10;
                            }
                        }
                    } else {
                        var score = this.arrayContainsFuzzy(rdata, bit);
                        if (score === false) {
                            rscore += 1;
                        } else {
                            if ((score >= 0) && (score < (bit.length/2))) {
                                if (rscore == -1) {
                                    rscore = 0;
                                }
                                
                                rscore += score;
                                rcount += 1;
                            } else {
                                break;
                            }
                        }
                    }
                }
            }
            
            if (rscore != -1) {
                rows[rpos] = [rowid, rscore];
                rpos += 1;
            }
        }
        return rows;
    }
    
    this.searchLooseMatch = function(bits) {
        var rows = [];
        var rpos = 0;
        for (var rowid in this.data) {
            var row = this.data[rowid];
            var rdata = new Array();
            for (var pos in row) {
                rdata[pos] = row[pos];
                for (var tpos in this.tcolumns) {
                    if (this.tcolumns[tpos][1] == pos) {
                        if (this.tcolumns[tpos][2] == 'hextext') {
                            rdata[pos] = this.unhex(row[pos]);
                            break;
                        }
                    }
                }
            }
                        
            var rscore = -1;
            var rcount = 0;
            
            for (var bitpos in bits) {
                var bit = bits[bitpos];
                var score = this.arrayContainsFuzzy(rdata, bit);
                if (score === false) {
                    rcount += 1.5;
                } else {
                    if ((score >= 0) && (score < (bit.length/2))) {
                        if (rscore == -1) {
                            rscore = 0;
                        }
                        
                        rscore += score;
                        rcount += 1;
                    } else {
                        break;
                    }
                }
            }
            
            if (rscore >= 0) {
                rscore *= rcount;
                rows[rpos] = [rowid, rscore];
                rpos += 1;
            }
        }
        return rows;
    }
    
    this.newRow = function() {
        if (this.editable == true) {
            if ($('#' + this.telement + '-tbtr-new').length == 0) {
                result += '<tr id="' + this.telement + '-tbtr-new" class="jTableBodyTrNew jTableEditing">';
                var result;
                for (var column in this.tcolumns) {
                    var name = this.tcolumns[column][0];
                    switch (name) {
                        case 'table-checkbox':
                            result += '<td id="' + this.telement + '-tbtr-new-td-checkbox" class="jTableBodyTdCheckboxNew jTableEditing">&nbsp;</td>';
                            break;
                        case 'jcanvas':
                            result += '<td id="' + this.telement + '-tbtr-new-td-' + column + '-canvas" class="jTableBodyTdCanvasNew jTableEditing"><canvas id="' + this.telement + '-tbtr-new-' + column + '-canvas" width="' + this.tcolumns[column][2][0] + '" height="' + this.tcolumns[column][2][1] + '" class="jTableBodyCanvasNew jTableEditing">' + this.tcolumns[column][1] + '</canvas></td>';
                            break;
                        default:
                            var pos = this.tcolumns[column][1];
                            var type = this.tcolumns[column][2];
                            if (type == "drop-down") {
                                result += '<td id="' + this.telement + '-tbtr-new-td-' + pos + '" class="jTableBodyTdNew jTableEditing"><select id="' + this.telement + '-tbtr-new-td-' + pos + '-value" class="jTableBodyTdSpanNew jTableEditing"><option disabled="disabled" selected="selected">-----</option>';
                                var ditems = this.tcolumns[column][3];
                                for (var item in ditems) {
                                    result += '<option value="' + ditems[item][1] + '">' + ditems[item][0] + '</option>';
                                }
                                result += '</select></td>';
                            } else {
                                result += '<td id="' + this.telement + '-tbtr-new-td-' + pos + '" class="jTableBodyTdNew jTableEditing"><input id="' + this.telement + '-tbtr-new-td-' + pos + '-value" type="' + pos + '-field" class="jTableBodyTdInputNew jTableEditing"></td>';
                            }
                            break;
                    }
                }
                result += '</tr>';
                $('#' + this.telement + '-tbody').append(result);
                this.newRowDrawCanvases();
                this.newRowBindEvents();
            }
        }
    }
    
    this.newRowDrawCanvases = function() {
        if (this.editable == true) {
            for (var column in this.tcolumns) {
                var name = this.tcolumns[column][success];
                if (name == 'jcanvas') {
                    var codes = this.tcolumns[column][3];
                    var frame = codes;
                    if (codes instanceof Array) {
                        frame = codes[1];
                    }
                    
                    var canvas = document.getElementById(this.telement + '-tbtr-new-' + column + '-canvas');
                    var ctx = canvas.getContext('2d');
                    jCanvasDraw(canvas, ctx, frame);
                }
            }
        }
    }
    
    this.newRowBindEvents = function() {
        if (this.editable == true) {
            for (var column in this.tcolumns) {
                var name = this.tcolumns[column][0];
                if (name == 'jcanvas') {
                    var action = this.tcolumns[column][4];
                    switch (action) {
                        case 'table-edit':
                            $(document).on('click', '#' + this.telement + '-tbtr-new-' + column + '-canvas', { instance: this, rowid: 'new' }, this.eventTriggerSaveRow);
                            break;
                        case 'table-delete':
                            $(document).on('click', '#' + this.telement + '-tbtr-new-' + column + '-canvas', { instance: this, rowid: 'new' }, this.eventTriggerDeleteRow);
                            break;
                    }
                }
            }
        }
    }
    
    this.editRows = function() {
        if (this.editable == true) {
            for (var drpos in this.drows) {
                var rowid = this.drows[drpos];
                if ($('#' + this.telement + '-tbtr-' + rowid + '-checkbox').is(':checked') != false) {
                    this.editRow(rowid);
                }
            }
        }
    }
    
    this.editRow = function(rowid) {
        if ((this.editable == true) && ($('#' + this.telement + '-tbtr-' + rowid + '-td-0').hasClass('jTableBodyEditing') == false)) {
            var row = this.data[rowid];
            for (var column in this.tcolumns) {
                var name = this.tcolumns[column][0];
                switch (name) {
                    case 'table-checkbox':
                        break;
                    case 'jcanvas':
                        var codes = this.tcolumns[column][3];
                        var frame = codes;
                        if (codes instanceof Array) {
                            frame = codes[1];
                        }
                        
                        var canvas = document.getElementById(this.telement + '-tbtr-' + rowid + '-' + column + '-canvas');
                        var ctx = canvas.getContext('2d');
                        jCanvasDraw(canvas, ctx, frame);
                        
                        $(document).off('click', '#' + this.telement + '-tbtr-new-' + column + '-canvas');
                        
                        var action = this.tcolumns[column][4];
                        switch (action) {
                            case 'table-edit':
                                $(document).on('click', '#' + this.telement + '-tbtr-' + rowid + '-' + column + '-canvas', { instance: this, rowid: rowid }, this.eventTriggerSaveRow);
                                break;
                            case 'table-delete':
                                $(document).on('click', '#' + this.telement + '-tbtr-' + rowid + '-' + column + '-canvas', { instance: this, rowid: rowid }, this.eventTriggerDeleteRow);
                                break;
                        }
                        break;
                    default:
                        var type = this.tcolumns[column][2];
                        var pos = this.tcolumns[column][1];
                        
                        if (type == 'drop-down') {
                            var htmltext = '<select id="' +  this.telement + '-tbtr-' + rowid + '-td-' + pos + '-value" class="jTableBodySelect">';
                            var elements = this.tcolumns[column][3];
                            if (row[pos] == '') {
                                htmltext += '<option selected="selected" disabled="disabled">-----</option>';
                            } else {
                                htmltext += '<option disabled="disabled">-----</option>';
                            }
                            for (var position in elements) {
                                var element = elements[position];
                                var value = row[pos].replace('&nbsp;', ' ');
                                if (value == element[0]) {
                                    htmltext += '<option value="' + element[1] + '" selected="selected">' + element[0] + '</option>';
                                } else {
                                    htmltext += '<option value="' + element[1] + '">' + element[0] + '</option>';
                                }
                            }
                            htmltext += '</select>';
                            $('#' +  this.telement + '-tbtr-' + rowid + '-td-' + pos).addClass('jTableEditing');
                            $('#' +  this.telement + '-tbtr-' + rowid + '-td-' + pos).html(htmltext);
                        } else if (type == 'hextext') {
                            $('#' +  this.telement + '-tbtr-' + rowid + '-td-' + pos).addClass('jTableEditing');
                            $('#' +  this.telement + '-tbtr-' + rowid + '-td-' + pos).html('<input id="' +  this.telement + '-tbtr-' + rowid + '-td-' + pos + '-value" type="' + type + '" placeholder="' + this.unhex(row[pos]) + '" class="jTableBodyInputEditing">');
                        } else {
                            $('#' +  this.telement + '-tbtr-' + rowid + '-td-' + pos).addClass('jTableEditing');
                            $('#' +  this.telement + '-tbtr-' + rowid + '-td-' + pos).html('<input id="' +  this.telement + '-tbtr-' + rowid + '-td-' + pos + '-value" type="' + type + '" placeholder="' + row[pos] + '" class="jTableBodyInputEditing">');
                        }
                        break;
                }
            }
        }
    }
    
    this.saveRow = function(rowid) {
        var variables = new Array();
        for (var column in this.tcolumns) {
            var name = this.tcolumns[column][0];
            switch (name) {
                case 'table-checkbox':
                    break;
                case 'jcanvas':
                    break;
                default:
                    var pos = this.tcolumns[column][1];
                    var type = this.tcolumns[column][2];
                    $('#' + this.telement + '-tbtr-' + rowid + '-td-' + pos + '-value').removeClass('jTableInputInvalid');
                    var value = $('#' + this.telement + '-tbtr-' + rowid + '-td-' + pos + '-value').val();
                    variables[pos] = [column, value];
                    break;
            }
        }
        
        var passed = true;
        for (var position in variables) {
            var variable = variables[position][1];
            var column = variables[position][0];
            var type = this.tcolumns[column][2];
            
            switch (type) {
                case 'number':
                    if ((variable.search(/[^0-9]/) != -1) && (variable != '')) {
                        $('#' + this.telement + '-tbtr-' + rowid + '-td-' + position + '-value').addClass('jTableInputInvalid');
                        passed = false;
                    }
                    break;
                case 'email':
                    var emailregex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    if ((emailregex.test(variable) == false) && (variable != '')) {
                        $('#' + this.telement + '-tbtr-' + rowid + '-td-' + position + '-value').addClass('jTableInputInvalid');
                        passed = false;
                    }
                    break;
            }
        }
        
        if (passed != true) {
            alert("Please check your fields.");
            return 0;
        }
        
        if (rowid == 'new') {
            for (var position in variables) {
                var variable = variables[position][1];
                var column = variables[position][0];
                var type = this.tcolumns[column][2];
                
                if (this.keyfield != position) {
                    if (variable == '') {
                        $('#' + this.telement + '-tbtr-' + rowid + '-td-' + position + '-value').addClass('jTableInputInvalid');
                        passed = false;
                    }
                }
            }
            
            if (passed != true) {
                alert("Please check your fields.");
                return 0;
            }
        } else {
            var previous = this.data[rowid];
            var pastKeyfield = previous[this.keyfield];
            
            var different = new Array();
            var diffcount = 0;
            
            for (var position in variables) {
                var variable = variables[position][1];
                var pos = variables[position][0];
                
                if (this.tcolumns[pos][2] != 'drop-down') {
                    if ((variable != previous) && (variable != '')) {
                        different[diffcount] = [this.tcolumns[pos][0], variable];
                        diffcount += 1;
                    }
                }
            }
            
            if (diffcount > 0) {
                if (this.saveuri[1] == 'get') {
                    var arguments = '?keyfield=' + pastKeyfield;
                    for (var position in different) {
                        var diff = different[position];
                        arguments += "&" + diff[0] + "=" + diff[1];
                    }
                    
                    $.ajaxSetup({async:false});
                    $.get(this.saveuri[0] + arguments, function(data) {
                        if (data != 'success') {
                            alert(data);
                        }
                    });
                    $.ajaxSetup({async:true});
                } else if (this.saveuri[1] == 'post') {
                    var arguments = {};
                    arguments['keyfield'] = pastKeyfield;
                    for (var position in different) {
                        var diff = different[position];
                        arguments[diff[0]] = diff[1];
                    }
                    
                    $.ajaxSetup({async:false});
                    $.post(this.saveuri[0], arguments, function(data) {
                        if (data != 'success') {
                            alert(data);
                        }
                    });
                    $.ajaxSetup({async:true});
                }
            }
        }
    }
    
    this.deleteRow = function(rowid) {
        if (this.deletable == true) {
            if (rowid == 'new') {
                $('#' + this.telement + '-tbtr-new').remove();
                unbindEvents();
                bindEvents();
            } else {
                if ($('#' + this.telement + '-tbtr-' + rowid + '-td-0').hasClass('jTableEditing') == true) {
                    row = this.data[rowid];
                    
                    for (var column in this.tcolumns) {
                        var name = this.tcolumns[column][0];
                        switch (name) {
                            case 'table-checkbox':
                                $('#' + this.telement + '-tbtr-' + rowid + '-td-checkbox').removeClass('jTableInputInvalid');
                                $('#' + this.telement + '-tbtr-' + rowid + '-td-checkbox').removeClass('jTableEditing');
                                $('#' + this.telement + '-tbtr-' + rowid + '-td-checkbox').html('<input id="' + this.telement + '-tbtr-' + rowid + '-checkbox" type="checkbox">');
                                break;
                            case 'jcanvas':
                                $('#' + this.telement + '-tbtr-' + rowid + '-td-' + column + '-canvas').removeClass('jTableInputInvalid');
                                $('#' + this.telement + '-tbtr-' + rowid + '-td-' + column + '-canvas').removeClass('jTableEditing');
                                $('#' + this.telement + '-tbtr-' + rowid + '-td-' + column + '-canvas').html('<canvas id="' + this.telement + '-tbtr-' + rowid + '-' + column + '-canvas" width="' + this.tcolumns[column][2][0] + '" height="' + this.tcolumns[column][2][1] + '">' + this.tcolumns[column][1] + '</canvas>');
                                break;
                            default:
                                var pos = this.tcolumns[column][1];
                                var regex = /^\d+$/;
                                if (regex.test(pos)) {
                                    $('#' + this.telement + '-tbtr-' + rowid + '-td-' + pos).removeClass('jTableInputInvalid');
                                    $('#' + this.telement + '-tbtr-' + rowid + '-td-' + pos).removeClass('jTableEditing');
                                    $('#' + this.telement + '-tbtr-' + rowid + '-td-' + pos).html(row[pos]);
                                }
                                break;
                        }
                    }
                    
                    this.unbindEvents();
                    this.drawCanvases();
                    if ($('#' + this.telement + '-tbtr-new').length != 0) {
                        this.newRowBindEvents();
                    }
                    this.bindEvents();
                } else {
                    row = this.data[rowid];
                    if (confirm("Are you sure you want to delete this row (" + row[this.keyfield] + ")?")) {
                        if (this.deleteuri[1] == 'get') {
                            var arguments = '?keyfield=' + row[this.keyfield];
                            
                            $.ajaxSetup({async:false});
                            $.get(this.deleteuri[0] + arguments, function(data) {
                                if (data != 'success') {
                                    alert(data);
                                } else {
                                    alert("Row deleted successfully");
                                }
                            });
                            $.ajaxSetup({async:true});
                            this.refreshTable();
                        } else if (this.deleteuri[1] == 'post') {
                            var arguments = {};
                            arguments['keyfield'] = row[this.keyfield];
                            
                            $.ajaxSetup({async:false});
                            $.post(this.deleteuri[0], arguments, function(data) {
                                if (data != 'success') {
                                    alert(data);
                                } else {
                                    alert("Row deleted successfully");
                                }
                            });
                            $.ajaxSetup({async:true});
                            this.refreshTable();
                        }
                    }
                }
            }
        }
        return false;
    }
    
    this.refreshTable = function() {
        this.unbindEvents();
        this.updateData();
        this.drawTable();
        this.drawCanvases();
        this.bindEvents();
    }
    
    this.updateData = function() {
        this.data = [];
        var ddata = '';
        this.dcount = 0;
        $.ajaxSetup({async:false});
        $.get(this.duri, function(data) {
            ddata = data;
        });
        $.ajaxSetup({async:true});
        var rows = ddata.split(this.dlsplit);
        for (var rid in rows) {
            if (rows[rid] != '') {
                line = rows[rid];
                line = line.split(this.drsplit);
                this.data[this.dcount] = line;
                this.dcount += 1;
            }
        }
        
        this.drows = this.genRange(0, this.dcount-1);
    }
    
    this.showTable = function() {
        $('#' + this.ucelement).show();
        $('#' + this.telement).show();
        $('#' + this.lcelement).show();
    }
    
    this.hideTable = function() {
        $('#' + this.ucelement).hide();
        $('#' + this.telement).hide();
        $('#' + this.lcelement).hide();
    }
    
    this.drawTable = function() {
        if (this.ucelement != '') {
            $('#' + this.ucelement).html(this.getUControls());
        }
        if (this.telement != '') {
            $('#' + this.telement).html(this.getTable());
        }
        if (this.lcelement != '') {
            $('#' + this.lcelement).html(this.getLControls());
        }
    }
    
    this.drawCanvases = function() {
        for (var drpos in this.drows) {
            var rowid = this.drows[drpos];
            
            var row = this.data[rowid];
            for (var column in this.tcolumns) {
                var name = this.tcolumns[column][0];
                if (name == 'jcanvas') {
                    var codes = this.tcolumns[column][3];
                    var frame = codes;
                    if (codes instanceof Array) {
                        frame = codes[0];
                    }
                    
                    var canvas = document.getElementById(this.telement + '-tbtr-' + rowid + '-' + column + '-canvas');
                    var ctx = canvas.getContext('2d');
                    jCanvasDraw(canvas, ctx, frame);
                }
            }
        }
    }
    
    this.eventCheckAll = function(event) {
        var checked = $(event.target).is(':checked');
        for (var i = 0; i < event.data.instance.dcount; i++) {
            $('#' + event.data.instance.telement + '-tbtr-' + i + '-checkbox').attr('checked', checked);
        }
        $('#' + event.data.instance.telement + '-tftr-checkbox').attr('checked', checked);
    }
    
    this.eventTriggerNew = function(event) {
        if (event.data.instance.editable == true) {
            event.data.instance.newRow();
        }
    }
    
    this.eventTriggerEdit = function(event) {
        if (event.data.instance.editable == true) {
            event.data.instance.editRows();
        }
    }
    
    this.eventTriggerEditRow = function(event) {
        if (event.data.instance.editable == true) {
            event.data.instance.editRow(event.data.rowid);
        }
    }
    
    this.eventTriggerDeleteRow = function(event) {
        var ct =  new Date().getTime();
        if ((ct > (this.ldevent+500)) || (this.ldevent == undefined)) {
            if (event.data.instance.deletable == true) {
                event.data.instance.deleteRow(event.data.rowid);
            }
        }
        this.ldevent = ct;
    }
    
    this.eventTriggerSaveRow = function(event) {
        if (event.data.instance.editable == true) {
            event.data.instance.saveRow(event.data.rowid);
        }
    }
    
    this.eventTriggerRefresh = function(event) {
        event.data.instance.refreshTable();
    }
    
    this.eventTriggerSort = function(event) {
        if (event.data.instance.sortable == true) {
            event.data.instance.doSort(event.data.column);
        }
    }
    
    this.eventTriggerSearch = function(event) {
        if (event.which == 13) {
            event.data.instance.doSearchSubmit(event.data.element + '-search');
        }
    }
    
    this.bindEvents = function() {
        this.unbindEvents();
        $(document).on('click', '#' + this.telement + '-thtr-checkbox', { instance: this }, this.eventCheckAll);
        $(document).on('click', '#' + this.telement + '-tftr-checkbox', { instance: this }, this.eventCheckAll);
        
        for (var control in this.uecontrols) {
            var item = this.uecontrols[control];
            switch (item) {
                case 'new':
                    $(document).on('click', '#' + this.ucelement + '-new', { instance: this }, this.eventTriggerNew);
                    break;
                case 'edit':
                    $(document).on('click', '#' + this.ucelement + '-edit', { instance: this }, this.eventTriggerEdit);
                    break;
                case 'refresh':
                    $(document).on('click', '#' + this.ucelement + '-refresh', { instance: this }, this.eventTriggerRefresh);
                    break;
                case 'search':
                    $(document).on('keypress', '#' + this.ucelement + '-search', { instance: this, element: this.ucelement }, this.eventTriggerSearch);
                    break;
            }
        }
        
        for (var control in this.lecontrols) {
            var item = this.lecontrols[control];
            switch (item) {
                case 'new':
                    $(document).on('click', '#' + this.lcelement + '-new', { instance: this }, this.eventTriggerNew);
                    break;
                case 'edit':
                    $(document).on('click', '#' + this.lcelement + '-edit', { instance: this }, this.eventTriggerEdit);
                    break;
                case 'refresh':
                    $(document).on('click', '#' + this.lcelement + '-refresh', { instance: this }, this.eventTriggerRefresh);
                    break;
                case 'search':
                    $(document).on('keypress', '#' + this.lcelement + '-search', { instance: this, element: this.lcelement }, this.eventTriggerSearch);
                    break;
            }
        }
        
        for (var drpos in this.drows) {
            var rowid = this.drows[drpos];
            var row = this.data[rowid];
            for (var column in this.tcolumns) {
                var name = this.tcolumns[column][0];
                if (name == 'jcanvas') {
                    var action = this.tcolumns[column][4];
                    switch (action) {
                        case 'table-edit':
                            $(document).on('click', '#' + this.telement + '-tbtr-' + rowid + '-' + column + '-canvas', { instance: this, rowid: rowid}, this.eventTriggerEditRow);
                            break;
                        case 'table-delete':
                            $(document).on('click', '#' + this.telement + '-tbtr-' + rowid + '-' + column + '-canvas', { instance: this, rowid: rowid}, this.eventTriggerDeleteRow);
                            break;
                    }
                }
            }
        }
        
        if (this.sortable == true) {
            for (var column in this.tcolumns) {
                var name = this.tcolumns[column][0];
                switch (name) {
                    case 'table-checkbox':
                        break;
                    case 'jcanvas':
                        break;
                    default:
                        var cid = this.tcolumns[column][1];
                        $(document).on('click', '#' + this.telement + '-thtr-td-' + name + '-sort', { instance: this, column: cid }, this.eventTriggerSort);
                        $(document).on('click', '#' + this.telement + '-tftr-td-' + name + '-sort', { instance: this, column: cid }, this.eventTriggerSort);
                        break;
                }
            }
        }
        
        this.bindHandler();
    }
    
    this.unbindEvents = function() {
        $(document).off('click', '#' + this.telement + '-thtr-checkbox');
        $(document).off('click', '#' + this.telement + '-tftr-checkbox');
        
        for (var control in this.uecontrols) {
            var item = this.uecontrols[control];
            switch (item) {
                case 'new':
                    $(document).off('click', '#' + this.ucelement + '-new');
                    break;
                case 'edit':
                    $(document).off('click', '#' + this.ucelement + '-edit');
                    break;
                case 'refresh':
                    $(document).off('click', '#' + this.ucelement + '-refresh');
                    break;
                case 'search':
                    $(document).off('keypress', '#' + this.ucelement + '-search');
                    break;
            }
        }
        
        for (var control in this.lecontrols) {
            var item = this.lecontrols[control];
            switch (item) {
                case 'new':
                    $(document).off('click', '#' + this.lcelement + '-new');
                    break;
                case 'edit':
                    $(document).off('click', '#' + this.lcelement + '-edit');
                    break;
                case 'refresh':
                    $(document).off('click', '#' + this.lcelement + '-refresh');
                    break;
                case 'search':
                    $(document).off('keypress', '#' + this.lcelement + '-search');
                    break;
            }
        }
        
        for (var rowid in this.data) {
            var row = this.data[rowid];
            for (var column in this.tcolumns) {
                var name = this.tcolumns[column][0];
                if (name == 'jcanvas') {
                    var action = this.tcolumns[column][4];
                    switch (action) {
                        case 'table-edit':
                            $(document).off('click', '#' + this.telement + '-tbtr-' + rowid + '-' + column + '-canvas');
                            break;
                        case 'table-delete':
                            $(document).off('click', '#' + this.telement + '-tbtr-' + rowid + '-' + column + '-canvas');
                            break;
                    }
                }
            }
        }
        
        if ($('#' + this.telement + '-tbtr-new').length == 0) {
            for (var column in this.tcolumns) {
                var name = this.tcolumns[column][0];
                if (name == 'jcanvas') {
                    var action = this.tcolumns[column][4];
                    switch (action) {
                        case 'table-edit':
                            $(document).off('click', '#' + this.telement + '-tbtr-new-' + column + '-canvas');
                            break;
                        case 'table-delete':
                            $(document).off('click', '#' + this.telement + '-tbtr-new-' + column + '-canvas');
                            break;
                    }
                }
            }
        }
        
        if (this.sortable == true) {
            for (var column in this.tcolumns) {
                var name = this.tcolumns[column][0];
                switch (name) {
                    case 'table-checkbox':
                        break;
                    case 'jcanvas':
                        break;
                    default:
                        $(document).off('click', '#' + this.telement + '-thtr-td-' + name + '-sort');
                        $(document).off('click', '#' + this.telement + '-tftr-td-' + name + '-sort');
                        break;
                }
            }
        }
        
        this.unbindHandler();
    }
    
    this.load = function() {
        this.hideTable();
        this.updateData();
        this.drawTable();
        this.drawCanvases();
        this.bindEvents();
        this.showTable();
    }
    
    this.unhex = function(text) {
        if (text.length % 2 != 0) {
            return;
        } else {
            var result = "";
            var stext = text.split('');
            for (var i = 0; i < text.length; i += 2) {
                result += String.fromCharCode(parseInt("0x" + stext[i] + '' + stext[i+1]));
            }
            return result;
        }
    }
    
    this.genRange = function(lower, upper) {
        var result = [];
        for (var i = lower; i <= upper; i++) {
            result.push(i);
        }
        return result;
    }
}
