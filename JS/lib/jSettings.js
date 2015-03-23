/**
 * jSettings v1.3 - A dynamic settings library with jQuery
 * Depends: jQuery >= 1.5
 * 
 * Copyright (C) 2013,2014 Alex Scheel
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
 *   var settings = new jSettings();
 *   settings.init('div-jsettings-id', '/path/to/settings/load.php');
 *   settings.setSettings(["jsettings-element", "Username", 'username', 'text', 'regular'], ["jsettings-space"], ["jsettings-element", "Password", 'password', 'password', 'hex']]);
 *   settings.setSaveURI('/path/to/saveuri.php');
 *   settings.setSplit(':');
 *   settings.setSaveBehavior('both');
 *   settings.setLabels(true);
 *   settings.load();
 *   
 * API:
 *   Main:
 *     init(element, uri) - Set page element, settings loading uri
 *     
 *     load() - Starts jSettings, loads data, displays
 *     
 *   Config:
 *     setSettings(settings) - Set settings options from array.
 *     
 *     setSaveURI(suri) - Set saving uri
 *     
 *     setURLAppend(data) - GET options to append to queries
 *     
 *     setSplit(split) - Delimiter for data loading
 *     
 *     setSaveBehavior(button|automatic|both) - Set save behavior
 *     
 *     setSuccess(value) - Result of successful save
 *     
 *     setLabels(true|false) - Show labels?
 *     
 *     setBindHandler(handler) - Called before binds
 *     
 *     setUnbindHandler(handler) - Called before unbinds
 *     
 *     showSettings(),hideSettings() - Show/hide settings
 *     
 *   Internal:
 *     Data loading:
 *       getData()
 *       getDataFromSetting()
 *       pushData()
 *       pushDataFromSetting()
 *       saveData()
 *       saveDataFromSetting()
 *       loadData()
 *       loadDataFromSetting()
 *       
 *     Events:
 *       eventTriggerSave(event)
 *       bindEvents()
 *       bindEventsFromSetting()
 *       unbindEvents()
 *       unbindEventsFromSetting()
 *       
 *     Display:
 *       getSettings() 
 *       getElement() 
 *       loadSettings()
 *     
 *     unhex(text)
**/

