/**
 * jPage v1.1 - A dynamic jQuery multi-page library
 * Depends: jQuery >= 1.5
 * 
 * Copyright (C) 2011, 2013, 2014 Alex Scheel
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
 *   var page = new jPage();
 *   page.init('div-page-id');
 *   page.setPages(['#hash', 'name', 'title', ['/url/to/page', false], true], '#hash');
 *   page.setNavigation('anav', 'ul');
 *   page.setBasepath('/containing/folder');
 *   page.load();
 *   
 * API:
 *   Main:
 *     init(pelement) - Set container element for jPage
 *     
 *     load() - Starts jPage, loads data, displays
 *     
 *   Config:
 *     setPages(pages-as-array, starting-hash) - Sets pages, starting hash. To include get, set ['/url/to/page', true]
 *     
 *     setNavigation(nelement, navtype) - Sets navigation element, type
 *     
 *     setBasepath(bpath) - Prefix urls of page with base path
 *     
 *     setStorage(name, value) - Sets internal storage option
 *     
 *   Getters:
 *     getPositionFromHash(hash) - Returns id of hash
 *     
 *     getNavigation() - returns rendered navigation
 *     
 *     getPage()  - gets current page, synchronously
 *     
 *     getPageTitle() - gets page title
 *
 *     getStorage(name) - gets value from storage
 *     
 *   Internal:
 *     setPage()
 *     showPage()
 *     hidePage()
 *     toPageByHash(hash)
 *     toPageById(hash)
 *     returnGET()
 *     
 *     Events:
 *       eventChangePage(event)
 *       bindEvents()
 *       unbindEvents()
**/

function jPage() {
    this.pages = [];
    this.pelement = "";
    this.cpage = 0;
    this.opage = 0;
    this.navtype = "";
    this.nelement = "";
    this.basepath = "";
    this.storage = {};
    this.callback = undefined;
    
    this.init = function(pelement, callback) {
        this.pelement = pelement;
        this.callback = callback;
    }
    
    this.setPages = function(page, starting) {
        this.pages = page;
        if (this.getPositionFromHash(starting) != false) {
            this.cpage = this.getPositionFromHash(starting);
        } else {
            this.cpage = 0;
        }
    }
    
    this.setNavigation = function(nelement, navtype) {
        this.nelement = nelement;
        this.navtype = navtype;
    }
    
    this.setBasepath = function(bpath) {
        this.basepath = bpath;
    }
    
    this.setStorage = function(name, value) {
        this.storage[name] = value;
    }
    
    this.getPositionFromHash = function(hash) {
        for (var pos in this.pages) {
            var phash = this.pages[pos][0];
            if (phash == hash) {
                return pos;
            }
        }
        return false;
    }
    
    this.getNavigation = function() {
        var result = '<ul class="jpage-ul">';
        for (var pos in this.pages) {
            var hash = this.pages[pos][0];
            var text = this.pages[pos][1];
            var required = this.pages[pos][4];
            
            if (required == true) {
                result += '<li class="jpage-li" id="' + this.nelement + '-' + hash.substr(1) + '-li"><a class="jpage-anchor" href="' + hash + '">' + text + "</a></li>";
            }
        }
        return result;
    }
    
    this.getPage = function() {
        $.ajaxSetup({async:false});
        var url = this.pages[this.cpage][3];
        var data = "";
        if (url.constructor == Array) {
            url = url[0];
            if (typeof this.pages[this.cpage][3][1] == "object") {
                for (var pos in this.pages[this.cpage][3][1]) {
                    var npos = this.pages[this.cpage][3][1][pos];
                    if (data == "") {
                        data = "?" + npos + "=" + this.storage[npos];
                    } else {
                        data += "&" + npos + "=" + this.storage[npos];
                    }
                }
            } else if (this.pages[this.cpage][3][1] == true) {
                var get = this.returnGET();
                for (var index in get) {
                    if (data == '') {
                        data = "?" + index + "=" + get[index];
                    } else {
                        data += "&" + index + "=" + get[index];
                    }
                }
            }
        }
        
        $.get(this.basepath + url + data, $.proxy(this.handlePage, this));
        $.ajaxSetup({async:true});
    }
    
    this.getPageTitle = function() {
        return this.pages[this.cpage][2];
    }
    
    this.getStorage = function(name) {
        return this.storage[name];
    }
    
    this.setPage = function() {
        window.document.title = this.getPageTitle();
        $('#' + this.nelement).html(this.getNavigation());
        this.getPage();
    }
    
    this.hidePage = function() {
        $('#' + this.nelement).hide();
        $('#' + this.pelement).hide();
    }
    
    this.showPage = function() {
        $('#' + this.nelement).show();
        $('#' + this.pelement).show();
    }
    
    this.handlePage = function(data) {
        $('#' + this.pelement).html(data);
        if (this.callback)
            this.callback();
    }
    
    this.returnGET = function() {
	    var vars = {};
	    var parts = window.location.href.replace('#[a-zA-Z0-9-]*', '').replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		    vars[key] = value;
	    });
	    return vars;
    }
    
    this.toPageByHash = function(hash) {
        this.opage = this.cpage;
        if (this.getPositionFromHash(hash) != false) {
            this.cpage = this.getPositionFromHash(hash);
        } else {
            this.cpage = 0;
        }
        this.setPage();
    }
    
    this.toPageById = function(id) {
        this.opage = this.cpage;
        this.cpage = id;
        this.setPage();
    }
    
    this.eventChangeHash = function(event) {
        if (location.hash != event.data.instance.pages[event.data.instance.cpage][0]) {
            event.data.instance.toPageByHash(location.hash);
        }
    }
    
    this.eventChangePage = function(event) {
        event.data.instance.toPageById(event.data.element)
    }
    
    this.bindEvents = function() {
        this.unbindEvents();
        
        if ("onhashchange" in window) {
            $(window).on('hashchange', { instance: this }, this.eventChangeHash);
        }
        
        for (var pos in this.pages) {
            var hash = this.pages[pos][0];
            var required = this.pages[pos][4];
            
            if (required == true) {
                $(document).on('click', '#' + this.nelement + '-' + hash.substr(1) + '-li', { instance: this, element: pos }, this.eventChangePage);
            }
        }
    }
    
    this.unbindEvents = function() {
        if ("onhashchange" in window) {
            $(window).off('hashchange');
        }
        
        for (var pos in this.pages) {
            var hash = this.pages[pos][0];
            var required = this.pages[pos][4];
            
            if (required == true) {
                $(document).off('click', '#' + this.nelement + '-' + hash.substr(1) + '-li');
            }
        }
    }
    
    this.load = function() {
        this.hidePage();
        this.unbindEvents();
        this.setPage();
        this.bindEvents();
        this.showPage();
    }
}
