<?php
    require_once dirname(__DIR__) . '/inc/bootstrap.php';
    Quipp()->secure()->stopTokenRefresh();
    require_once dirname(__DIR__) . '/vendors/securimage/securimage.php';

    $img = new Securimage();

//    $img->captcha_type   = Securimage::SI_CAPTCHA_MATHEMATIC; // show a simple math problem instead of text
//    $img->case_sensitive = true;                              // true to use case sensitve codes - not recommended
//    $img->image_height   = 90;                                // width in pixels of the image
//    $img->image_width    = $img->image_height * M_E;          // a good formula for image size
//    $img->perturbation   = .75;                               // 1.0 = high distortion, higher numbers = more distortion
//    $img->image_bg_color = new Securimage_Color("#0099CC");   // image background color
//    $img->text_color     = new Securimage_Color("#EAEAEA");   // captcha text color
//    $img->num_lines      = 8;                                 // how many lines to draw over the image
//    $img->line_color     = new Securimage_Color("#0000CC");   // color of lines over the image

    $img->show();