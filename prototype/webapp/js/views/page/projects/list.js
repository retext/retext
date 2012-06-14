define([
    'vm',
    'views/page/base',
    'views/modules/project/listing',
    'collections/project',
    'text!templates/page/projects/list.html'
], function (Vm, PageViewBase, ProjectListingView, ProjectCollection, ProjectListPageTemplate) {
    return PageViewBase.extend({
        events:{
            'click button.details':'selectProject'
        },
        render:function () {
            var el = $(this.el);
            el.html(ProjectListPageTemplate);
            el.find('div.view-projectlist').html(Vm.create(this, 'projectlisting', ProjectListingView, {model:new ProjectCollection()}).el);
            return this;
        },
        selectProject:function (ev) {
            ev.preventDefault();
            var div = $($(ev.target).closest('div'));
            var h2 = div.find('h2');
            $(this.el).find('div.view-projectinfo').html('<h2>' + h2.html() + '</h2><' + div.tagName + ' class="' + div.attr('class') + '">' + div.find('.projectprogress').html() + '</' + div.tagName + '>');
        }
    });
});
