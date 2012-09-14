/**
 * Der Viewmanager kümmert sich um das Erstellen und zerstören von Views.
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'events'
], function (Events) {
    var views = {};
    var destroy = function (view) {
        view.undelegateEvents();
        if (typeof view.clean === 'function') {
            view.clean();
        }
        $(view.el).remove();
    };
    var create = function (context, name, View, options) {
        // Create new view
        var view = new View(_.isUndefined(options) ? {} : options);
        var replacedView = null;
        if (!_.isUndefined(views[name])) {
            destroy(views[name]);
        }
        // Save for undelegate on removal
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
        return view;
    };
    var updateExtras = function (el) {
        // Enable tooltips
        $(el).find('[rel=tooltip]').tooltip();
        $(el).find('label[title]').tooltip();
        // Extender dropdowns
        $('.extender-toggle').each(function (idx, el) {
            var el = $(el);
            el.click(function (ev) {
                $(el.data('target')).toggleClass('hidden');
                var hiddenIcons = el.find('i.hidden');
                if (hiddenIcons.length > 0) {
                    el.find('i').toggleClass('hidden');
                }
                console.log(el.find('i'));
            });

        });
    };
    return {
        create:create,
        destroy:destroy,
        updateExtras:updateExtras
    };
});
