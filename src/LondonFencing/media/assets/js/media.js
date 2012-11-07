$(document).ready(function(){
    
    $('.primeImg img').each(function(){
        //full size height = 310px;
        var imgSrc = "/src/LondonFencing/StaticPage/resize.php?jpeg="+encodeURIComponent('home/'+$(this).data('src'))+"&jpgw="+$(this).parent().width()+"&jpgh="+$(this).parent().height();
        $(this).attr('src',imgSrc);
        $(this).attr('width',$(this).parent().width());
        $(this).attr('height',$(this).parent().height());
    });
    
    $('.homeThumb').each(function(){
        //full size width = 960;
        //full size thumb = 100 x 100
        //scale based on width b/c thumbnails are square
        var newWH = Math.round(($('.banner').width()/960) * 100);
        var imgSrc = "/src/LondonFencing/StaticPage/resize.php?jpeg="+encodeURIComponent('med/'+$(this).data('src'))+"&jpgw="+newWH+"&jpgh="+newWH;
        $(this).attr('src',imgSrc);
        $(this).attr('width',newWH);
        $(this).attr('height',newWH);
    });
    
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
    
    $('.homeThumb').bind('click', function(){
        $('.primeImg').cycle(($(this).parent().index()-1));
    });
    
});