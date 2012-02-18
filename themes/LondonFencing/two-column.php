<?php

array_push($meta['body_classes'], 'two-column');
require 'header.php';

?>

<section id="colA"><?php print $page->get_col_content('twoColA'); ?></section>

<section class="main">
	<div id="colB"><?php print $page->get_col_content('twoColB'); ?></div>
	<div id="colC"><?php print $page->get_col_content('twoColC'); ?></div>
</section>

<?php

require 'footer.php';
?> 