define([
], function () {
    var View = Backbone.View.extend({
        tagName:'div',
        initialize:function () {
            this.model.bind("change", this.change, this);
        },
        render:function () {
            var el = $(this.el);
            el.html(this.template({element:this.model.toJSON()}));
            el.data('id', this.model.get('id'));
            this.postRender();
            return this;
        },
        postRender:function () {
        },
        change:function () {
            var el = $(this.el);
            if (this.model.get('selected')) {
                el.addClass('selected');
            } else {
                el.removeClass('selected');
            }
            _.each(['name', 'text', 'commentCount'], function (attribute) {
                var val = this.model.get(attribute);
                var attrElem = el.find('[data-attribute="' + attribute + '"]');
                attrElem.html(
                    (_.isString(val) && val.length == 0)
                        || _.isNull(val) ? attrElem.data('empty') : val);
            }, this);
            this.postChange();
        },
        postChange:function () {
        }
    });
    return View;
});
