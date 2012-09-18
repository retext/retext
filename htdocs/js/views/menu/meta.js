/**
 * Rendert das Meta-Menü
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/menu/item',
    'collections/menu/item'
], function (MenuItem, MenuItemCollection) {
    return Backbone.View.extend({
        auth:false,
        render:function () {
            _.each($(this.el).find('a'), function (a) {
                var a = $(a);
                if (this.auth && a.data('anononly')) {
                    a.addClass('hidden');
                } else {
                    a.removeClass('hidden');
                }
                if (!this.auth && a.data('authonly')) {
                    a.addClass('hidden');
                } else {
                    a.removeClass('hidden');
                }
            }, this);
            return this;
        }
    });
});