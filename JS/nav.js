$(document).on('dblclick', '#navdropdown', function() {
    if ($('#navlogin').is("*")) {
        touch = true;
        window.location.href = '/login.php';
    }
});

touch = false;
$(document).on('click', '#navdropdown', function() {
    if (touch == false) {
        touch = true;
        $('ul.navmenu').show();
    } else {
        touch = false;
        $('ul.navmenu').hide();
    }
});

$(document).on('mouseenter', '#canvashome', function(event) {
        drawHome('canvashome', '#4466FA');
});

$(document).on('mouseleave', '#canvashome', function(event) {
        drawHome('canvashome', '#232323');
});

$(document).on('mouseenter', '#canvascreate', function(event) {
        drawPlus('canvascreate', '#4466FA');
});

$(document).on('mouseleave', '#canvascreate', function(event) {
        drawPlus('canvascreate', '#232323');
});

$(document).on('mouseenter', '#navdropdown', function(event) {
        drawDarr('canvasdropdown', '#4466FA');
});

$(document).on('mouseleave', '#navdropdown', function(event) {
        drawDarr('canvasdropdown', '#232323');
});

$(document).on('hover', '#navdropdown', function(event) {
    if ($('#navlogin').length == 0) {
        if (event.type == 'mouseenter') {
            drawDarr('canvasdropdown', '#4466FA');
        } else {
            drawDarr('canvasdropdown', '#232323');
        }
    }
});
