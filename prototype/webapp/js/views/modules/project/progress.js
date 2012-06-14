define([
    'models/projectprogress',
    'text!templates/modules/project/progress.html'
], function (Model, ModuleTemplate) {
    return Backbone.View.extend({
        template:_.template(ModuleTemplate),
        initialize:function () {
            this.model.bind("change", this.render, this);
        },
        render:function () {
            var data = [
                {label:'Gesamt', percent:Math.max(0.01, this.model.get('total').progress) * 100},
                {label:'Rechtschreibung', percent:Math.max(0.01, this.model.get('spellingApproved').progress) * 100},
                {label:'Inhalt', percent:Math.max(0.01, this.model.get('contentApproved').progress) * 100},
                {label:'Freigabe', percent:Math.max(0.01, this.model.get('approved').progress) * 100}
            ];
            $(this.el).html(this.template({progresses:data}));
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
});
