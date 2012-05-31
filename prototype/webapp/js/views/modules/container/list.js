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
            _.extend(this, Backbone.Events);
            this.project = options.project;
            this.model = new ContainerCollection({project:this.project});
            // TODO: Hier nicht bei einem Update eines Models die ganze Liste aktualisieren.
            this.model.bind("change", this.render, this);
            this.model.bind("reset", this.render, this);
            this.model.bind("add", this.render, this);
        },
        render:function () {
            // TODO: Für jeden Container eine eigene View erzeugen
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
            // TODO: Merken
            $('div.gui-container').removeClass('selected');
            var div = $($(ev.target).closest('div.gui-container'));
            div.addClass('selected');
            this.trigger('containerSelected', this.model.get(div.data('id')));
        }
    });
    return View;
});
