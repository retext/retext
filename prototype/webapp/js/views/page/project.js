define([
    'vm',
    'views/page/base',
    'views/modules/element/list',
    'views/modules/project/breadcrumb',
    'views/forms/container',
    'views/forms/text',
    'text!templates/page/project.html',
    'models/project',
    'models/container',
    'models/text',
    'collections/element',
    'collections/breadcrumb'
], function (Vm, PageViewBase, ElementListView, BreadCrumbModule, ContainerForm, TextForm, ViewTemplate, ProjectModel, ContainerModel, TextModel, ElementCollection, BreadcrumbCollection) {
    var View = PageViewBase.extend({
            template:_.template(ViewTemplate),
            events:{
                'click a.gui-toggle':'toggleCol'
            },
            initialize:function () {
                // IDs are passed as in the model param
                var projectId = this.model.id;
                var parentContainerId = this.model.parentContainerId;
                this.model = new ProjectModel({id:projectId});
                this.parentContainer = new ContainerModel({id:parentContainerId});
                this.newContainerModel = new ContainerModel({parent:parentContainerId});
                this.newTextModel = new TextModel({parent:parentContainerId});
                this.parentContainer.bind('change', this.parentContainerFetched, this);
            },
            render:function () {
                $(this.el).html(this.template({project:this.model.toJSON()}));
                return this;
            },
            parentContainerFetched:function () {
                var elementCollection = new ElementCollection();
                elementCollection.url = this.parentContainer.getRelation('http://jsonld.retext.it/Element', true).get('href');
                var elementList = Vm.create(this, 'current-container', ElementListView, {el:$('#gui-current-container'), model:elementCollection, newContainerModel:this.newContainerModel, newTextModel:this.newTextModel});
                var breadcrumbCollection = new BreadcrumbCollection();
                breadcrumbCollection.url = this.parentContainer.getRelation('http://jsonld.retext.it/Breadcrumb', true).get('href');
                Vm.create(this, 'breadcrumb', BreadCrumbModule, {el:$(this.el).find('div.view-breadcrumb'), model:breadcrumbCollection, project:this.model});
                var project = this.model;
                elementList.on('elementSelected', function (model) {
                    var form;
                    if (model.get('@context') == 'http://jsonld.retext.it/Container') {
                        // Vm.create(this, 'current-element-form', ContainerForm, {el:$('#current-element-form'), model:new ContainerModel(model.toJSON())});
                        form = Vm.create(this, 'current-element-form', ContainerForm, {model:model});
                    } else {
                        // Vm.create(this, 'current-element-form', TextForm, {el:$('#current-element-form'), model:new TextModel(model.toJSON())});
                        form = Vm.create(this, 'current-element-form', TextForm, {model:model});
                    }
                    $('#gui-edit-forms').html(form.el);
                }, this);
                elementList.on('elementsReordered', function (order) {
                    this.parentContainer.save({childOrder:order}, {wait:true, silent:true});
                }, this);
            },
            complete:function () {
                this.model.fetch(); // Will trigger update an subviews
                this.parentContainer.fetch();
            },
            toggleCol:function (ev) {
                var a = $(ev.target).closest('a');
                var closeIcon = a.data('closeicon');
                var openIcon = a.data('openicon');
                var icon = a.children('i:first');

                if (icon.hasClass(closeIcon)) {
                    icon.removeClass(closeIcon);
                    icon.addClass(openIcon);
                    this.closeCol(a);
                } else {
                    icon.removeClass(openIcon);
                    icon.addClass(closeIcon);
                    this.openCol(a);
                }
            },
            closeCol:function (a) {
                var div = $(a.data('col'));
                div.detach();
                a.data('div', div);
                var span = parseInt(div.data('openspan'), 10);
                var main = $(this.el).find('div.project-main');
                main.removeClass('span' + main.data('currentspan'));
                var mainSpan = parseInt(main.data('currentspan'), 10) + span;
                main.data('currentspan', mainSpan);
                main.addClass('span' + mainSpan);
            },
            openCol:function (a) {
                var div = a.data('div');
                var span = parseInt(div.data('openspan'), 10);
                var main = $(this.el).find('div.project-main');
                main.removeClass('span' + main.data('currentspan'));
                var mainSpan = parseInt(main.data('currentspan'), 10) - span;
                main.data('currentspan', mainSpan);
                main.addClass('span' + mainSpan);

                if (div.data('align') == 'left') {
                    $(this.el).find('div.gui-cols:first').prepend(div);
                } else {
                    $(this.el).find('div.gui-cols:first').append(div);
                }
            }
        })
        ;
    return View;
})
;
