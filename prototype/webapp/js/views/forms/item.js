define([
    'views/page/base'
], function (PageViewBase) {
    var FormView = PageViewBase.extend({
        events:{
            'click button.gui-save':'save',
            'click button.gui-delete':'delete'
        },
        initialize:function () {
            this.model.bind("change", this.render, this);
            this.model.bind("reset", this.render, this);
        },
        render:function () {
            $(this.el).html(this.template(this.model.toJSON()));
            return this;
        },
        save:function (ev) {
            ev.preventDefault();
            var model = this.model;
            var form = $(this.el);
            var data = {};
            _.each(this.model.attributes, function (value, name) {
                if (name.substr(0, 1) == '@') return;
                var inp = form.find('input[name=' + name + ']');
                if (inp.length == 1) {
                    data[name] = inp.attr('value');
                }
            });
            model.url = model.get('@subject');
            model.save(data, {
                success:function (updatedModel) {
                    model.set(updatedModel.attributes);
                }
            });
        },
        delete:function (ev) {
            ev.preventDefault();
        }
    });
    return FormView;
});