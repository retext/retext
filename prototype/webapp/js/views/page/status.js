/**
 * Zeigt den API-Status an
 *
 * @author Markus Tacker <m@tckr.cc>
 */
define([
    'views/page/base',
    'collections/apistatus',
    'models/defaultapistatus',
    'text!templates/page/status.html'
], function (PageViewBase, ApiStatusCollection, DefaultApiStatusModel, StatusPageTemplate) {
    return PageViewBase.extend({
        template:_.template(StatusPageTemplate),
        initialize:function () {
            this.model = new ApiStatusCollection();
            this.model.add(new DefaultApiStatusModel());
            this.model.bind("change", this.render, this);
            this.model.bind("reset", this.render, this);
        },
        render:function () {
            $(this.el).html(this.template(this.model.at(0).toJSON()));
            return this;
        },
        complete:function () {
            this.model.fetch();
        }
    });
});