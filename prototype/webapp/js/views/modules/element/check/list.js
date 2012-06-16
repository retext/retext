define([
    'events',
    'views/modules/element/list'
], function (Events, BaseListView) {
    return BaseListView.extend({
        mode:'check',
        postChange:function (model, info) {
            if (_.has(info.changes, 'approved') || _.has(info.changes, 'contentApproved') || _.has(info.changes, 'spellingApproved')) {
                this.loadComments(model);
                Events.trigger('projectProgressChanged');
            }
        }
    });
});
