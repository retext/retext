define([
    'views/modules/element/element',
    'text!templates/modules/element/check/text.html',
    'text!templates/modals/approve.html'
], function (ElementView, ViewTemplate, ApproveModalTemplate) {
    var View = ElementView.extend({
        template:_.template(ViewTemplate),
        modalTemplate:_.template(ApproveModalTemplate),
        className:'gui-element gui-text gui-check-text',
        events:{
            'click .dropdown-menu a':'changeState'
        },
        changeState:function (ev) {
            var a = $(ev.target).closest('a');
            var firstButton = $(ev.target).closest('.btn-group').find('.btn:first');
            var stateIcon = a.find('i');
            var firstButtonIcon = firstButton.find('i');
            var model = this.model;
            var submitStatusChange = function (comment) {
                var data = {};
                data[a.data('state-name')] = a.data('state-value');
                data.comment = comment;
                firstButtonIcon.attr('title', stateIcon.attr('title'));
                firstButtonIcon.attr('class', stateIcon.attr('class'));
                model.save(data, {wait:true});
            };
            if (a.data('state-value')) {
                submitStatusChange(null);
                return; // Modal nicht anzeigen, wenn ein Status akzeptiert wird
            }
            // Modal anzeigen, wenn ein Status abgelehnt wird
            var submitStatusChangeComment = function () {
                var comment = $('#approveModal input').attr('value');
                $('#approveModal').modal('hide');
                submitStatusChange(comment);
            };
            $(document.body).append(this.modalTemplate({title:a.attr('title')}));
            $('#approveModal').modal();
            $('#approveModal input').focus();
            $('#approveModal form').submit(function (ev) {
                ev.preventDefault();
                submitStatusChangeComment();

            });
            $('#approveModal').on('hidden', function () {
                $('#approveModal').remove();
            });
            $('#approveModal .btn-primary').on('click', function () {
                submitStatusChangeComment();
            });
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
