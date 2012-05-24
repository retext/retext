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
            this.navigate("login");
        },
        logout: function() {
            this.user.set('authenticated', false);
            this.navigate("login");
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
                _.each(menuGroup.models, function (menuItem) {
                    menuItem.set({active: menuItem.get('id') == pageId});
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