var resizeEditor = function () {
    var p = $('#preview');
    $('#editor').css({
        'height':p.height() + 'px',
        'width':p.width() + 'px'
    });
};

jQuery(function ($) {
    $(document).ready(resizeEditor);
    $('img').load(resizeEditor);
});
