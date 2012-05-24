define([
], function () {
    var MenuItemView = Backbone.View.extend({
        'tagName':'li',
        'render':function () {
            var children = this.model.get('children');
            var el = $(this.el);
            if (children.length > 0) {
                el.append(_.template('<a href="#<%= id %>" class="dropdown-toggle" data-toggle="dropdown"><%= label %><b class="caret"></b></a><ul class="dropdown-menu"></ul>', this.model.toJSON()));
                var ul = el.find('ul');
                _.each(children, function(child) {
                    ul.append(_.template('<a href="#<%= id %>"><%= label %></a>', child.toJSON()));
                });
                el.addClass('dropdown');
            } else {
                el.append(_.template('<a href="#<%= id %>"><%= label %></a>', this.model.toJSON()));
            }
            return this;
        }
    });
    return MenuItemView;
});