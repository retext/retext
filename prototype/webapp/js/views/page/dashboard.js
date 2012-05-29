define([
    'views/page/base',
    'text!templates/page/dashboard.html'
], function (PageViewBase, PageTemplate) {
    var View = PageViewBase.extend({
        template:_.template(PageTemplate),
        render:function () {
            $(this.el).html(this.template({}));
            return this;
        }
    });
    return View;
});