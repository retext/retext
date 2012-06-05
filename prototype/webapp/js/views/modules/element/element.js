define([
], function () {
    var View = Backbone.View.extend({
        tagName:'div',
        initialize:function () {
            this.model.bind("change", this.render, this);
        },
        render:function () {
            var el = $(this.el);
            el.html(this.template({element:this.model.toJSON()}));
            el.data('id', this.model.get('id'));
            if (this.model.get('selected')) {
                el.addClass('selected');
            } else {
                el.removeClass('selected');
            }
            return this;
        }
    });
    return View;
});
