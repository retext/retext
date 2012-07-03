/**
 * Formular zum Bearbeiten eines Texts
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'vm',
    'views/forms/item',
    'views/forms/texttype',
    'models/texttype',
    'text!templates/forms/text.html',
    'collections/texttype'
], function (Vm, ItemForm, TexttypeForm, TexttypeModel, ViewTemplate, TextTypeCollection) {
    return ItemForm.extend({
        template:_.template(ViewTemplate),
        initialize:function () {
            this.textTypes = new TextTypeCollection();
            this.textTypes.url = this.model.getRelation('http://jsonld.retext.it/TextType', true).get('href');
            this.textTypes.bind("reset", this.typesFetched, this);
        },
        render:function () {
            $(this.el).html(this.template({model:this.model.toJSON()}));
            return this;
        },
        complete:function () {
            this.textTypes.fetch();
        },
        typesFetched:function () {
            // Update Type-Input
            var typeInput = $(this.el).find('input[name=type]');
            var textTypeNames = _.map(_.filter(this.textTypes.models, function (textType) {
                return !_.isNull(textType.get('name'))
            }), function (textType) {
                return textType.get('name');
            });
            typeInput.typeahead({source:textTypeNames});
            this.renderTypeView();
        },
        renderTypeView:function () {
            var el = $(this.el);
            var model = this.model;
            el.after(Vm.create(this, 'texttype-form', TexttypeForm, {model:_.find(this.textTypes.models, function (textType) {
                return _.isEqual(model.get('type'), textType.get('name'));
            })}).el);
        }
    });
});
