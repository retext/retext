define([
    'events'
], function (Events) {
    var views = {};
    var el2view = {};
    var create = function (context, name, View, options) {
        if (typeof views[name] !== 'undefined') {
            views[name].undelegateEvents();
            if (typeof views[name].clean === 'function') {
                views[name].clean();
            }
        }
        var view = new View(options);
        views[name] = view;
        if (typeof context.children === 'undefined') {
            context.children = {};
            context.children[name] = view;
        } else {
            context.children[name] = view;
        }
        view.render();
        if (typeof views[name].complete === 'function') {
            views[name].complete();
        }
        Events.trigger('viewCreated');
        // Save for undelegate on removal
        if (typeof el2view[view.el] !== 'undefined') {
            el2view[view.el].undelegateEvents();
        }
        el2view[view.el] = view;
        return view;
    }
    return {
        create:create
    };
});