<?php

array_push($meta['body_classes'], 'four-column');
require 'header.php';

?>

<section class="colA"><?php print $page->get_col_content('fourColA'); ?></section>

<section class="main">  
	<div class="colC"><?php print $page->get_col_content('fourColC'); ?></div>
	<div class="colD"><?php print $page->get_col_content('fourColD'); ?></div>
</section>
<!--<section id="colF"><?php /*print $page->get_col_content('fourColF');*/ ?></section>-->
<?php

require 'footer.php';
?> 