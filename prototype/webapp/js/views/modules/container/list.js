define([
    'views/page/base',
    'models/container',
    'collections/container',
    'text!templates/modules/container/list.html'
], function (PageViewBase, ContainerModel, ContainerCollection, ViewTemplate) {
    var View = PageViewBase.extend({
        template:_.template(ViewTemplate),
        events:{
            'click button.act-new-container':'newContainer',
            'click div.gui-container':'selectContainer'
        },
        initialize:function (options) {
            this.project = options.project;
            this.model = new ContainerCollection({project:this.project});
            this.model.bind("change", this.render, this);
            this.model.bind("reset", this.render, this);
            this.model.bind("add", this.render, this);
        },
        render:function () {
            $(this.el).html(this.template({containers:this.model.toJSON()}));
            return this;
        },
        complete:function () {
            this.model.fetch();
        },
        newContainer:function () {
            var container = new ContainerModel({project:this.project});
            container.urlRoot = this.project.url() + '/container';
            var containers = this.model;
            container.save({}, {
                success:function (model, request) {
                    containers.add(model);
                }
            });
        },
        selectContainer:function (ev) {
            $('div.gui-container').removeClass('selected');
            var div = $($(ev.target).closest('div.gui-container'));
            div.addClass('selected');
        }
    });
    return View;
});
