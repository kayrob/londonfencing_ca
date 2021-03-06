
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
  <script type="text/javascript" src="/js/uniform_js/jquery.uniform.min.js"></script>
  <script>!window.jQuery && document.write(unescape('%3Cscript src="/js/jquery-1.6.4.min.js"%3E%3C/script%3E'))</script>
 
  <script src="/min/?f=js/plugins.js,js/script.js"></script> 

<?php 
//print out any scripts that are needed for the page calling in this header file, 
//this is set in that particular file using array_push($quipp->js['header'],"/path/to/script.js", "/path/to/another/script.js");

if(isset($quipp->js['footer'])) {
	if(is_array($quipp->js['footer'])) {
		foreach($quipp->js['footer'] as $val) {
			if ($val != '') {
				print "<script type=\"text/javascript\" src=\"$val\"></script>\n"; 
			}
		}
	}
}
?>

<script type="text/javascript">

	$(function(){ 
		
	
		<?php if(isset($quipp->js['footer'])) { print $quipp->js['onload']; } ?>
		$("#metaFields select.uniform, #metaFields input:checkbox.uniform, #metaFields input:radio.uniform, #metaFields input:file.uniform, .uniform").uniform();
		
		$("#saveBtn").click(function(){

			//$("#boxContentForm").submit();
			//console.log('submitting form data');

			$("#loader").show();

			var cID = $('#contentID').val();
			var pID = $('#pageID').val();
			var rID = $('#regionID').val();
			var boxStyle = $('#boxStyle').val();
			
			var boxTitle = $('#boxTitle').val();
			var boxContent = $('#boxBodyContent').val();
			var hideTitle = $('#hideTitle').attr("checked") ? $('#hideTitle').val() : 0;
			
			var nonce = $('#nonce').val();
			
			
			//var boxContent = CKEDITOR.instances.boxBodyContent.getData();

			var pb = $('#pb').val();

			//console.log(boxContent);
			$.ajax({
						url: "<?php print $_SERVER['PHP_SELF']; ?>",
						type: 'POST',
						data: "&nonce=" + nonce + "&pb=" + pb + "&contentID=" + cID + "&pageID=" + pID + "&regionID=" + rID + "&boxStyle=" + boxStyle + "&boxTitle=" + escape(boxTitle) + "&hideTitle=" + escape(hideTitle) + "&boxBodyContent=" + escape(boxContent),
						context: document.body,
						success: function(result){
							//console.log(result);
							//console.log("ajax returned");
							$("#loader").hide();
							parent.reload(cID, boxTitle, nonce);
     					}
			});
		});
	});
</script>
</body>
</html>