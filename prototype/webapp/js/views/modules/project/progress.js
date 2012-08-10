/**
 * Zeigt den Fortschritt eines Projekts an
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/projectprogress',
    'text!templates/modules/project/progress.html'
], function (Model, ModuleTemplate) {
    return Backbone.View.extend({
        template:_.template(ModuleTemplate),
        className:'projectprogress',
        initialize:function () {
            this.model.bind("change", this.render, this);
        },
        render:function () {
            var data = [
                {id:'total', label:'Gesamt', percent:Math.max(0.01, this.model.get('total').progress) * 100, completed:this.model.get('total').yes, total:this.model.get('total').yes + this.model.get('total').no},
                {id:'spelling', label:'Rechtschreibung', percent:Math.max(0.01, this.model.get('spellingApproved').progress) * 100, completed:this.model.get('spellingApproved').yes, total:this.model.get('spellingApproved').yes + this.model.get('spellingApproved').no},
                {id:'content', label:'Inhalt', percent:Math.max(0.01, this.model.get('contentApproved').progress) * 100, completed:this.model.get('contentApproved').yes, total:this.model.get('contentApproved').yes + this.model.get('contentApproved').no},
                {id:'approval', label:'Freigabe', percent:Math.max(0.01, this.model.get('approved').progress) * 100, completed:this.model.get('approved').yes, total:this.model.get('approved').yes + this.model.get('approved').no}
            ];
            $(this.el).html(this.template({progresses:data}));
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
});
