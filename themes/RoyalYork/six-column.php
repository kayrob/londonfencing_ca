<?php

array_push($meta['body_classes'], 'six-column');
require 'header.php';

?>

<section id="colA"><?php print $page->get_col_content('sixColA'); ?></section>

<section id="colB"><?php print $page->get_col_content('sixColB'); ?></section>

<section class="main">
	<div id="colC"><?php print $page->get_col_content('sixColC'); ?></div>
	<div id="colD"><?php print $page->get_col_content('sixColD'); ?></div>
	<div id="colE"><?php print $page->get_col_content('sixColE'); ?></div>
</section>


<section id="colF"><?php print $page->get_col_content('sixColF'); ?></section>

<?php

require 'footer.php';
?> 