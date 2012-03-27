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
});