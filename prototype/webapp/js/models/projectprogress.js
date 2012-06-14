define([
    'events',
    'models/base'
], function (Events, BaseModel) {
    return BaseModel.extend({
        defaults:{
            approved:{yes:0, no:0, progress:0, percent:0},
            spellingApproved:{yes:0, no:0, progress:0, percent:0},
            contentApproved:{yes:0, no:0, progress:0, percent:0},
            total:{yes:0, no:0, progress:0, percent:0}
        },
        initialize:function () {
            Events.on('projectProgressChanged', this.fetch, this);
        }
    });
});