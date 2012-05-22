define([
    'vm'
], function (Vm) {
    var AppRouter = Backbone.Router.extend({
        routes:{
            "":"home",
            "*page":"showPage"
        },
        home:function () {
            this.showPage("login");
        },
        showPage:function (pageId) {
            _.each(Vm.pages, function (page) {
                if (page.id == pageId) {
                    $(page.el).show();
                } else {
                    $(page.el).hide();
                }
            });
            _.each(Vm.menuItems.models, function (menuItem) {
                menuItem.set('active', menuItem.get('id') == pageId);
            });
        }
    });

    var initialize = function (options) {
        var appView = options.appView;
        var router = new AppRouter(options);

        router.on('', function () {
            this.showPage("login");
        });

        router.on('*page', function (pageId) {
            _.each(Vm.pages, function (page) {
                if (page.id == pageId) {
                    $(page.el).show();
                } else {
                    $(page.el).hide();
                }
            });
            _.each(Vm.menuItems.models, function (menuItem) {
                menuItem.set('active', menuItem.get('id') == pageId);
            });
        });

        Backbone.history.start();
    };
    return {
        initialize:initialize
    };
});