<!doctype html>
<html lang="en" class="no-js">
<head>
  <meta charset="utf-8">
  <!--[if IE]><![endif]-->

  <title><?php print $meta['title'] . $meta['title_append']; ?></title>
  <meta name="robots" content="noindex,nofollow">
  <link rel="shortcut icon" type="image/png" href="/admin/favicon.png">
  <link rel="apple-touch-icon" href="/admin/apple-touch-icon.png">
  <link rel="stylesheet" href="/js/uniform_js/css/uniform.aristo2.css">
  <link rel="stylesheet" href="/min/?f=css/style.css,css/admin.css,js/jquery-ui/jquery-ui-1.8.18.custom.css">
 
   <?php 
	//print out any scripts that are needed for the page calling in this header file, 
	//this is set in that particular file using array_push($quipp->js['header'],"/path/to/script.js", "/path/to/another/script.js");
	
	if(isset($quipp->css)) {
		if(is_array($quipp->css)) {
			foreach($quipp->css as $val) {
				if($val != '') {
					print '<link rel="stylesheet" href="' . $val . '">'; 
				}
			}
		}
	}
	?>
  <script src="/js/modernizr.custom.js"></script>
  <?php 
	//print out any scripts that are needed for the page calling in this header file, 
	//this is set in that particular file using array_push($quipp->js['header'],"/path/to/script.js", "/path/to/another/script.js");
	
	if(isset($quipp->js['header'])) {
		if(is_array($quipp->js['header'])) {
			foreach($quipp->js['header'] as $val) {
				if($val != '') {
					print '<script type="text/javascript" src="' . $val . '"></script>'; 
				}
			}
		}
	}
  ?>
</head>
<body class="light <?php print Page::body_class($meta['body_classes']); ?>" id="<?php if (!empty($meta['body_id'])) print $meta['body_id']; ?>">
