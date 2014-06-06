$(document).ready(function(){
    
    var docSize = $(document).width();
    var isResizing = false;
    
    if ($(".primeImg").length == 1){
        var getPrimeImgSrc = function(newImg){
            var imgSrc = "/src/LondonFencing/StaticPage/resize.php?jpeg="+encodeURIComponent('home/'+newImg.data('src'))+"&jpgw="+$(".primeImg").width()+"&jpgh="+$(".primeImg").height();
            return imgSrc;
        };
        var setPrimeImgSize = function(){
            $('.primeImg img').each(function(){
                //full size height = 310px;
                var imgSrc = getPrimeImgSrc($(this));
                $(this).attr('src',imgSrc);
                $(this).attr('width',$(this).parent().width());
                $(this).attr('height',$(this).parent().height());
            });
        }
        var setThumbContainerSize = function(){
            var bannerWidth = ($(".banner .resize").width() + 10) * $(".banner .resize").length
            $(".banner").css({"width" : bannerWidth + "px"});
        };

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
        $(".banner-scroller").on("mouseenter", function(){
            $(this).css({"overflow-x": "scroll"}); 
        });
        $(".banner-scroller").on("mouseleave", function(){
            $(this).css({"overflow-x": "hidden"}); 
        });

        var getCurrentImageIndex = function(){
            var currentIndex = 0;
            $(".banner img").each(function(){
            if ($(this).hasClass("homeThumbB")){
                currentIndex = $(this).data("index");
            }
            });
            return currentIndex;
        };
        var getNextImageIndex = function(currentIndex, direction){
            var nextIndex = 0;
            var maxIndex = $(".banner .resize").length - 1;
            if (currentIndex == 0 && direction == -1){
                nextIndex = maxIndex;
            }
            else if ((currentIndex + direction) > maxIndex){
                nextIndex = 0;
            }
            else if ((currentIndex + direction) < 0){
                nextIndex = maxIndex;
            }
            else{
                nextIndex = currentIndex + direction;
            }
            return nextIndex;
        };

        var scrollToThumbnail = function(nextIndex){
            var containerWidth = $(".banner-scroller").width();
            var scrollWidth = $(".banner li").width() + 10; //including margin
            var currentPos = $(".banner-scroller").scrollLeft();
            var nextImgPos = (scrollWidth * nextIndex) + scrollWidth; //because we need the full image in view
            var maxIndex = $(".banner .resize").length - 1;

            var isScroll = false;
            var scrollToPos = 0;
            if (nextImgPos < containerWidth || nextIndex == 0){
                isScroll = true;
            }
            else if (currentPos == 0 && nextIndex == maxIndex){
                var liInView = containerWidth/scrollWidth;
                var firstLI = maxIndex - liInView;
                nextImgPos = (firstLI * scrollWidth) + scrollWidth;
                isScroll = true;
                scrollToPos = nextImgPos;
            }
            else if (nextImgPos == currentPos){
                isScroll = true;
                scrollToPos = currentPos - scrollWidth;
            }
            else if (nextImgPos < currentPos){
                isScroll = true;
                scrollToPos = nextImgPos;
            }
            else if (nextImgPos > currentPos){
                var newPos = currentPos + scrollWidth;
                isScroll = true;
                scrollToPos = newPos;
            }
            if (isScroll === true){
                $(".banner-scroller").animate({"scrollLeft": scrollToPos}, "slow", "swing");
            }
        };

        var changeImage = function(currentIndex, nextIndex, scroll){
            var nextSrc = getPrimeImgSrc($(".banner .resize:eq("+nextIndex+") img"));
            var nextTitle = $(".banner .resize:eq("+nextIndex+") img").data("title");

            var primeIndexHide = ($(".primeImg img:eq(0)").is(":visible")) ? 0 : 1;
            var primeIndexShow = (primeIndexHide == 1) ? 0 : 1;
            $(".primeImg img:eq("+primeIndexShow+")").attr("src", nextSrc).attr("title", nextTitle);

            if ($(document).scrollTop() > 0){
                $("html, body").animate({"scrollTop": 0}, "slow", "swing");
            }

            $(".primeImg img:eq("+primeIndexHide+")").fadeOut(500);
            $(".primeImg img:eq("+primeIndexShow+")").fadeIn(500, function(){
                $(".banner .resize:eq("+nextIndex+") img").removeClass("homeThumb").addClass("homeThumbB");
                $(".banner .resize:eq("+currentIndex+") img").removeClass("homeThumbB").addClass("homeThumb");
                if (scroll === true){
                    scrollToThumbnail(nextIndex);
                }
            });
        };
        $("#banner-arrows a").on("click", function(e){
            e.preventDefault();
            var direction = ($(this).hasClass("prev"))? -1 : 1;
            var currentIndex = getCurrentImageIndex();
            var nextIndex = getNextImageIndex(currentIndex, direction);
            changeImage(currentIndex, nextIndex, true);

        });
        $(".banner .resize img").on("click", function(){
            if ($(this).hasClass("homeThumb")){
                var currentIndex = getCurrentImageIndex();
                var thisIndex = $(this).data("index");
                changeImage(currentIndex, thisIndex,false);
            }
        });
        
        setPrimeImgSize();
        setThumbContainerSize();

        var homeResizeEnd = function(){
            if ($(document).width() == docSize && isResizing == true) {
                isResizing = false;
                setPrimeImgSize();
                setThumbContainerSize();

            } else {
                docSize = $(document).width();
                setTimeout(homeResizeEnd, 300);
            }
        }
        $(window).resize(function(){
            if (isResizing === false) {
                isResizing = true;
                setTimeout(homeResizeEnd, 300);
            }
        });
        
        $(window).resize(function(){
            setThumbContainerSize(); 
        });
    }
    
    //inner banner
    var innerBannerGallery = function(){
        var countInner = $(".inner-banner li").length;
        var containerWidth = $(".banner-inner-scroller").width();
        var newWidth = Math.floor(containerWidth / countInner);
        var newWidthPct = containerWidth / countInner;
        if (newWidth >= 100){
            $(".inner-banner").width(containerWidth);
            $("#inner-banner-arrows").hide("fast",function(){
                $(".icon-arrow-right, .icon-arrow-left", this).off("mouseover");
                $(".icon-arrow-right, .icon-arrow-left", this).off("mouseout");
                $(".icon-arrow-right, .icon-arrow-left", this).off("click");
            });
        }
        else{
            //max images is 4 here
            var numSpread = 2;
            newWidth = Math.floor(containerWidth/2);
            if ((containerWidth/4) >= 100){
                newWidth = Math.floor(containerWidth/4);
                numSpread = 4;
            }
            else if ((containerWidth/3) >= 100){
                newWidth = Math.floor(containerWidth/3);
                numSpread = 3;
            }
            newWidthPct = containerWidth/numSpread;
            $(".inner-banner").width(newWidthPct * countInner);
            $("#inner-banner-arrows").show("fast",function(){
                $(".icon-arrow-right", this).on("mouseover", function(){
                    $(".banner-inner-scroller").animate({"scrollLeft": (newWidthPct * countInner)}, 6000, "swing");
                }).on("mouseout", function(){
                    $(".banner-inner-scroller").stop();
                }).on("click", function(){
                    $(".banner-inner-scroller").stop().animate({"scrollLeft": (newWidthPct * countInner)}, "fast", "swing");
                });
                $(".icon-arrow-left", this).on("mouseover", function(){
                    $(".banner-inner-scroller").animate({"scrollLeft": 0}, 6000, "swing");
                }).on("mouseout", function(){
                    $(".banner-inner-scroller").stop();
                }).on("click", function(){
                    $(".banner-inner-scroller").stop().animate({"scrollLeft": 0}, "fast", "swing");
                });
            });
        }
        $(".innerThumb").each(function(){
            var newSrc = "/src/LondonFencing/StaticPage/resize.php?jpeg=" + encodeURIComponent($(this).data("src")) + "&jpgw="+ newWidth +"&jpgh=100";
            $(this).css({"width" : newWidthPct + "px"});
            $(this).attr("src", newSrc);
        });
    }
    if ($(".inner-banner").length == 1){
        innerBannerGallery();
        
        var resizeEnd = function(){
            if ($(document).width() == docSize && isResizing == true) {
                isResizing = false;
                innerBannerGallery();
            } else {
                docSize = $(document).width();
                setTimeout(resizeEnd, 300);
            }
        }
        $(window).resize(function(){
            if (isResizing === false) {
                isResizing = true;
                setTimeout(resizeEnd, 300);
            }
        });
        
        $('.fbGallery').fancybox();
    }

});