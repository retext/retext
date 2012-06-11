define([
    'views/modules/history',
    'text!templates/modules/textcomments.html'
], function (HistoryView, ModuleTemplate) {
    return HistoryView.extend({
        template:_.template(ModuleTemplate),
        tagName:'div',
        className:'textcomments',
        events:{
            'submit form':'addComment',
            'keyup input':'onKeyUp'
        },
        addComment:function (ev) {
            ev.preventDefault();
        },
        onKeyUp:function (ev) {
            if (ev.keyCode == 13) {
                this.createComment($(ev.target).closest('input').attr('value'));
            }
        },
        createComment:function (comment) {
            var el = $(this.el);
            el.find('.progress').removeClass('hidden');
            var commentModel = new this.model.model();
            commentModel.urlRoot = this.model.url;
            var collection = this.model;
            commentModel.save({comment:comment}, {
                success:function (comment) {
                    collection.add(comment, {at:0});
                    el.find('.progress').addClass('hidden');
                },
                error:function () {
                    el.find('.progress').addClass('hidden');
                }
            })
        }
    });
})
;
