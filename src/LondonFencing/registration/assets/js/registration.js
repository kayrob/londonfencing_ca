(function ($) {
    $.extend($.datepicker, {

        // Reference the orignal function so we can override it and call it later
        _inlineDatepicker2: $.datepicker._inlineDatepicker,

        // Override the _inlineDatepicker method
        _inlineDatepicker: function (target, inst) {

            // Call the original
            this._inlineDatepicker2(target, inst);

            var beforeShow = $.datepicker._get(inst, 'beforeShow');

            if (beforeShow) {
                beforeShow.apply(target, [target, inst]);
            }
        }
    });
}(jQuery));
jQuery(document).ready(function($){
    
        $('#RQvalDATEbirthDate').datepicker({
                dateFormat : 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: "-80:+0",
                beforeShow: function(input){
                    var top = $("#" + input.id).position().top + 40;
                    setTimeout(function(){
                        $('#ui-datepicker-div').css({'top':top});      
                    },1);
                }
        });
        
        $('#terms').fancybox();
        
        $('#regSubmit').click(function(){
            
                if ($('#RQvalDATEbirthDate').val() != ""){
                    
                    var bdate = $('#RQvalDATEbirthDate').val().split('-');
                    var bdateTs = new Date(bdate[0],bdate[1], bdate[2]).getTime();
                    var today = new Date().getTime();
                    
                    if ((today - bdateTs)/(1000*60*60*24*365) < 18){
                        if ($('#OPvalALPHparentName').val() != ""){
                            var minorConsent = $('#dvMConsent').html();
                            minorConsent = minorConsent.replace('%MINORNAME%',$('#RQvalALPHfirstName').val()+' '+$('#RQvalALPHlastName').val());
                            minorConsent = minorConsent.replace('%PARENTNAME%', $('#OPvalALPHparentName').val());
                            $('#dvMConsent').html(minorConsent);
                            $('#terms').attr('href','#dvMConsent').trigger('click');
                        }
                        else{
                            //submit to get the error
                          $('#begIntRegForm').submit();
                        }
                    }
                    else{
                        $('#terms').attr('href','#dvAConsent').trigger('click');
                    }
                }
                else{
                    //submit to get the error
                    $('#begIntRegForm').submit();
                }
            
        });
        
});
var sendReg = function(){
    
    if (document.getElementById('begIntRegForm')){
        document.getElementById('begIntRegForm').submit();
    };
}

