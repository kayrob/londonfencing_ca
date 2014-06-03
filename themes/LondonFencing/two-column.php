<?php

array_push($meta['body_classes'], 'two-column');
require 'header.php';

?>

<section class="colA"><?php print $page->get_col_content('twoColA'); ?></section>

<section class="main">
	<div class="colB"><div class="wrapper"><?php print $page->get_col_content('twoColB'); ?></div></div>
	<div class="colC"><div class="wrapper"><?php print $page->get_col_content('twoColC'); ?></div></div>
</section>

<?php

require 'footer.php';
?> 