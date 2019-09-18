(function( $ ){
$.fn.Values = function()
    {
        var myform = $(this).find(":input");
        // Find disabled inputs, and remove the "disabled" attribute
        var opened = myform.find(':disabled').removeAttr('disabled');
        // serialize the form
        var serialized = myform.serialize();
        // re-disabled the set of inputs that you previously enabled
        opened.attr('disabled','disabled');

        var index = serialized.indexOf("+");
        while(index != -1){
            serialized = serialized.replace("+","%2B");
            index = serialized.indexOf("+");
        }
        return serialized;
    }
})( jQuery );