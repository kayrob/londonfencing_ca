/**
 * @note DO NOT PLACE CODE IN THIS FUNCTION
 * @todo Place this in its own file
 */
jQuery(document).ready(function($) {
    function dropdownMouseover() { $(this).children('ul').slideDown(); }
    function dropdownMouseout() { $(this).children('ul').slideUp(); }
    var config = {
        over: dropdownMouseover,
        timeout: 500,
        out: dropdownMouseout
    }
    $('.dropdown').hoverIntent(config);
    
    $('.fbMap').fancybox();
    if ($('.primeImg').length == 1){
        $('.primeImg').cycle({
        fx: 'fade',
        next: '.next',
        prev: '.prev',
        before: afterCycle
    });
    }
    var colBHeight = $('#colB').height();
    var colCElementsHeight = 0;
    $('#colC div').each(function() {
    	colCElementsHeight += $(this).height();	
    });
    
    if(colCElementsHeight > colBHeight) {
    	$('#colC div').addClass('tooLong');
    }
    function afterCycle(){
        $('.banner img').each(function(imgIndex){
            if (imgIndex > 0 && imgIndex < 7){
                $(this).removeClass().addClass('homeThumb');
            }
        });
        $('.banner img:eq('+($(this).index() +1)+')').removeClass('homeThumb').addClass('homeThumbB');
    }
    
});