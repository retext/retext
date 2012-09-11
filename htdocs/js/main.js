/**
 * Einstieg der Anwendung.
 *
 * Lädt die AppView und den Router.
 *
 * @author Markus Tacker <m@tckr.cc>
 */
require.config({
    urlArgs:"bust=" + (new Date()).getTime(),
    paths:{
        text:'../vendor/require-text/text',
        templates:'../templates'
    }
});

require([
    'views/app',
    'router',
    'vm'
], function (AppView, Router, Vm) {

    // So, in order to get a FileList out of a native host drag event object when binding with jQuery, you have to push the dataTransfer property onto the jQuery.event.props array:
    // Originally solved by Tim Branyen in his drop file plugin
    // http://dev.aboutnerd.com/jQuery.dropFile/jquery.dropFile.js
    jQuery.event.props.push('dataTransfer');

    // App initialisieren
    var appView = new AppView();
    new Router(appView);
    appView.render();
    Backbone.history.start();
    appView.complete();
    Vm.updateExtras(document.body);
});