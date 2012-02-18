<section id="contactFooter">
	<div class="blankMainHeader">
		<h2>Request A Consult</h2>
	</div>
	
	<div id="colC">
		Etiam porta sem malesuada magna mollis euismod. Maecenas sed diam eget risus varius blandit sit amet non magna. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Duis mollis, est non commodo luctus.
    </div>
<form action="/request-a-consultation" method="post">
	    <input type="text" name="RQvalALPHName" id="RQvalALPHName" required placeholder="Name" />
		<label for="RQvalALPHName"><h6>Jane Smith</h6></label>
        <input type="text" name="RQvalPHONPhone_Number" required id="RQvalPHONPhone_Number" placeholder="Phone Number" />
		<label for="RQvalPHONPhone_Number"><h6>416-555-1212</h6></label>
	    <input type="email" name="RQvalMAILEmail_Address" required id="RQvalMAILEmail_Address" placeholder="Email Address"/>
        <label for="RQvalMAILEmail_Address"><h6>user@domain.com</h6></label>
        <input type="text" name="RQvalMAILServices" required id="RQvalMAILServices" placeholder="Service"/>
        <label for="RQvalALPHServices"><h6>Invisalign</h6></label>
        <textarea name="RQvalALPHMessage" id="RQvalALPHMessage" cols="30" rows="5" placeholder="Message" required></textarea>
        <input type="submit" value="Submit" name="sub-req-consult" class="btnStyle" />
        <input type="hidden" name="nonce" value="<?php echo Quipp()->config('security.nonce');?>" />
</form>
</section>