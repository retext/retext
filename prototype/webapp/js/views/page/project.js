define([
    'views/page/base',
    'text!templates/page/project.html',
    'models/project'
], function (PageViewBase, ViewTemplate, ProjectModel) {
    var View = PageViewBase.extend({
        template:_.template(ViewTemplate),
        events:{
            'click a.gui-close':'closeCol',
            'click a.gui-open':'openCol'
        },
        initialize:function (options) {
            this.model = new ProjectModel({id:options.id});
            this.model.bind("change", this.render, this);
            this.model.bind("reset", this.render, this);
        },
        render:function () {
            $(this.el).html(this.template({project: this.model.toJSON()}));
            $('#openleft').css({position:'absolute', top:'60px', left:0, display:'none'});
            $('#openright').css({position:'absolute', top:'60px', right:0, display:'none'});
            return this;
        },
        complete:function () {
            this.model.fetch();
        },
        closeCol:function (ev) {
            var a = $(ev.target).closest('a');
            var div = $(a.closest('div.gui-closeable'));
            var span = parseInt(div.data('openspan'), 10);
            div.hide();
            var main = $('#project-main');
            main.removeClass('span' + main.data('currentspan'));
            var mainSpan = parseInt(main.data('currentspan'), 10) + span;
            main.data('currentspan', mainSpan);
            main.addClass('span' + mainSpan);
            $(div.data('openbutton')).css({display:'block'});
        },
        openCol:function (ev) {
            var a = $(ev.target).closest('a');
            a.css({display:'none'});

            var div = $(a.data('opencol'));
            var span = parseInt(div.data('openspan'), 10);

            var main = $('#project-main');
            main.removeClass('span' + main.data('currentspan'));
            var mainSpan = parseInt(main.data('currentspan'), 10) - span;
            main.data('currentspan', mainSpan);
            main.addClass('span' + mainSpan);

            div.show();
        }
    });
    return View;
});
