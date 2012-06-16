define([
    'views/modules/element/element',
    'text!templates/modules/element/check/text.html'
], function (ElementView, ViewTemplate) {
    var View = ElementView.extend({
        template:_.template(ViewTemplate),
        className:'gui-element gui-text gui-check-text',
        events:{
            'click .dropdown-menu a':'changeState'
        },
        changeState:function (ev) {
            var a = $(ev.target).closest('a');
            var firstButton = $(ev.target).closest('.btn-group').find('.btn:first');
            var stateIcon = a.find('i');
            var firstButtonIcon = firstButton.find('i');
            firstButtonIcon.attr('title', stateIcon.attr('title'));
            firstButtonIcon.attr('class', stateIcon.attr('class'));
            var data = {};
            data[a.data('state-name')] = a.data('state-value');
            this.model.save(data, {wait:true});
        },
        postChange:function () {
            this.visualizeProgress();
        },
        postRender:function () {
            this.visualizeProgress();
        },
        visualizeProgress:function () {
            var el = $(this.el);
            el.removeClass('progress-danger');
            el.removeClass('progress-warning');
            el.removeClass('progress-success');
            var approvedProgress = this.model.get('approvedProgress');
            if (approvedProgress < 0.15) {
                el.addClass('progress-danger');
            } else if (approvedProgress < 0.75) {
                el.addClass('progress-warning');
            } else if (approvedProgress == 1.0) {
                el.addClass('progress-success');
            }
        }
    });
    return View;
});
