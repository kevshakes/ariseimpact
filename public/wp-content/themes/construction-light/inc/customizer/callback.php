<?php
/*
 * About Us Progress Bar Active Callback Function.
*/
function construction_light_active_progressbar(){
  $about_progressbar = get_theme_mod('construction_light_aboutus_progressbar',true);
  if ($about_progressbar == true) {
    return true;
  }else {
    return false;
  }
}

/*
 * About Us Button Active Callback Function.
*/
function construction_light_active_about_button(){
  $about_button = get_theme_mod('construction_light_aboutus_content', 'excerpt');
  if ($about_button == 'excerpt') {
    return true;
  }else {
    return false;
  }
}