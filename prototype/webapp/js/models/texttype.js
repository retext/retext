/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'remote',
    'models/element'
], function (Remote, ElementModel) {
    var Model = ElementModel.extend({
        urlRoot:Remote.apiUrlBase + 'texttype',
        defaults:{
            id:null,
            project:null,
            name:null,
            fontsize:null,
            fontname:null,
            description:null,
            multiline: false
        }
    });
    return Model;
});