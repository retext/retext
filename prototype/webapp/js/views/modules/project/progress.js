define([
    'models/projectprogress',
    'text!templates/modules/project/progress.html'
], function (Model, ModuleTemplate) {
    return Backbone.View.extend({
        template:_.template(ModuleTemplate),
        className: 'projectprogress',
        initialize:function () {
            this.model.bind("change", this.render, this);
        },
        render:function () {
            var data = [
                {id: 'total', label:'Gesamt', percent:Math.max(0.01, this.model.get('total').progress) * 100},
                {id: 'spelling', label:'Rechtschreibung', percent:Math.max(0.01, this.model.get('spellingApproved').progress) * 100},
                {id: 'content', label:'Inhalt', percent:Math.max(0.01, this.model.get('contentApproved').progress) * 100},
                {id: 'approval', label:'Freigabe', percent:Math.max(0.01, this.model.get('approved').progress) * 100}
            ];
            $(this.el).html(this.template({progresses:data}));
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
});
