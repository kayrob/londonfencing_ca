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
        var imgLength = $('.banner img').length;
        var theImage = $(this).index();
        $('.banner img').each(function(imgIndex){
            if (imgIndex > 0 && imgIndex < ($('.banner li.resize').length)){
                $(this).removeClass().addClass('homeThumb');
            }
        });
        var pagerLength = (imgLength > 6) ? 6 : imgLength;
        var visible = new Array();
        for (var i = theImage; i < imgLength; i++){
            visible.push(i);
            if (visible.length == pagerLength){
                break;
            }
        }
        if (visible.length < 6){
            for (var m = 0; m < (imgLength - visible.length); m++){
                visible.push(m);
                if (visible.length == pagerLength){
                    break;
                }
            }
        }
        $(".banner li.resize").hide();
        $(".banner li.resize img").removeClass("homeThumb").removeClass("homeThumbB").unbind("click");
        for (var v = 0; v < visible.length; v++){
            var theLI = $(".banner li.resize:eq("+visible[v]+")");
            var thumbClass = (visible[v] == theImage) ? "homeThumbB" : "homeThumb";
            $("img", theLI).addClass(thumbClass).bind("click", function(){
                $('.primeImg').cycle($(this).data('index'));
            });
            theLI.show();
        }
   
    }
    var documentSetHeight = function(){
        var windowH = $(window).height();
        var docH = $("header").height() + $("footer").height() + $(".main").height();
        if ($(".colA").length == 1){
            docH += $(".colA").height();
        }
        var marginSlack = ($(".error").length == 1) ? 103 : 13;
        if (docH < windowH){
            var diff = (windowH - docH - marginSlack) + $(".main").height();
            $(".main").css({height: diff + "px"});
        }
    }
    var jpm = $.jPanelMenu({
        menu: 'nav',
        trigger: '#momenu'
    });
    var mobileMenu = function(){
        ($("#momenu").is(":visible")) ? jpm.on() : jpm.off();
    }
    mobileMenu();
    documentSetHeight();
    
    $(window).on("resize", function(){
        mobileMenu();
        documentSetHeight();
    });
    
    
});