define([
    'models/text',
    'models/container'
], function (TextModel, ContainerModel) {
    var Collection = Backbone.Collection.extend({
        parse:function (response) {
            return _.map(response, function (modelData) {
                if (modelData['@context'] == 'http://jsonld.retext.it/Container') return new ContainerModel(modelData);
                return new TextModel(modelData);
            });
        }
    });
    return Collection;
});
