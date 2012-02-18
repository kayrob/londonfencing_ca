$(document).ready(function() {

	$("div#bannerSlider").cycle({ 
	    fx: 'fade',
	    timeout: 3000,
	    speed: 1000,
	    before: beforeCycle,
	    //after: afterBCycle,
	    pause: 1,
	    prev: "#prev",
	    next: "#next"
	});
	
	function beforeCycle() {

		var t1 = $(this).attr('data-title');
		var t2 = $(this).attr('data-bodytext');

		var overlay = $(this).attr('data-overlay');
		
		$('#bannerContent').slideUp(400, function() {
			$('#bannerContent h4').html(t1);
			$('#bannerContent h1').html(t2);
			$('#bannerContent').slideDown();
		});
		/*$('#bannerImg').fadeIn(400, function(){
		    $('img', this).attr('src','uploads/banners/'+overlay);
		});*/
		$('#bannerImg img').attr('src','uploads/banners/'+overlay)
		
	}
	function afterBCycle(){
	   $('ul a','#bannerNav').each(function(){
            $(this).removeClass();
       });
       $('ul a:eq('+$(this).index()+')','#bannerNav').addClass('current');
	}
});