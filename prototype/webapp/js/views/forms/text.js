define([
    'views/forms/item',
    'text!templates/forms/text.html'
], function (ItemForm, ViewTemplate) {
    var FormView = ItemForm.extend({
        template:_.template(ViewTemplate)
    });
    return FormView;
});