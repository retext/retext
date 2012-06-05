define([
    'views/forms/item',
    'text!templates/forms/text.html',
    'collections/texttype'
], function (ItemForm, ViewTemplate, TextTypeCollection) {
    var FormView = ItemForm.extend({
        template:_.template(ViewTemplate),
        complete: function()
        {
            var typeInput = $(this.el).find('input[name=type]');
            var texttypeCollection = new TextTypeCollection();
            texttypeCollection.url = this.model.getRelation('http://jsonld.retext.it/TextType', true).get('href');
            texttypeCollection.fetch({
                success: function(collection) {
                    var textTypeNames = _.map(_.filter(collection.models, function(textType) { return !_.isNull(textType.get('name')) }), function(textType) { return textType.get('name'); });
                    typeInput.typeahead({source: textTypeNames});
                }
            })
        }
    });
    return FormView;
});