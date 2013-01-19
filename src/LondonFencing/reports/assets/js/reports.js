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

var sendTaxReceipts = function(){
    $.post('/src/LondonFencing/reports/assets/sendTaxReceipts.php',{rpt:'Send_Tax_Receipts'}, function(data){
        if (data == 'success'){
            feedback('All receipts were sent!','Tax Receipts:',1);
        }
        else{
            feedback('Error: Some receipts could not be sent','Tax Receipts',2);
        }
    });
}

var checkErrors = function(){
    
    var reqURI = window.location.href.split('?');
    if (reqURI.length > 1 && reqURI[1].match(/rpt=([A-Za-z])&e=\d+/) != null){
        var errInfo = reqURI[1].split('&');
        var errNo = errInfo[1].replace('e=','');
        var reportTitle = errInfo[0].replace('rpt=','');
        reportTitle = reportTitle.replace('_', ' ');
        var message = "";
        switch(errNo){
            case '2':
                message = "Report not created. Invalid Option Selected.";
                break;
            case '1':
                message = "Report not created. All required fields were not submitted.";
                break;
            case '0':
                message = "Report not created. No data matched your query.";
                break;
            default:
                break;
        }
        feedback(message, 'Error: '+reportTitle+' Report', 2);
    }
    else if (reqURI.length > 1 && reqURI[1].match(/rpt=([A-Za-z])&s=\d+/) !== false){
        var successInfo = reqURI[1].split('&');
        var report = successInfo[0].replace('rpt=','');
        if (report == 'Tax_Receipts'){
            sendTaxReceipts();
            feedback('Success: Now Sending Emails', 'Tax Receipts Report', 1);
        }
    }
}

jQuery(document).ready(function($){
    
    $('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });
    
    $('#frmFoundation').submit(function(){
            return checkDate($(':input[name=foundationStart]').val(), $(':input[name=foundationEnd]').val()); 
    });
    
    checkErrors();
    
    $('#moreTax').click(function(){
        if ($(this).html().match(/More/) != null){
            $('#dvMoreTax').slideDown();
            $(this).html('&#9650;Less');
        }
        else{
            $('#dvMoreTax').slideUp();
            $(this).html('&#9660;More');
        }
    });
    $('#btnEmergency').click(function(){
       window.open('/src/LondonFencing/reports/assets/emergencyContacts.php'); 
    });
});