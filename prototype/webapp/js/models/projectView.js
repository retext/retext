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
            project:null,
            container:null,
            mode:null
        }
    });
});
