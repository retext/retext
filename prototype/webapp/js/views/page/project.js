define([
    'vm',
    'views/page/base',
    'views/modules/project/breadcrumb',
    'views/modules/project/mode-switcher',
    'text!templates/page/project.html',
    'models/projectView',
    'models/project',
    'models/container',
    'collections/breadcrumb'
], function (Vm, PageViewBase, BreadCrumbView, ModeSwitcherView, ViewTemplate, ProjectViewModel, ProjectModel, ContainerModel, BreadcrumbCollection) {
    var View = PageViewBase.extend({
            template:_.template(ViewTemplate),
            events:{
                'click a.gui-toggle':'toggleCol'
            },
            initialize:function () {
                // IDs are passed in the model param
                var projectId = this.model.id;
                var parentContainerId = this.model.parentContainerId;
                var mode = this.model.mode;
                this.project = new ProjectModel({id:projectId});
                this.parentContainer = new ContainerModel({id:parentContainerId});
                this.parentContainer.bind('change', this.parentContainerFetched, this);
                this.model = new ProjectViewModel({project:this.project, container:this.parentContainer, mode:mode});
            },
            render:function () {
                var el = $(this.el);
                el.html(this.template({project:this.project.toJSON()}));
                el.find('.view-mode-switcher').html(Vm.create(this, 'mode-switcher', ModeSwitcherView, {model:this.model}).el);
                return this;
            },
            parentContainerFetched:function () {
                // Lade View je nach aktuellem Modus
                var that = this;
                require(['views/modules/element/' + this.model.get('mode') + '/list'], function (ElementListView) {
                    that.viewFetched(ElementListView);
                });
            },
            viewFetched:function (elementListView) {
                var el = $(this.el);
                // Die Unter-View kann anfordern, dass ein Formular zum Editieren angezeigt wird
                var elementList = Vm.create(this, 'current-container', elementListView, {model:this.model});
                el.find('div.view-current-container').html(elementList.el);
                elementList.on('showForm', function (form, model) {
                    el.find('div.view-edit-forms').html(Vm.create(this, 'current-element-form', form, {model:model}).el);
                });
                elementList.on('showHistory', function (view, model) {
                    el.find('div.view-history').html(Vm.create(this, 'current-element-history', view, {model:model}).el);
                });
                var breadcrumbCollection = new BreadcrumbCollection();
                breadcrumbCollection.url = this.parentContainer.getRelation('http://jsonld.retext.it/Breadcrumb', true).get('href');
                Vm.create(this, 'breadcrumb', BreadCrumbView, {el:$(this.el).find('div.view-breadcrumb'), model:breadcrumbCollection, project:this.project});
            },
            complete:function () {
                this.project.fetch(); // Will trigger update an subviews
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
