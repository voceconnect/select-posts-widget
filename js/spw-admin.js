/*global jQuery,_ */


jQuery(document).ready(function ($) {

    /**
     * Trigger change on input.title in the widget, changes to hidden elements aren't triggered, this forces the update
     */
    var updateWidgetDebounced = _.debounce( function($element) {

        var $textInput = $element.closest('.spw-form').find('input.title');
        $textInput.trigger('change');

    }, 250 );


    /**
     * If sort event triggered on jQueryUI Sortable, update the widget by triggering change on input.title on the widget
     */
    $(document).on('sort', '.psu-box .ui-sortable', function(){

        updateWidgetDebounced( $(this));

    });


    /**
     * If a tr is added or removed from the DOM, trigger change on input.title
     */
    $(document).on('DOMNodeInserted DOMNodeRemoved', '.psu-box .ui-sortable tr', function(){
        var $that = $(this).closest('.psu-box');

            updateWidgetDebounced($that);

    });

});