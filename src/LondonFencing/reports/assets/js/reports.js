var checkDate = function(date1, date2){
    var start = date1.split('-');
    var end = date2.split('-');
    var sDate = new Date(start[0], start[1], start[2]);
    var eDate = new Date(end[0], end[1], end[2]);
    if (sDate.getTime() < eDate.getTime()){
        return true;
    }
    else{
        feedback('End Date Range must be greater than Start Date Range','Report Generation Issue:',2);
        return false;
    }
}

var checkErrors = function(){
    
    var reqURI = window.location.href.split('?');
    if (reqURI.length > 1 && reqURI[1].match(/rpt=([A-Za-z])&e=\d+/) !== false){
        var errInfo = reqURI[1].split('&');
        var errNo = errInfo[1].replace('e=','');
        var message = "";
        switch(errNo){
            case '1':
                message = "Report not created. All required fields were not submitted.";
                break;
            case '0':
                message = "Report not created. No data matched your query.";
                break;
            
        }
        feedback(message, 'Error: '+errInfo[0].replace('rpt=','')+' Report', 2);
    }
}

jQuery(document).ready(function($){
    
    $('#foundationStart').datepicker({
        dateFormat: 'yy-mm-dd'
    });
    $('#foundationEnd').datepicker({
        dateFormat: 'yy-mm-dd'
    });
    
    $('#frmFoundation').submit(function(){
            return checkDate($(':input[name=foundationStart]').val(), $(':input[name=foundationEnd]').val()); 
    });
    
    checkErrors();
    
    
});