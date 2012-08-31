/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'remote',
    'models/element'
], function (Remote, ElementModel) {
    return ElementModel.extend({
        urlRoot:Remote.apiUrlBase + 'container',
        defaults:{
            id:null,
            project:null,
            name:null,
            parent:null,
            childCount:0
        }
    });
});