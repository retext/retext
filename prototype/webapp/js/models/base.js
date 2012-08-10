/**
 * Basisklasse für alle Models.
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/relation'
], function (RelationModel) {
    return Backbone.Model.extend({
        /**
         * Sucht nach einer Relation in diesem Model
         *
         * @param context
         * @param list
         * @param role
         * @return {*}
         */
        getRelation:function (context, list, role) {
            if (_.isUndefined(list)) list = false;
            var rel = new RelationModel();
            if (!_.has(this.attributes, '@relations')) {
                console.error('No @relations in ', this.attributes);
                return rel;
            }
            var matched = false;
            _.each(this.get('@relations'), function (relation) {
                if (relation.relatedcontext == context && relation.list == list) {
                    if (!_.isUndefined(role)) { // Optional auf Rolle prüfen
                        if (_.isEqual(role, relation.role)) {
                            if (matched) console.error('Already matched a relation for ', {context:context, list:list, role:role});
                            matched = true;
                            rel.set(relation);
                        }
                    } else {
                        if (matched) console.error('Already matched a relation for ', {context:context, list:list, role:role});
                        rel.set(relation);
                        matched = true;
                    }
                }
            });
            if (!matched) {
                console.error('No relation found for ', {context:context, list:list, role:role});
            }
            return rel;
        }
    });
});