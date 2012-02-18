<?php

array_push($meta['body_classes'], 'one-column');
require 'header.php';

?>

<?php print $page->get_col_content('oneColA'); ?>

<?php

require 'footer.php';
?> 