function jSettings() {
    this.settings = [];
    this.selement = "";
    this.loaduri = "";
    this.saveuri = "";
    this.storage = {};
    this.urlappend = "";
    this.datasplit = ",";
    this.behavior = "button";
    this.changed = [];
    this.success = 'success';
    this.labels = false;
    this.ledited = '';
    this.bindHandler = function() {};
    this.unbindHandler = function() {};
    
    this.init = function(element, luri) {
        this.selement = element;
        this.loaduri = luri;
    }
    
    this.setSettings = function(settings) {
        this.settings = settings;
    }
    
    this.setSaveURI = function(suri) {
        this.saveuri = suri;
    }
    
    this.setURLAppend = function(data) {
        this.urlappend = data;
    }
    
    this.setSplit = function(delimiter) {
        this.datasplit = delimiter;
    }
    
    this.setSaveBehavior = function(type) {
        this.behavior = type;
    }
    
    this.setSuccess = function(value) {
        this.success = value;
    }
    
    this.setLabels = function(value) {
        this.labels = value;
    }
    
    this.setBindHandler = function(handler) {
        this.bindHandler = handler;
    }
    
    this.setUnbindHandler = function(handler) {
        this.unbindHandler = handler;
    }
    
    this.hideSettings = function() {
        $('#' + this.selement).hide();
    }
    
    this.showSettings = function() {
        $('#' + this.selement).show();
    }
    
    this.getData = function() {
        for (var sid in this.settings) {
            var setting = this.settings[sid];
            if ((setting[0] == 'jsettings-space') || (setting[0] == 'jsettings-button')) {
                continue;
            }
            
            this.getDataFromSetting(setting);
        }
        
        this.changed = [];
    }
    
    this.getDataFromSetting = function(setting) {
        if (setting[0] == 'jsettings-element') {
            var ddata = '';
            $.ajaxSetup({async:false});
            $.get(this.loaduri + "?id=" + setting[2] + this.urlappend, function(data) {
                ddata = data;
            });
            $.ajaxSetup({async:true});
            
            if (ddata != 'error') {
                var keyvalue = ddata.split(this.datasplit, 2);
                
                if (setting[4] == 'hex') {
                    keyvalue[1] = this.unhex(keyvalue[1]);
                }
                
                this.storage[setting[2]] = keyvalue[1];
            } else {
                alert("Error loading setting: " + setting[2]);
                return;
            }
        } else if (setting[0] == 'jsettings-section') {
            var celements = setting[4];
            for (var id in celements) {
                var nsetting = celements[id];
                this.getDataFromSetting(nsetting);
            }
        } else if (setting[0] == 'jsettings-group') {
            var gdata = '';
            $.ajaxSetup({async:false});
            $.get(this.loaduri + "?id=" + setting[1] + this.urlappend, function(data) {
                gdata = data;
            });
            $.ajaxSetup({async:true});
            
            if (ddata != 'error') {
                gdata = jQuery.parseJSON(gdata, true);
                for (var dloc in gdata) {
                    var ddata = gdata[dloc];
                    
                    var keyvalue = ddata.split(this.datasplit, 2);
                    
                    if (setting[4] == 'hex') {
                        keyvalue[1] = this.unhex(keyvalue[1]);
                    }
                    
                    this.storage[keyvalue[0]] = keyvalue[1];
                }
            } else {
                alert("Error loading setting: " + setting[1]);
                return;
            }
        }
    }
    
    this.getElement = function(setting) {
        var result = "";
        if (setting[0] == "jsettings-element") {
            var text = setting[1];
            var id = setting[2];
            var type = setting[3];
            var handle = setting[4];
            
            if (this.labels && type != 'hidden') {
                result += '<label id="' + this.selement + '-' + id + '-label" class="jsettings-label">' + text + '</label>';
            }
            
            if (type == 'image') {
                result += '<img alt="' + id + '" id="' + this.selement + '-' + id + '" src="' + this.storage[id] + '" title="' + text + '" class="jsettings-image">';
            } else if (type == 'text-area') {
                result += '<textarea id="' + this.selement + '-' + id + '" placeholder="' + text + '" title="' + text + '" class="jsettings-textarea"></textarea>';
            } else if (type == 'select') {
                result += '<select id="' + this.selement + '-' + id + '" class="jsettings-select">';
                for (var hid in handle) {
                    var option = handle[hid];
                    if (option[1] != this.storage[id]) {
                        result += '<option value="' + option[1] + '">' + option[0] + '</option>';
                    } else {
                        result += '<option value="' + option[1] + '" selected="selected">' + option[0] + '</option>';
                    }
                }
                result += '</select>';
            } else {
                result += '<input id="' + this.selement + '-' + id + '" placeholder="' + text + '" type="' + type + '" title="' + text + '" class="jsettings-input">';
            }
            if (this.labels && type != 'hidden') {
                result += "<br>";
            }
        } else if (setting[0] == "jsettings-space") {
            result += "<br>";
        } else if (setting[0] == "jsettings-button") {
            result += '<button id="' + this.selement + '-' + setting[2] + '" class="jsettings-button">' + setting[1] + '</button>';
        } else if (setting[0] == "jsettings-section") {
            result += '<div id="' + this.selement + '-' + setting[2] + '" class="jsettings-section">';
            result += '<h3 id="' + this.selement + '-' + setting[2] + '-title" class="jsettings-section-title">' + setting[1] + '</h3>';
            result += '<p id="' + this.selement + '-' + setting[2] + '-text" class="jsettings-section-description">' + setting[3] + '</p>';
            var celements = setting[4];
            for (var id in celements) {
                var nsetting = celements[id];
                result += this.getElement(nsetting);
            }
            result += '</div>';
        }
        return result;
    }
    
    this.getSettings = function() {
        var result = "";
        
        for (var sid in this.settings) {
            var setting = this.settings[sid];
            
            if (setting[0] == 'jsettings-group') {
                for (var i = 2; i < setting.length; i++) {
                    result += this.getElement(setting[i]);
                }
            } else {            
                result += this.getElement(setting);
            }
        }

        if ((this.behavior == 'button') || (this.behavior == 'both')) {
            result += '<button class="jsettings-button" id="' + this.selement + '-submit">Save</button>';
        }
        
        result += '<span id="' + this.selement + '-saving" class="jsettings-saving">&nbsp;</span>';
        
        return result;
    }
    
    this.loadSettings = function() {
        $('#' + this.selement).html(this.getSettings());
    }
    
    this.pushData = function() {
        for (var cid in this.changed) {
            var changeid = this.changed[cid];
            var levels = changeid.split('.');
            var setting = this.settings;
            for (var lid in levels) {
                var level = levels[lid];
                if (lid == 0) {
                    setting = this.settings[level];
                    if (setting[0] == 'jsettings-section') {
                        setting = setting[4];
                    } else {
                        break;
                    }
                } else {
                    if (setting[level][0] == 'jsettings-section') {
                        setting = setting[level][4];
                    } else {
                        setting = setting[level];
                        break;
                    }
                }
            }
            
            this.pushDataFromSetting(setting);
        }
        
        this.changed = [];
    }
    
    this.pushDataFromSetting = function(setting) {
        if (setting[0] == "jsettings-element") {
            var nvalue = this.storage[setting[2]];
            var ddata = '';
            $.ajaxSetup({async:false});
            $.get(this.saveuri + "?id=" + setting[2] + "&val=" + nvalue + this.urlappend, function(data) {
                ddata = data;
            });
            $.ajaxSetup({async:true});
            
            if (ddata != this.success) {
                alert("Error saving setting: " + setting[2] + ": " + ddata);
            }
        } else if (setting[0] == "jsettings-section") {
            var celements = setting[4];
            for (var id in celements) {
                var nsetting = celements[id];
                this.pushDataFromSetting(nsetting);
            }
        } else if (setting[0] == "jsettings-group") {
            var query = "?id=" + setting[1];
            for (var i = 2; i < setting.length; i++) {
                query += "&" + setting[i][2] + '=' + this.storage[setting[i][2]];
            }
            
            $.ajaxSetup({async:false});
            $.get(this.saveuri + query + this.urlappend, function(data) {
                ddata = data;
            });
            $.ajaxSetup({async:true});
            
            if (ddata != this.success) {
                alert("Error saving setting: " + setting[1] + ": " + ddata);
            }
        }
    }
    
    this.saveData = function() {
        for (var sid in this.settings) {
            var setting = this.settings[sid];
            if ((setting[0] == 'jsettings-space') || (setting[0] == 'jsettings-button')) {
                continue;
            }
            
            this.saveDataFromSetting(setting, sid);
        }
    }
    
    this.saveDataFromSetting = function(setting, sid) {
        if (setting[0] == "jsettings-element") {
            if ($('#' + this.selement + "-" + setting[2]).val() != this.storage[setting[2]]) {
                this.storage[setting[2]] = $('#' + this.selement + "-" + setting[2]).val();
                this.changed.push(sid);
            }
        } else if (setting[0] == "jsettings-section") {
            var celements = setting[4];
            for (var id in celements) {
                var nsetting = celements[id];
                this.saveDataFromSetting(nsetting, sid + "." + id);
            }
        } else if (setting[0] == "jsettings-group") {
            var pushed = false;
            for (var i = 2; i < setting.length; i++) {
                if ($('#' + this.selement + "-" + setting[i][2]).val() != this.storage[setting[i][2]]) {
                    this.storage[setting[i][2]] = $('#' + this.selement + "-" + setting[i][2]).val();
                    if (!pushed) {
                        this.changed.push(sid);
                        pushed = true;
                    }
                }
            }
        }
    }
    
    this.loadData = function() {
        for (var sid in this.settings) {
            var setting = this.settings[sid];
            if ((setting[0] == 'jsettings-space') || (setting[0] == 'jsettings-button')) {
                continue;
            }
            
            this.loadDataFromSetting(setting);
        }
        
        this.changed = [];
    }
    
    this.loadDataFromSetting = function(setting) {
        if (setting[0] == "jsettings-element") {
            $('#' + this.selement + '-' + setting[2]).val(this.storage[setting[2]]);
        } else if (setting[0] == "jsettings-section") {
            var celements = setting[4];
            for (var id in celements) {
                var nsetting = celements[id];
                this.loadDataFromSetting(nsetting);
            }
        } else if (setting[0] == "jsettings-group") {
            for (var i = 2; i < setting.length; i++) {
                this.loadDataFromSetting(setting[i]);
            }
        }
    }
    
    this.eventTriggerSave = function(event) {
        event.data.instance.saveData();
        if (event.data.instance.changed.length != 0) {
            $('#' + event.data.instance.selement + '-saving').html('Saving...');
            event.data.instance.ledited = event.data.element;
            event.data.instance.pushData();
            event.data.instance.load();
        }
    }
    
    this.bindEvents = function() {
        this.unbindEvents();
        if (this.behavior == 'button') {
            $(document).on('click', '#' + this.selement + '-submit', { instance: this }, this.eventTriggerSave);
        } else {
            if (this.behavior == 'both') {
                $(document).on('click', '#' + this.selement + '-submit', { instance: this }, this.eventTriggerSave);
            }
            
            for (var sid in this.settings) {
                var setting = this.settings[sid];
                
                if (setting[0] != 'jsettings-space') {
                    this.bindEventsFromSetting(setting);
                }
            }
        }
        
        this.bindHandler();
    }
    
    this.bindEventsFromSetting = function(setting) {
        if (setting[0] == "jsettings-element") {
            $(document).on('focusout', '#' + this.selement + '-' + setting[2], { instance: this, element: '#' + this.selement + '-' + setting[2]}, this.eventTriggerSave);
        } else if (setting[0] == "jsettings-button") {
            $(document).on('click', '#' + this.selement + '-' + setting[2], { instance: this, element: '#' + this.selement + '-' + setting[2], callback: setting[3]}, setting[3]);
        } else if (setting[0] == "jsettings-section") {
            var celements = setting[4];
            for (var id in celements) {
                var nsetting = celements[id];
                this.bindEventsFromSetting(nsetting);
            }
        }
    }
    
    this.unbindEvents = function() {
        if (this.behavior == 'button') {
            $(document).off('click', '#' + this.selement + '-submit');
        } else {
            if (this.behavior == 'both') {
                $(document).off('click', '#' + this.selement + '-submit');
            }
            
            for (var sid in this.settings) {
                var setting = this.settings[sid];
                
                if (setting[0] != 'jsettings-space') {
                    this.unbindEventsFromSetting(setting);
                }
            }
        }
        
        this.unbindHandler();
    }
    
    this.unbindEventsFromSetting = function(setting) {
        if (setting[0] == "jsettings-element") {
            $(document).off('focusout', '#' + this.selement + '-' + setting[2]);
        } else if (setting[0] == "jsettings-button") {
            $(document).off('click', '#' + this.selement + '-' + setting[2]);
        } else if (setting[0] == "jsettings-section") {
            var celements = setting[4];
            for (var id in celements) {
                var nsetting = celements[id];
                this.unbindEventsFromSetting(nsetting);
            }
        }
    }
    
    this.unhex = function(text) {
        if (text == undefined) {
            return;
        }
        
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
    
    this.load = function() {
        this.unbindEvents();
        this.getData();
        this.hideSettings();
        this.loadSettings();
        this.loadData();
        this.showSettings();
        this.bindEvents();
        if (this.ledited != '') {
            $(this.ledited).focus();
        }
    }
}
