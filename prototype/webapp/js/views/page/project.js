define([
    'vm',
    'views/page/base',
    'views/modules/container/list',
    'views/modules/project/breadcrumb',
    'views/forms/container',
    'text!templates/page/project.html',
    'models/project',
    'models/container'
], function (Vm, PageViewBase, ContainerListView, BreadCrumbModule, ContainerForm, ViewTemplate, ProjectModel, ContainerModel) {
    var View = PageViewBase.extend({
        template:_.template(ViewTemplate),
        events:{
            'click a.gui-toggle':'toggleCol'
        },
        initialize:function (options) {
            this.model = new ProjectModel({id:options.id});
        },
        render:function () {
            $(this.el).html(this.template({project:this.model.toJSON()}));
            $('#toggleleft').css({position:'absolute', top:'25%', left:0});
            $('#toggleright').css({position:'absolute', top:'25%', right:0});
            this.hiddenDiv = $('#hiddendiv');
            var containerList = Vm.create(this, 'current-container', ContainerListView, {el:$('#gui-current-container'), project:this.model});
            Vm.create(this, 'breadcrumb', BreadCrumbModule, {el:$('#gui-project-breadcrumb'), model:this.model});
            var project = this.model;
            containerList.on('containerSelected', function (model) {
                model.urlRoot = project.url() + '/container';
                Vm.create(this, 'current-element-form', ContainerForm, {el:$('#current-element-form'), model:model});
            });
            return this;
        },
        complete:function () {
            this.model.fetch(); // Will trigger update an subviews
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
    });
    return View;
});
