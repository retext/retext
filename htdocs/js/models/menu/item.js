/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
], function (MenuItemCollection) {
    return Backbone.Model.extend({
        defaults:{
            href:'#',
            active:false,
            children:[],
            icon:false,
            authOnly:false,
            anonOnly:false
        }
    });
});