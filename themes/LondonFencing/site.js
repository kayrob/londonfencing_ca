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
            if (imgIndex > 0 && imgIndex < ($('.banner li').length - 1)){
                $(this).removeClass().addClass('homeThumb');
            }
        });
        $('.banner img:eq('+($(this).index() +1)+')').removeClass('homeThumb').addClass('homeThumbB');
        $('.banner li:eq('+($(this).index() +1)+')').show();
        //get siblings prior to current li index where class <> 'next' and class <> 'prev'
        if ($('.banner li').length > 8){
            var visible = 1;
            var imgLen = $('.banner li').length - 1;
            var stInd = ($(this).index() + 1) + 1;
            var prvInd = $(this).index();
            for (var s = stInd; s < imgLen; s++){
                if (visible < 6){
                    var cycleInd = (-1 + s);
                    $('.banner img:eq('+s+')').addClass('homeThumb').bind('click', function(){
                        $('.primeImg').cycle($(this).data('index'));
                    });
                    $('.banner li:eq('+s+')').show();
                    visible++;
                }
                else{
                    $('.banner img:eq('+s+')').removeClass('homeThumb').unbind('click');
                    $('.banner li:eq('+s+')').hide();
                }
            }
            for (s = prvInd; s > 0; s--){
                if (visible < 6){
                    cycleInd = (-1 + s);
                    $('.banner img:eq('+s+')').addClass('homeThumb').bind('click', function(){
                        $('.primeImg').cycle($(this).data('index'));
                    });
                    $('.banner li:eq('+s+')').show();
                    visible++;
                }
                else{
                    $('.banner img:eq('+s+')').removeClass('homeThumb').unbind('click');
                    $('.banner li:eq('+s+')').hide();
                }
            }
        }
    }
    
 /*   $('#momenu').click(function(){
        $('nav ul').toggle(300,function(){
            var triangle = ($('#momenu').html() == '▼' )?'▲' : '▼';
            $('#momenu').html(triangle);
        });
    });*/
    
});
function hsNav(){
    var navUL = document.getElementById("container").getElementsByTagName("ul");
    var navStyle = (navUL[0].style.display == 'none')?"block":"none";
    var triangle = (navStyle == "block")?"▲":"▼";
    navUL[0].style.display = navStyle;
    document.getElementById('momenu').innerHTML = triangle;
}