/**
 * Kümmert sich um die Anzeige des Passwort-Vergessen-Formulars
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'views/page/base',
    'text!templates/page/lostpassword.html'
], function (PageViewBase, Template) {
    return PageViewBase.extend({
        className:'container compact',
        render:function () {
            $(this.el).html(Template);
            return this;
        }
    });
});
