/**
 * Basis-Klasse für die Anzeige der Elemente in den verschiedenen Bearbeitungsmodi
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
], function () {
    return Backbone.View.extend({
        tagName:'div',
	mode: null,
        initialize:function (options) {
		this.model = options.model;
		this.mode = options.mode;
            this.model.bind("change", this.change, this);
        },
        render:function () {
            var el = $(this.el);
            el.html(this.template({element:this.model.toJSON(), mode:this.mode}));
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
            _.each(['name', 'text', 'commentCount', 'type'], function (attribute) {
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
});
