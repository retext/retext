define([
    'text!templates/modules/container/item.html'
], function (ViewTemplate) {
    var View = Backbone.View.extend({
        template:_.template(ViewTemplate),
        tagName:'div',
        className:'gui-container',
        initialize:function () {
            this.model.bind("change", this.render, this);
        },
        render:function () {
            var el = $(this.el);
            el.html(this.template({container:this.model.toJSON()}));
            if (this.model.get('selected')) {
                el.addClass('selected');
            } else {
                el.removeClass('selected');
            }
            el.data({order:this.model.get('order'), id:this.model.get('id')});
            el.attr('draggable', 'draggable');
            return this;
        }
    });
    return View;
});
