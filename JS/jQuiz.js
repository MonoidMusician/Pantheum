function jQuiz() {
    this.questions = [];
    this.results = [];
    this.answers = {};
    this.stats = [];
    this.current = 0;
    this.next = 0;
    this.last = 1;
    this.qelement = 'quiz';
    this.gurl = '';
    this.surl = '';
    
    this.init = function(qelement, gurl, surl) {
        this.qelement = qelement;
        this.gurl = gurl;
        this.surl = surl;
    };
    
    this.getNextQuestion = function() {
        $.ajaxSetup({async:false});
        $.get(this.gurl, $.proxy(this.handleQuestion, this));
        $.ajaxSetup({async:true});
    };
    
    this.submitQuestion = function() {
        $.ajaxSetup({async:false});
        $.post(this.surl, this.answers, $.proxy(this.handleResponse, this));
        $.ajaxSetup({async:true});
    };
    
    this.handleQuestion = function(data) {
        this.questions[this.next] = jQuery.parseJSON(data);
        this.current = this.next;
        this.next += 1;
        this.displayQuestion();
    };
    
    this.handleResponse = function(data) {
        if (data != '') {
            result = jQuery.parseJSON(data);
            this.results[this.current] = result;
            this.displayQuestion();
        }
    };
    
    this.buildHeader = function() {
        var header = '<section id="' + this.qelement + '-top">';
        
        if ((this.current - 1) >= 0) {
            header += '<button id="' + this.qelement + '-back">Back</button>';
        } else {
            header += '<button id="' + this.qelement + '-back" class="jQuiz-disabled">Back</button>';
        }
        
        header += '<span>' + (this.current+1) + ' / ' + this.last + '</span>';
        
        if ((this.current == (this.next-1)) && (this.results[this.current] == undefined)) {
            header += '<button id="' + this.qelement + '-submit">Submit</button>';
        } else if (this.current == this.last - 1) {
            header += '<button id="' + this.qelement + '-next">Finish</button>';
        } else {
            header += '<button id="' + this.qelement + '-next">Next</button>';
        }
        
        header += '</section>';
        
        return header;
    }
    
    this.parsePart = function(part) {
        var result = '';
        
        if (part[0] == 'text') {
            result = '<span class="jquiz-text">' + part[1] + '</span>';
        } else if (part[0] == 'html') {
            result = part[1];
        } else {
            if (this.results[this.current] == undefined) {
                if (part[0] == 'input') {
                    result = '<input type="text" id="' + this.qelement + '-' + part[1] + '" placeholder="' + part[2] + '" title="' + part[3] + '">';
                } else if (part[0] == 'select') {
                    //result += '<select id="' + this.qelement + '-' + part[1] + '" title="' + part[2] + '">';
                    result += '<span id="' + this.qelement + part[1] + '" class="select select-bordered">';
                    for (var oid in part[3]) {
                        var option = part[3][oid];
                        result += '<label>';
                        result += '<input class="inputlabel" type="radio"';
                        result += 'name="'+this.qelement + '-' + part[1]+'"';
                        result += 'value="'+option+'" required>';
                        result += option;
                        result += '</label><br>';
                        //result += '<option>' + option + '</option>';
                    }
                    result += '</span>';
                    //result += '</select>';
                } else if (part[0] == 'matching-row') {
                    for (var oid in part[3]) {
                        var option = part[3][oid];
                        result += '<td><label>';
                        result += '<input class="inputlabel" type="radio"';
                        result += 'name="'+this.qelement + '-' + part[1]+'"';
                        result += 'value="'+option+'" required>';
                        result += (parseInt(oid)+1)+'.';
                        result += '</label></td>';
                        //result += '<option>' + option + '</option>';
                    }
                } else if (part[0] == 'matching') {
                    //result += '<select id="' + this.qelement + '-' + part[1] + '" title="' + part[2] + '">';
                    result += '<div class="scrollable"><table id="' + this.qelement + part[1] + '" class="jquiz-matching"><tr><td></td>';
                    for (var oid in part[4]) {
                        var option = part[4][oid];
                        result += '<th>';
                        result += option;
                        result += '</th>';
                    }
                    result += '</tr>';
                    for (var vid in part[3]) {
                        var left = part[3][vid];
                        result += '<tr><th>'+left+'</th>';
                        for (var oid in part[4]) {
                            var option = part[4][oid];
                            result += '<td><label>';
                            result += '<input class="inputlabel" type="radio"';
                            result += 'name="'+this.qelement + '-' + part[1]+'-'+vid+'"';
                            result += 'value="'+option+'" required>';
                            result += (parseInt(oid)+1)+'.';
                            result += '</label></td>';
                            //result += '<option>' + option + '</option>';
                        }
                        result += '</tr>';
                    }
                    result += '</table></div>';
                    //result += '</select>';
                }
            } else {
                if (this.results[this.current][part[1]][1] == true) {
                    result += '<span class="jquiz-correct">' + this.results[this.current][part[1]][0] + ' </span>';
                } else {
                    result += '<span class="jquiz-incorrect">' + this.results[this.current][part[1]][0] + ' </span>';
                    result += '<span class="jquiz-correct">(' + this.results[this.current][part[1]][1] + ') </span>';
                }
            }
        }
        
        return result;
    };
    
    this.buildBody = function() {
        var body = '<section id="' + this.qelement + '-content">';
        body += '<p type="jquiz-question">';
        for (var pid in this.questions[this.current]) {
            var part = this.questions[this.current][pid];
            body += this.parsePart(part);
        }
        body += '</p>';
        body += '</section>';
        
        return body;
    }
    
    this.displayQuestion = function() {
        var html = this.buildHeader() + this.buildBody();
        
        $('#' + this.qelement).html(html);
    };
    
    this.handleSubmit = function(data) {
        this.answers = {};
        for (var pid in this.questions[this.current]) {
            var part = this.questions[this.current][pid];
            if ((part[0] == 'input')) {
                this.answers[part[1]] = $('#' + this.qelement + '-' + part[1]).val();
            } else if ((part[0] == 'select') || (part[0] == 'matching-row')) {
                this.answers[part[1]] = $('input:radio[name='+this.qelement + '-' + part[1]+']:checked').val();
            } else if ((part[0] == 'matching')) {
                this.answers[part[1]] = [];
                for (vid in part[3]) {
                    this.answers[part[1]].push($('input:radio[name='+this.qelement + '-' + part[1]+'-'+vid+']:checked').val());
                }
                this.answers[part[1]] = this.answers[part[1]].join("\n");
            } else continue;
            if (!this.answers[part[1]]) {
                alert('Please answer all questions before submitting!');
                return;
            }
        }
        this.submitQuestion();
    };
    
    this.handleNext = function(data) {
        if (this.current == this.last - 1) {
            this.endQuiz();
        } else if (this.current == (this.next-1)) {
            this.getNextQuestion();
        } else {
            this.current += 1;
            this.displayQuestion();
        }
    };
    
    this.handleBack = function(data) {
        if (this.current == 0) {
        } else {
            this.current -= 1;
        }
        
        this.displayQuestion();
    };
    
    
    this.bindEvents = function() {
        this.unbindEvents();
        
        $(document).on('click', '#' + this.qelement + '-back', $.proxy(this.handleBack, this));
        $(document).on('click', '#' + this.qelement + '-submit', $.proxy(this.handleSubmit, this));
        $(document).on('click', '#' + this.qelement + '-next', $.proxy(this.handleNext, this));
    };
    
    this.unbindEvents = function() {
        $(document).off('click', '#' + this.qelement + '-back');
        $(document).off('click', '#' + this.qelement + '-submit');
        $(document).off('click', '#' + this.qelement + '-next');
        $(document).off('click', '#' + this.qelement + '-finish');
    };
    
    this.start = function(last) {
        this.last = last;
        this.getNextQuestion();
        this.bindEvents();
    };
}
