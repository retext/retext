define([
    'views/forms/item',
    'text!templates/forms/texttype.html'
], function (ItemForm, ViewTemplate) {
    var FormView = ItemForm.extend({
        template:_.template(ViewTemplate)
    });
    return FormView;
});
