<header>
    <h2 class="validation">Validation</h2>
</header>
<article>
    <div class="validation">
        <div class="input">
            <input id="validationusername" class="validation" type="text" placeholder="Username"><br>
            <input id="validationemail" class="validation" type="email" placeholder="Email"><br>
            <button id="validationsubmit">Validate</button>
            <div id="validationerror"></div>
        </div>
    </div>
    <script type="text/javascript">
        $('#validationsubmit').click(function(e) {
            if (value != 'xx') {
                var username = $('#validationusername').val();
                var email = $('#validationemail').val();
                var code = makeGET()['id'];
                if (username != '') {
                    if (email != '') {
                        if ((code != '') && (code != 'undefined') && (code != undefined)) {
                            if (email.search(/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i) != -1) {
                                $('#validationperror').html('Sending&nbsp;request...');
                                $.post("/latin/PHP5/validation.php", { u: username, e: email, s: code, v: value }, function(data) {
                                    if (data == 'success') {
                                        $('#validationerror').html('Account&nbsp;validated.<br>Please&nbsp;sign&nbsp;in&nbsp;now.');
                                    } else {
                                        $('#validationerror').html('Error&nbsp;validation&nbsp;account.<br>Code:&nbsp;' + data);
                                    }
                                });
                            } else {
                                $('#validationerror').html('Invalid&nbsp;email.');
                            }
                        } else {
                            $('#validationerror').html('Missing&nbsp;account&nbsp;code.');
                        }
                    } else {
                        $('#validationerror').html('Missing&nbsp;email.');
                    }
                } else {
                    $('#validationerror').html('Missing&nbsp;username.');
                }
            } else {
                $.get('/latin/PHP5/getcode.php', function(data) {
                    value = data;
                });
                
                $('#validationerror').html('Error&nbsp;with&nbsp;magic&nbsp;code.<br>Please&nbsp;try&nbsp;again.');
            }
        });
        
        $('input.validation').keypress(function(e){
            if (e.which == 13) {
                $('#validationsubmit').click();
            }
        });
        
        value = 'xx';
        
        $.get('/latin/PHP5/getcode.php', function(data) {
            value = data;
        });
        
        function makeGET() {
            var tmp = {};
            var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
                tmp[key] = value;
            });
            return tmp;
        }
    </script>
</article>
