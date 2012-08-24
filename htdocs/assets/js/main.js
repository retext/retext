$(document).ready(function () {
    /* Enable tooltips */
    $('[rel=tooltip]').tooltip();
    $('[rel=popover]').popover();
    var inp = $('#signupform input:first');
    inp.popover();
    $('[href=#signupform]').click(function () {
        inp.focus();
    });
    if (typeof CFInstall !== "undefined") {
        CFInstall.check();
    }
});