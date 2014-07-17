/**
 * jForm v1.4 - A HTML5 Form library 
 * Depends: jQuery v1.5+
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
 *   var form = new jForm();
 *   form.init('div-jform-id', '/path/to/jform/push');
 *   form.setForm([[[0, 0, 0]], [["Title for page", ["Name", "id-element", "text", "Description of element"]]]);
 *   form.setRControls('div-rcontrols-id', ["next"]);
 *   form.setLControls('div-lcontrols-id', ["next"]);
 *   form.setReturnCodes([['1', 'Missing Name', 0]]);
 *   form.setSubmit('/path/on/success');
 *   form.load();
 *   
 * API:
 *   Main:
 *     init(formid, saveuri) - Sets id of form, location to save to
 *     
 *     doNext() - Go to next form page
 *     
 *     doBack() - Go to previous form page
 *     
 *     saveData() - Save page's data
 *     
 *     submit() - Submit form
 *     
 *     load() - Loads form, displays
 *     
 *   Config:
 *     setForm(form) - Sets contents of form from array
 *     
 *     setRControls(id, elements) - Set contents of right controls
 *     
 *     setLControls(id, elements) - Set contents of left controls
 *     
 *     setSubmit(path) - Path on success
 *     
 *     setReturnCodes(codes) - Array with return codes.
 *     
 *   Getters:
 *     getRControls() - Render right controls
 *     
 *     getLControls() - Render left controls
 *     
 *     getPageTitle() - Get title of form page
 *     
 *     getPageDescription() - Get description of form page
 *     
 *     getElement(placeholder, name, type, rtype, title, dynamiccount) - render element
 *     
 *     getPageElements() - Get all elements
 *     
 *     getPage() - Get entire page
 *     
 *   Internal:
 *     Form:
 *       hideForm()
 *       showForm()
 *       toPage()
 *       loadPage()
 *       doSubmit()
 *       handleSubmit()
 *       doDynamic()
 *       verify()
 *       verifyType()
 *       
 *     Events:
 *       eventTriggerBack(event)
 *       eventTriggerDynamic(event)
 *       eventTriggerNext(event)
 *       bindEvents()
 *       unbindEvents()
 *       
 *     Misc:
 *       saveData()
 *       fdata(event)
 *       loadData()
 *       htmlSafe()
**/

