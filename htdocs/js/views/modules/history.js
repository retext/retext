/**
 * Allgemeine Klasse für die Anzeige von Verläufen
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
], function () {
    return Backbone.View.extend({
        tagName:'ul',
        initialize:function () {
            this.model.bind('reset', this.render, this);
            this.model.bind('add', this.render, this);
        },
        render:function () {
            var el = $(this.el);
            el.html(this.template({models:this.model.toJSON()}));
            var timestamps = el.find('time');
            if (timestamps.length > 0)  _.each(timestamps, function (t) {
                $(t).html($.timeago(t));
            });
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
})
;
