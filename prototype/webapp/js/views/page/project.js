define([
    'vm',
    'views/page/base',
    'views/modules/container/list',
    'views/modules/project/breadcrumb',
    'views/forms/container',
    'text!templates/page/project.html',
    'models/project',
    'models/container',
    'collections/container',
    'collections/breadcrumb'
], function (Vm, PageViewBase, ContainerListView, BreadCrumbModule, ContainerForm, ViewTemplate, ProjectModel, ContainerModel, ContainerCollection, BreadcrumbCollection) {
    var View = PageViewBase.extend({
            template:_.template(ViewTemplate),
            events:{
                'click a.gui-toggle':'toggleCol'
            },
            initialize:function (options) {
                this.model = new ProjectModel({id:options.id});
                if (_.has(options, "parentContainerId")) {
                    this.parentContainer = new ContainerModel({id:options.parentContainerId});
                    this.newContainerModel = new ContainerModel({parent:options.parentContainerId});
                    this.parentContainer.bind('change', this.parentContainerFetched, this);
                } else {
                    this.parentContainer = this.model;
                    this.newContainerModel = new ContainerModel({project:this.model.get('id')});
                }

                this.parentContainer.bind('change', this.parentContainerFetched, this);
            },
            render:function () {
                $(this.el).html(this.template({project:this.model.toJSON()}));
                $('#toggleleft').css({position:'absolute', top:'25%', left:0});
                $('#toggleright').css({position:'absolute', top:'25%', right:0});
                this.hiddenDiv = $('#hiddendiv');
                return this;
            },
            parentContainerFetched:function () {
                var containersCollection = new ContainerCollection();
                containersCollection.url = this.parentContainer.getRelation('http://jsonld.retext.it/Container', true).get('href');
                var containerList = Vm.create(this, 'current-container', ContainerListView, {el:$('#gui-current-container'), model:containersCollection, newContainerModel:this.newContainerModel});
                var breadcrumbCollection = new BreadcrumbCollection();
                breadcrumbCollection.url = this.parentContainer.getRelation('http://jsonld.retext.it/Breadcrumb', true).get('href');
                Vm.create(this, 'breadcrumb', BreadCrumbModule, {el:$(this.el).find('div.view-breadcrumb'), model:breadcrumbCollection, project:this.model});
                var project = this.model;
                containerList.on('containerSelected', function (model) {
                    Vm.create(this, 'current-element-form', ContainerForm, {el:$('#current-element-form'), model:model});
                });
            },
            complete:function () {
                this.model.fetch(); // Will trigger update an subviews
                if (!_.isEqual(this.model, this.parentContainer)) this.parentContainer.fetch();
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
                var main = $('#project-main');
                main.removeClass('span' + main.data('currentspan'));
                var mainSpan = parseInt(main.data('currentspan'), 10) + span;
                main.data('currentspan', mainSpan);
                main.addClass('span' + mainSpan);
            },
            openCol:function (a) {
                var div = a.data('div');
                var span = parseInt(div.data('openspan'), 10);
                var main = $('#project-main');
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