function jForm() {
    this.data = {};
    this.mdata = [];
    this.suri = "";
    this.page = 1;
    this.ppage = 0;
    this.npage = 2;
    this.lpage = 3;
    this.phdddown = false;
    this.cdnumber = [];
    this.felement = "";
    this.felements = [];
    this.rcelement = "";
    this.rcelements = [];
    this.rcelement = "";
    this.lcelements = [];
    this.submitSuccess = "";
    this.filedataname = "";
    this.returncodes = [];
    
    this.init = function(formid, saveuri) {
        this.felement = formid;
        this.suri = saveuri;
    }
    
    this.setForm = function(form) {
        this.mdata = form[0];
        this.page = this.mdata[0][0];
        this.fpage = this.mdata[0][0];
        this.ppage = this.mdata[0][0];
        this.npage = this.mdata[0][1];
        this.lpage = this.mdata[0][this.mdata[0].length-1];
        this.felements = form[1];
    }
    
    this.setRControls = function(rcontrols, elements) {
        this.rcelement = rcontrols;
        this.rcelements = elements;
    }
    
    this.setLControls = function(lcontrols, elements) {
        this.lcelement = lcontrols;
        this.lcelements = elements;
    }
    
    this.setSubmit = function(success) {
        this.submitSuccess = success;
    }
    
    this.setReturnCodes = function(codes) {
        this.returncodes = codes;
    }
    
    this.getRControls = function() {
        var result = "";
        for (var pos in this.rcelements) {
            var element = this.rcelements[pos];
            if (element == 'next') {
                if (this.page == this.lpage) {
                    result += '<button id="' + this.rcelement + '-next" class="jform-submit">Submit</button>';
                } else {
                    result += '<button id="' + this.rcelement + '-next" class="jform-next">Next</button>';
                }
            } else if (element == 'back') {
                if (this.page != this.fpage) {
                    result += '<button id="' + this.rcelement + '-back" class="jform-back">Back</button>';
                } else {
                    result += '<button id="' + this.rcelement + '-back" class="jform-gray" disabled="disabled">Back</button>';
                }
            }
        }
        return result;
    }
    
    this.getLControls = function() {
        var result = "";
        for (var pos in this.lcelements) {
            var element = this.lcelements[pos];
            if (element == 'next') {
                if (this.page != this.lpage) {
                    result += '<button id="' + this.lcelement + '-next" class="jform-submit">Submit</button>';
                } else {
                    result += '<button id="' + this.lcelement + '-next" class="jform-next">Next</button>';
                }
            } else if (element == 'back') {
                if (this.page != this.fpage) {
                    result += '<button id="' + this.lcelement + '-back" class="jform-back">Back</button>';
                } else {
                    result += '<button id="' + this.lcelement + '-back" class="jform-gray" disabled="disabled">Back</button>';
                }
            }
        }
        return result;
    }
    
    this.getPageTitle = function() {
        return '<h3 class="jform-ptitle">' + this.htmlSafe(this.felements[this.page][0]) + '</h3>';
    }
    
    this.getPageDescription = function() {
        return '<p class="jform-pdesc">' + this.htmlSafe(this.felements[this.page][1]) + '</p>';
    }
    
    this.getElementDynamic = function(element, dynamic) {
        var result = "";
        if (element == undefined) {
            element = "HERE-I-AM-PARTY";
        }
        
        if (element.constructor == Array) {
            for (var pos in element) {
                var delement = element[pos]; 
                if (delement == 'jform-dnumber') {
                    result += dynamic;
                } else {
                    result += delement;
                }
            }
        } else {
            result = element;
        }
        return result;
    }
    
    this.getElement = function(placeholder, name, type, rtype, title, dynamiccount) {
        var result = "";
        switch (rtype) {
            case "box-switch":
                result += '<h4 class="jform-select-title" title="' + this.htmlSafe(title) + '">' + this.htmlSafe(placeholder) + '</h4><select class="jform-select" name="' + name + '" id="jform-' + name + '" title="' + this.htmlSafe(title) + '">';
                for (var tpos in type[1]) {
                    var telement = type[1][tpos];
                    var tname = telement[0];
                    var tvalue = telement[1];
                    var otitle = "";
                    
                    if (telement.length == 3) {
                        otitle = 'title="' + this.htmlSafe(telement[2])  + '" ';
                    }
                    
                    if (tvalue != 'jform-disabled') {
                        result += '<option class="jform-option" ' + otitle + 'value="' + tvalue + '">' + this.htmlSafe(tname) + '</option>';
                    } else {
                        result += '<option class="jform-option" ' + otitle + 'value="" disabled="disabled" selected="selected">' + this.htmlSafe(tname) + '</option>';
                    }
                }
                result += '</select><br><br>';
                break;
            case "textarea":
                result += '<textarea class="jform-textarea" placeholder="' + this.htmlSafe(placeholder) + '" name="' + name + '" id="jform-' + name + '" title="' + this.htmlSafe(title) + '"></textarea><br><br>';
                break;
            case "dynamic-drop-down":
                result += '<h4 class="jform-dynamic-title" title="' + this.htmlSafe(title) + '">' + this.htmlSafe(placeholder) + '</h4><select class="jform-dynamic-select" name="' + name + '" id="jform-' + name + '" title="' + this.htmlSafe(title) + '">';
                for (var tpos in type[1]) {
                    var telement = type[1][tpos];
                    var tname = telement[0];
                    var tvalue = telement[1];
                    var otitle = "";
                    
                    if (telement.length == 3) {
                        otitle = 'title="' + this.htmlSafe(telement[2])  + '" ';
                    }
                    
                    if (tvalue != 'jform-disabled') {
                        result += '<option class="jform-dynamic-option" ' + otitle + 'value="' + tvalue + '">' + this.htmlSafe(tname) + '</option>';
                    } else {
                        result += '<option class="jform-dynamic-option" ' + otitle + 'value="" disabled="disabled" selected="selected">' + this.htmlSafe(tname) + '</option>';
                    }
                }
                var dynamic = "";
                if (this.data[name] != '') {
                    var contents = type[2];
                    for (var i = 1; i <= this.data[name]; i++) {
                        var dplaceholder = this.getElementDynamic(contents[0], i);
                        var dname = this.getElementDynamic(contents[1], i);
                        var dtype = contents[2];
                        var drtype = contents[2];
                        if (drtype.constructor == Array) {
                            drtype = dtype[0];
                        }
                        var dtitle = this.getElementDynamic(contents[3], i);
                        dynamic += this.getElement(dplaceholder, dname, dtype, drtype, dtitle, i);
                    }
                }
                result += '</select><br><br><section id="jform-dynamic-section">' + dynamic + '</section>';
                break;
            case "file":
                var src = "";
                if ((this.data[name] != '') && (this.data[name] != undefined)) {
                    src = '<img src="' + this.data[name] + '" width="50px" height="50px" alt="' + this.data[name].length + 'b">';
                }
                
                result += '<h4 class="jform-select-title" title="' + this.htmlSafe(title) + '">' + this.htmlSafe(placeholder) + '</h4><input class="jform-file" type="' + type + '" name="' + name + '" id="jform-' + name + '" title="' + this.htmlSafe(title) + '"><div class="jform-preview" id="jform-' + name + '-preview">' + src + '</div><br><br>';
                break;
            case "section":
                result += '<section id="jform-' + name + '" class="jform-section"><header class="jform-header"><h4>' + placeholder + '</h4><p>' + title + '</p></header><article class="jform-article">';
                for (var pos in type[1]) {
                    var selement = type[1][pos];
                    var splaceholder = this.getElementDynamic(selement[0], dynamiccount);
                    var sname = this.getElementDynamic(selement[1], dynamiccount);
                    var stype = selement[2];
                    var srtype = selement[2];
                    
                    if (srtype.constructor == Array) {
                        srtype = this.getElementDynamic(stype[0], dynamiccount);
                    }
                    
                    var stitle = this.getElementDynamic(selement[3], dynamiccount);
                    if ((selement[3] == true) || (selement[3] == false)) {
                        stitle = "";
                    }
                    
                    result += this.getElement(splaceholder, sname, stype, srtype, stitle, dynamiccount);
                }
                result += '</article></section>';
                break;
            case "number":
                var limits;
                if (type.length == 3) {
                    var nmin = type[1];
                    var nmax = type[2];
                    if (nmax < nmin) {
                        var t = nmax;
                        nmax = nmin;
                        nmin = t;
                    }
                    limits = ' max="' + nmax + '" min="' + nmin + '"'
                }
                
                result += '<input class="jform-input" type="' + type + '" placeholder="' + this.htmlSafe(placeholder) + '" name="' + name + '"' + limits + ' id="jform-' + name + '" title="' + this.htmlSafe(title) + '"><br><br>';
                break;
            default:
                result += '<input class="jform-input" type="' + type + '" placeholder="' + this.htmlSafe(placeholder) + '" name="' + name + '" id="jform-' + name + '" title="' + this.htmlSafe(title) + '"><br><br>';
                break;
        }
        return result;
    }
    
    this.getPageElements = function() {
        var result = "";
        this.phdddown = false;
        for (var pos in this.felements[this.page][2]) {
            var element = this.felements[this.page][2][pos];
            var placeholder = element[0];
            var name = element[1];
            var type = element[2];
            var rtype = element[2];
            if (rtype.constructor == Array) {
                rtype = type[0];
            }
            
            var title = element[3];
            if ((title == true) || (title == false)) {
                title = "";
            }
            
            result += this.getElement(placeholder, name, type, rtype, title, 0);
        }
        return result;
    }
    
    this.getPage = function() {
        var result = '<div class="jform-page-title">' + this.getPageTitle() + this.getPageDescription() + '</div><div class="jform-page-elements">' + this.getPageElements() + '</div>';
        return result;
    }
    
    this.hideForm = function() {;
        $('#' + this.felement).hide();
        $('#' + this.lcelement).hide();
        $('#' + this.rcelement).hide();
    }
    
    this.showForm = function() {
        $('#' + this.lcelement).show();
        $('#' + this.rcelement).show();
        $('#' + this.felement).show();
    }
    
    this.loadPage = function() {
        $('#' + this.lcelement).html(this.getLControls());
        $('#' + this.rcelement).html(this.getRControls());
        $('#' + this.felement).html(this.getPage());
        
        this.loadData();
    }
    
    this.toPage = function(page) {
        this.page = page;
        if (this.page == this.fpage) {
            this.ppage = this.fpage;
            this.npage = this.page + 1;
        } else if (this.page == this.lpage) {
            this.npage = this.lpage;
            this.ppage = this.page - 1;
        } else {
            this.npage = this.page + 1;
            this.ppage = this.page - 1;
        }
        
        this.load();
    }
    
    this.doSubmit = function() {
        var adata = "";
        for (var pos in this.data) {
            if (adata == "") {
                adata += pos + '=' + this.data[pos];
            } else {
                adata += '&' + pos + '=' + this.data[pos];
            }
        }
        
        $.get(this.suri + "?" + adata, $.proxy(this.handleSubmit, this));
    }
    
    this.handleSubmit = function(data) {
        for (var pos in this.returncodes) {
            var code = this.returncodes[pos];
            if (data == code[0]) {
                if (code[1] == 'jform-success') {
                    this.data = {};
                    window.location = this.submitSuccess;
                } else {
                    alert(code[1]);
                    this.toPage(code[2]);
                }
            }
        }
    }
    
    this.doBack = function() {
        this.saveData();
        
        if (this.page >= this.fpage) {
            this.npage = this.page;
            this.page = this.ppage;
            if (this.ppage > this.fpage) {
                this.ppage -= 1;
            }
            
            this.load();
        }
    }
    
    this.doNext = function() {
        this.saveData();
        
        if (this.page <= this.lpage) {
            this.ppage = this.page;
            this.page = this.npage;
            if (this.npage < this.lpage) {
                this.npage += 1;
            }
        
            this.load();
        } else {
            this.submit();
        }
    }
    
    this.doDynamic = function(element) {
        this.saveData();
        this.loadPage();
    }
    
    this.eventTriggerBack = function(event) {
        if (event.data.instance.page != event.data.instance.fpage) {
            event.data.instance.doBack();
        }
    }
    
    this.eventTriggerNext = function(event) {
        if (event.data.instance.page != event.data.instance.lpage) {
            event.data.instance.doNext();
        } else {
            event.data.instance.submit();
        }
    }
    
    this.eventTriggerDynamic = function(event) {
        event.data.instance.doDynamic(event.data.id);
    }
    
    this.bindEvents = function() {
        this.unbindEvents();
        
        for (var pos in this.rcelements) {
            var element = this.rcelements[pos];
            if (element == 'next') {
                $(document).on('click', '#' + this.rcelement + '-next', { instance: this }, this.eventTriggerNext);
            } else if (element == 'back') {
                $(document).on('click', '#' + this.rcelement + '-back', { instance: this }, this.eventTriggerBack);
            }
        }
        
        for (var pos in this.lcelements) {
            var element = this.lcelements[pos];
            if (element == 'next') {
                $(document).on('click', '#' + this.lcelement + '-next', { instance: this }, this.eventTriggerNext);
            } else if (element == 'back') {
                $(document).on('click', '#' + this.lcelement + '-back', { instance: this }, this.eventTriggerBack);
            }
        }
        
        
        for (var pos in this.felements[this.page][2]) {
            var element = this.felements[this.page][2][pos];
            var name = this.getElementDynamic(element[1]);
            var type = element[2];
            var rtype = element[2];
            
            if (rtype.constructor == Array) {
                rtype = type[0];
            }
            
            if (rtype == "dynamic-drop-down") {
                $(document).on('change', '#jform-' + name, { instance: this, id: 'jform-' + name }, this.eventTriggerDynamic);
            }
        }
    }
    
    this.unbindEvents = function() {
        for (var pos in this.rcelements) {
            var element = this.rcelements[pos];
            if (element == 'next') {
                $(document).off('click', '#' + this.rcelement + '-next');
            } else if (element == 'back') {
                $(document).off('click', '#' + this.rcelement + '-back');
            }
        }
        
        for (var pos in this.lcelements) {
            var element = this.lcelements[pos];
            if (element == 'next') {
                $(document).off('click', '#' + this.lcelement + '-next');
            } else if (element == 'back') {
                $(document).off('click', '#' + this.lcelement + '-back');
            }
        }
        
        
        for (var pos in this.felements[this.page][2]) {
            var element = this.felements[this.page][2][pos];
            var name = this.getElementDynamic(element[1], 0);
            var type = element[2];
            var rtype = element[2];
            
            if (rtype.constructor == Array) {
                rtype = type[0];
            }
            
            if (rtype == "dynamic-drop-down") {
                $(document).off('change', '#jform-' + name);
            }
        }
    }
    
    this.submit = function() {
        this.saveData();
        var result = this.verify();
        if (result === true) {
            this.doSubmit();
        } else {
            this.toPage(result);
        }
    }
    
    this.verify = function() {
        for (var page = this.fpage; page <= this.lpage; page++) {
            for (var pos in this.felements[page][2]) {
                var element = this.felements[page][2][pos];
                var name = this.getElementDynamic(element[1], 0);
                var type = element[2];
                var rtype = element[2];
                
                if (rtype.constructor == Array) {
                    rtype = type[0];
                }
                
                var title = element[3];
                var required;
                if ((title == true) || (title == false)) {
                    required = title;
                    title = "";
                } else {
                    required = element[4];
                }
                
                var id = "jform-" + name;
                
                if ((this.data[name] == '') && (required)) {
                    return page;
                }
                
                if (rtype == 'dynamic-drop-down') {
                    for (var i = 1; i <= this.data[name]; i++) {
                        for (var pos in type[2][2][1]) {
                            var selement = type[2][2][1][pos];
                            var nname = this.getElementDynamic(selement[1], i);
                            var ntitle = selement[3];
                            var nrequired;
                            if ((ntitle == true) || (ntitle == false)) {
                                nrequired = ntitle;
                                ntitle = "";
                            } else {
                                nrequired = selement[4];
                            }
                            
                            if ($('#jform-' + nname).length > 0) {
                                if ((this.data[nname] == '') && (nrequired)) {
                                    return page;
                                }
                                
                                if (this.verifyType(rtype, type, this.data[nname]) == false) {
                                    return page;
                                }
                            }
                        }
                    }
                } else {
                    if (this.verifyType(rtype, type, this.data[name]) != true) {
                        return page;
                    }
                }
            }
        }
        return true;
    }
    
    this.verifyType = function(rtype, type, data) {
        if ((data == '') || (data == undefined)) {
            return true;
        }
        
        switch (rtype) {
            case "number":
                if (data.match(/^[0-9]+$/).length > 0) {
                    if (type.length == 3) {
                        var nmin = type[1];
                        var nmax = type[2];
                        if (nmax < nmin) {
                            var t = nmax;
                            nmax = nmin;
                            nmin = t;
                        }
                        if ((nmin < data) && (data < nmax)) {
                            return true;
                        } else {
                            return false;
                        }
                    }
                    return true;
                }
                break;
            case "url":
                if (data.match(/^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i) == null) {
                    return false;
                } else {
                    return true;
                }
                break;
            default: 
                if (data != '') {
                    return true;
                }
                break;
        }
        return false;
    }
    
    this.saveData = function() {
        for (var pos in this.felements[this.page][2]) {
            var element = this.felements[this.page][2][pos];
            var name = this.getElementDynamic(element[1], 0);
            var type = element[2];
            var rtype = element[2];
            
            if (rtype.constructor == Array) {
                rtype = type[0];
            }
            
            var id = "jform-" + name;
            
            if (rtype == 'file') {
                var file = document.getElementById(id).files[0];
                if (file) {
                    var reader = new FileReader();
                    this.filedataname = name;
                    reader.onload = this.fdata;
                    reader.jforminstance = this;
                    reader.readAsDataURL(file);
                }
            } else {
                this.data[name] = $('#' + id).val();
                
                if (rtype == 'dynamic-drop-down') {
                    for (var i = 1; i <= this.data[name]; i++) {
                        for (var pos in type[2][2][1]) {
                            var selement = type[2][2][1][pos];
                            var nname = this.getElementDynamic(selement[1], i);
                            if ($('#jform-' + nname).length > 0) {
                                this.data[nname] = $('#jform-' + nname).val();
                            }
                        }
                    }
                }
            } 
        }
    }
    
    this.fdata = function(event) {
        this.jforminstance.data[this.jforminstance.filedataname] = event.target.result;
    }
    
    this.loadData  = function() {
        for (var pos in this.felements[this.page][2]) {
            var element = this.felements[this.page][2][pos];
            var name = this.getElementDynamic(element[1], 0);
            var type = element[2];
            var rtype = element[2];
            
            if (rtype.constructor == Array) {
                rtype = type[0];
            }
            
            if (rtype != 'file') {
                var id = "jform-" + name;
                $('#' + id).val(this.data[name]);
            }
            
            if (rtype == 'dynamic-drop-down') {
                for (var i = 1; i <= this.data[name]; i++) {
                    for (var pos in type[2][2][1]) {
                        var selement = type[2][2][1][pos];
                        var nname = this.getElementDynamic(selement[1], i);
                        if ($('#jform-' + nname).length > 0) {
                            $('#jform-' + nname).val(this.data[nname]);
                        }
                    }
                }
            }
        }
    }
    
    this.htmlSafe = function(zinput) {
        var r = zinput.replace('&', '&amp;').replace('<', '&lt;').replace('>', '&gt;');
        return r;
    }
    
    this.load = function() {
        this.hideForm();
        this.unbindEvents();
        this.loadPage();
        this.bindEvents();
        this.showForm();
    }
}
