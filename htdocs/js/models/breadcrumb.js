/**
 * Model
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'models/base'
], function (BaseModel) {
    return BaseModel.extend({
        defaults:{
            id:null,
            name:'Kein Name'
        }
    });
});