/**
 * @note DO NOT PLACE CODE IN THIS FUNCTION
 * @todo Place this in its own file
 */
jQuery(document).ready(function($) {
    function dropdownMouseover() {$(this).children('ul').slideDown();}
    function dropdownMouseout() {$(this).children('ul').slideUp();}
    var config = {
        over: dropdownMouseover,
        timeout: 500,
        out: dropdownMouseout
    }
    $('.dropdown').hoverIntent(config);
    
    $('.fbMap').fancybox();

    var colBHeight = $('#colB').height();
    var colCElementsHeight = 0;
    $('#colC div').each(function() {
    	colCElementsHeight += $(this).height();	
    });
    
    if(colCElementsHeight > colBHeight) {
    	$('#colC div').addClass('tooLong');
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

    documentSetHeight();
    $("#monav").mmenu().on("opening.mm", function(){
        $("#monav").show();
    }).on("closed.mm", function(){
        $("#monav").hide();
    });
    
    $(window).on("resize", function(){
        documentSetHeight();
    });
    
    
});