<?php global $nav; ?>

<h1>Primary</h1>
<?php print $nav->build_nav($nav->get_nav_items_under_bucket('primary')); ?>

<h1>Footer</h1>
<?php print $nav->build_nav($nav->get_nav_items_under_bucket('footer')); ?>