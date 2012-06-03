define([
    'models/relation'
], function (RelationModel) {
    var BaseModel = Backbone.Model.extend({
        getRelation:function (context, list) {
            if (typeof list == 'undefined') list = false;
            var rel = new RelationModel();
            if (!_.has(this.attributes, '@relations')) {
                console.error('No @relations in ', this.attributes);
                return rel;
            }
            _.each(this.get('@relations'), function (relation) {
                if (relation.relatedcontext == context && relation.list == list) rel.set(relation);
            });
            return rel;
        }
    });
    return BaseModel;
});