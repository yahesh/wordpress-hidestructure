<?php
  /*
    Plugin Name: HideStructure
    Plugin URI: https://yahe.sh/
    Description: Hides the WordPress URL structure from generated pages.
    Version: 0.2c1
    Author: Yahe
    Author URI: https://yahe.sh/

    this code is based on http://w-shadow.com/blog/2010/05/20/how-to-filter-the-whole-page-in-wordpress/
  */

  /* THESE SETTINGS ARE FREE TO EDIT */

  // [ROOT_CONTEXT] MUST begin with a slash
  // but MUST NOT end with a slash or it can be empty
  // if no special root context is in use! In addition
  // you need some URL rewriting to make this work:
  //
  // # hide WordPress URL structure
  // RewriteCond %{REQUEST_FILENAME} !-f
  // RewriteRule ^root\/(.*)$ [ROOT_CONTEXT]/$1 [L]
  //
  // # hide WordPress "wp-content" URL structure
  // RewriteCond %{REQUEST_FILENAME} !-f
  // RewriteRule ^content\/(.*)$ [ROOT_CONTEXT]/wp-content/$1 [L]
  //
  // # hide WordPress "wp-includes" URL structure
  // RewriteCond %{REQUEST_FILENAME} !-f
  // RewriteRule ^includes\/(.*)$ [ROOT_CONTEXT]/wp-includes/$1 [L]

  $hidestructure_rootcontext = "[ROOT_CONTEXT]";

  /* STOP EDITING HERE IF YOU DO NOT KNOW WHAT YOU ARE DOING */

  $hidestructure_after  = "after";
  $hidestructure_before = "before";

  $hidestructure_replacers_a = array(array($hidestructure_before => $hidestructure_rootcontext."/wp-content/",
                                           $hidestructure_after  => "/content/"),
                                     array($hidestructure_before => $hidestructure_rootcontext."/wp-includes/",
                                           $hidestructure_after  => "/includes/"));

  if (strlen($hidestructure_rootcontext) > 0) {
    $hidestructure_replacers_b = array(array($hidestructure_before => $hidestructure_rootcontext."/",
                                             $hidestructure_after  => "/root/"),
                                       array($hidestructure_before => $hidestructure_rootcontext."/wp-admin/",
                                             $hidestructure_after  => $hidestructure_rootcontext."/wp-admin/"));
  }

  /* STOP EDITING HERE */

  function hidestructure_startbuffering(){
    // do not filter admin pages
    if (!is_admin()){
      // we start buffering here but do not
      // stop it ourself - this is done
      // automatically in "wp_ob_end_flush_all()" in
      // file "/wp-includes/functions.php"
      ob_start("hidestructure_filterpage");
    }
  }

  function hidestructure_replaceslashesA($html) {
    return str_ireplace("/", "%2F", $html);
  }

  function hidestructure_replaceslashesB($html) {
    return str_ireplace("/", "\/", $html);
  }

  function hidestructure_filterpage($html) {
    global $hidestructure_after;
    global $hidestructure_before;
    global $hidestructure_replacers_a;
    global $hidestructure_replacers_b;

    foreach ($hidestructure_replacers_a as $item) {
      $html = str_ireplace($item[$hidestructure_before], $item[$hidestructure_after], $html);
      $html = str_ireplace(hidestructure_replaceslashesA($item[$hidestructure_before]), hidestructure_replaceslashesA($item[$hidestructure_after]), $html);
      $html = str_ireplace(hidestructure_replaceslashesB($item[$hidestructure_before]), hidestructure_replaceslashesB($item[$hidestructure_after]), $html);
    }
    foreach ($hidestructure_replacers_b as $item) {
      $html = str_ireplace($item[$hidestructure_before], $item[$hidestructure_after], $html);
      $html = str_ireplace(hidestructure_replaceslashesA($item[$hidestructure_before]), hidestructure_replaceslashesA($item[$hidestructure_after]), $html);
      $html = str_ireplace(hidestructure_replaceslashesB($item[$hidestructure_before]), hidestructure_replaceslashesB($item[$hidestructure_after]), $html);
    }

    return $html;
  }

  add_action("wp", "hidestructure_startbuffering");
?>
