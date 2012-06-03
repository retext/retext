define([
    'events'
], function (Events) {
    var views = {};
    var el2view = {};
    var create = function (context, name, View, options) {
        if (!_.isUndefined(views[name])) {
            views[name].undelegateEvents();
            if (typeof views[name].clean === 'function') {
                views[name].clean();
            }
        }
        var view = new View(_.isUndefined(options) ? {} : options);
        views[name] = view;
        if (_.isUndefined(context.children)) {
            context.children = {};
            context.children[name] = view;
        } else {
            context.children[name] = view;
        }
        view.render();
        if (_.isFunction(views[name].complete)) {
            views[name].complete();
        }
        Events.trigger('viewCreated:' + name);
        // Save for undelegate on removal
        var viewHash = view.el.id == "" ? view.el.tagName + '.' + view.el.className : '#' + view.el.id;
        if (!_.isUndefined(el2view[viewHash])) {
            el2view[viewHash].undelegateEvents();
        }
        el2view[viewHash] = view;
        return view;
    }
    return {
        create:create
    };
});