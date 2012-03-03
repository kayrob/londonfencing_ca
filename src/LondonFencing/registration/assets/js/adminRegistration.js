jQuery(document).ready(function($){
    
        $('#RQvalDATEStart_Date').datepicker({
                dateFormat : 'yy-mm-dd'
        });
        $('#RQvalDATEEnd_Date').datepicker({
                dateFormat : 'yy-mm-dd'
        });
        $('#RQvalTIMEStart_Time').timepicker({
                timeFormat: 'hh:mm TT',
                ampm: true
        });
        $('#RQvalTIMEEnd_Time').timepicker({
                timeFormat: 'hh:mm TT',
                ampm: true
        });
        $('#RQvalDATERegistration_Open').datepicker({
                dateFormat : 'yy-mm-dd'
        });
        $('#RQvalDATERegistration_Close').datepicker({
                dateFormat : 'yy-mm-dd'
        });
});

