<?php

array_push($meta['body_classes'], 'four-column');
require 'header.php';

?>

<section id="colA"><?php print $page->get_col_content('fourColA'); ?></section>

<section class="main">  
	<div id="colC"><?php print $page->get_col_content('fourColC'); ?></div>
	<div id="colD"><?php print $page->get_col_content('fourColD'); ?></div>
</section>
<section id="colF"><?php print $page->get_col_content('fourColF'); ?></section>
<?php

require 'footer.php';
?> 