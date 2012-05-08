$(document).ready(function(){
    $('.fbGallery').fancybox();
    $('.mediaPhotos p').each(function(){
            $(this).click(function(){
                    var id = $(this).attr('id').replace('p_','ul_');
                    if ($(this).text() == "More"){
                            $('#'+id,'.mediaPhotos').show();
                            $(this).text('Less');
                    }
                    else if ($(this).text() == "Less"){
                            $('#'+id,'.mediaPhotos').hide();
                            $(this).text('More');
                    }
            });
    });
    
    $('.homeThumb').click(function(){
        $('.primeImg').cycle(($(this).parent().index()-1));
        
    });
    
});