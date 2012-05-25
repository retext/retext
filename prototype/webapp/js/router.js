define([
], function () {
    var AppRouter = Backbone.Router.extend({
        routes:{
            "":"home",
            "login":"login",
            "logout":"logout",
            "*page":"showPage"
        },
        home:function () {
            this.showPage('login');
        },
        logout: function() {
            // TODO send logout to server
            this.user.set('authenticated', false);
            window.location.href = '/app/';
        },
        login: function() {
            this.showPage('login');
        },
        showPage:function (pageId) {
            _.each(this.vm.pages, function (page) {
                if (page.id == pageId) {
                    $(page.el).show();
                } else {
                    $(page.el).hide();
                }
            });
            _.each(this.vm.menuGroups.models, function (menuGroup) {
                _.each(menuGroup.get('children').models, function (menuItem) {
                    menuItem.set({active: menuItem.get('id')== pageId});
                    _.each(menuItem.get('children').models, function(menuChild) {
                        if (menuChild.get('id')== pageId) {
                            menuItem.set({active: true});
                        }
                    });
                });
            });
        },
        initialize: function (appView, vm, user) {
            this.appView = appView;
            this.vm = vm;
            this.user = user;
        }
    });
    return AppRouter;
});