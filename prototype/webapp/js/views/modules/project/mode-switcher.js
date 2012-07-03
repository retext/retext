define([
    'collections/mode',
    'models/mode',
    'text!templates/modules/project/mode-switcher.html'
], function (ModeCollection, ModeModel, Template) {
    return Backbone.View.extend({
        template:_.template(Template),
        className:'btn-group',
        initialize:function () {
            this.modes = new ModeCollection();
            this.modes.add(new ModeModel({id:'structure', label:'Definieren', 'icon':'icon-list-alt'}));
            this.modes.add(new ModeModel({id:'write', label:'Schreiben', 'icon':'icon-edit'}));
            this.modes.add(new ModeModel({id:'check', label:'Prüfen', 'icon':'icon-check'}));
            this.modes.add(new ModeModel({id:'translate', label:'Übersetzen', 'icon':'icon-random'}));
            this.selectedMode = this.modes.get(this.model.get('mode'));

        },
        render:function () {
            $(this.el).html(this.template({model:this.model.toJSON(), modes:this.modes.toJSON(), selectedMode:this.selectedMode.toJSON()}));
            return this;
        }
    });
})
;
