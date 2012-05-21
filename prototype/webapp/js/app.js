$(function () {

    var RetextApp = {
        apiUrlBase: window.location.protocol + '//' + window.location.host.replace(/^app\./, '') + '/api/'
    };

    var MenuItemView = Backbone.View.extend({
        'tagName':'li',
        'template':_.template($('#mainmenuitem_template').html()),
        'render':function () {
            $(this.el).append(this.template(this.model.toJSON()));
            return this;
        }
    });

    var MenuView = Backbone.View.extend({
        'tagName':'ul',
        'className':'nav',
        'initialize':function () {
            this.model.bind("change", this.render, this);
        },
        'events':{
            'click a':'linkClicked'
        },
        'render':function () {
            $(this.el).empty();
            _.each(this.model.models, function (menuItem) {
                $(this.el).append(new MenuItemView({'model':menuItem, 'className':menuItem.get('active') ? 'active' : ''}).render().el)
            }, this);
            return this;
        },
        'linkClicked':function (ev, ev2) {
            if ($(ev.target).attr('href') == '#status') {
                statusColl.fetch({
                    success:function (messages) {
                        RetextApp.pages['status'].render();
                    }
                });
            }
        }
    });

    var MenuItem = Backbone.Model.extend({
        'initialize':function () {
            if (!this.has('active')) this.set('active', false);
        }
    });
    var MenuItemCollection = Backbone.Collection.extend({
        'model':MenuItem
    });
    RetextApp.menuItems = new MenuItemCollection();
    _.each([
        ['login', 'Anmelden'],
        ['register', 'Registrieren'],
        ['about', 'Über'],
        ['status', 'Status']
    ], function (menuItem) {
        RetextApp.menuItems.add(new MenuItem({'id':menuItem[0], 'label':menuItem[1]}));
    });

    var ApiStatusModel = Backbone.Model.extend();
    var DefaultApiStatusModel = ApiStatusModel.extend({
        'initialize':function () {
            this.set('time', 'unknown');
            this.set('version', 'unknown');
        }
    });
    var ApiStatusCollection = Backbone.Collection.extend({
        'url': RetextApp.apiUrlBase + 'status',
        'model':ApiStatusModel
    });

    RetextApp.StatusView = Backbone.View.extend({
        'el':$('#status'),
        'template':_.template($('#status_template').html()),
        'initialize':function () {
            this.model.bind("change", this.render, this);
        },
        'render':function () {
            $(this.el).empty();
            $(this.el).append(this.template(this.model.at(0).toJSON()));
            return this;
        }
    });

    RetextApp.AppView = new Backbone.View({'el':$('#app')});
    RetextApp.pages = {};
    _.each(['login', 'register', 'about'], function (pageId) {
        RetextApp.pages[pageId] = new Backbone.View({'el':$('#' + pageId), 'id':pageId});
    });

    RetextApp.MenuView = new MenuView({'model':RetextApp.menuItems});

    var statusColl = new ApiStatusCollection();
    RetextApp.pages['status'] = new RetextApp.StatusView({'id':'status', 'model':statusColl});
    statusColl.add(new DefaultApiStatusModel());
    RetextApp.pages['status'].render();

    $('#mainmenu').append(RetextApp.MenuView.render().el);

    // Router
    var AppRouter = Backbone.Router.extend({
        routes:{
            "":"home",
            "*page":"showPage"
        },
        home:function () {
            this.showPage("login");
        },
        showPage:function (pageId) {
            _.each(RetextApp.pages, function (page) {
                if (page.id == pageId) {
                    $(page.el).show();
                } else {
                    $(page.el).hide();
                }
            });
            _.each(RetextApp.menuItems.models, function (menuItem) {
                menuItem.set('active', menuItem.get('id') == pageId);
            });
        }
    });

    var app = new AppRouter();
    Backbone.history.start();

});