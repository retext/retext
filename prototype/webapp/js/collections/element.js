/**
 * Collection
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/text',
    'models/container'
], function (TextModel, ContainerModel) {
    return Backbone.Collection.extend({
        parse:function (response) {
            /**
             * Je nach @context korrektes Model in der Liste verwenden
             */
            return _.map(response, function (modelData) {
                var model;
                if (modelData['@context'] == 'http://jsonld.retext.it/Container') {
                    model = new ContainerModel();
                } else {
                    model = new TextModel();
                }
                model.set(model.parse(modelData));
                return model;
            });
        }
    });
});
