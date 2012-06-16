define([
    'events',
    'views/modules/element/list',
    'views/modules/texthistory',
    'collections/texthistory'
], function (Events, BaseListView, TextHistoryView, TextHistoryCollection) {
    return BaseListView.extend({
        preferredContext:'info',
        mode:'write',
        events:{
            'click div.gui-element':'selectElement',
            'focus input':'selectElement',
            'focus textarea':'selectElement'
        },
        selectChange:function (selectedModel) {
            if (selectedModel.get('@context') == 'http://jsonld.retext.it/Text') {
                var historyCollection = new TextHistoryCollection();
                historyCollection.url = selectedModel.getRelation('http://jsonld.retext.it/TextVersion', true).get('href');
                this.trigger('contextInfo:show', 'history', TextHistoryView, historyCollection);
            } else {
                this.trigger('contextInfo:clear', 'history');
            }
        }
    });
});
