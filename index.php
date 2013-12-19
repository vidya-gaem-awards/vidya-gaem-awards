<?php

  //ob_start('ob_gzhandler');
  
  require_once("includes/config.php");

  define("EVERYONE", "*");
  define("LOGIN", "logged-in");

  // URL rewriter
  // Courtesy of http://stackoverflow.com/questions/893218/rewrite-for-all-urls
  $_SERVER['REQUEST_URI_PATH'] = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

  $SEGMENTS = explode('/', trim($_SERVER['REQUEST_URI_PATH'], '/'));
  $SEGMENTS = array_map("strtolower", $SEGMENTS);

  for ($i = 0; $i <= 9; $i++) {
    if (!isset($SEGMENTS[$i])) {
      $SEGMENTS[$i] = null;
    }
  }


  $PAGE = $SEGMENTS[0];

  // Change the default page in includes/config.php
  if (strlen($PAGE) == 0) {
    $PAGE = $DEFAULT_PAGE;
  }

  // Special handling for logout page
  if ($PAGE == "logout") {
    session_start();
    $_SESSION = array();
    session_destroy();
    
    $return = rtrim(implode("/", array_slice($SEGMENTS, 1)), "/");
    setcookie("token", "0", 1, "/", $DOMAIN);
    header("Location: /$return");
    exit;
  }

  // Special handling for ad landing page
  if ($PAGE == "promotions") {
    header("Location: {$AD_LANDING_PAGE}");
    exit;
  }

  $ACCESS = array(
    // These ones should never have to change
    "404" => EVERYONE,
    "about" => EVERYONE,
    "home" => EVERYONE,
    "login" => EVERYONE,
    "privacy" => EVERYONE,
    "promotions" => EVERYONE,
    "sitemap" => EVERYONE,
    
    // Volatile pages
    "ajax-category-feedback" => EVERYONE,
    "ajax-nominations" => "nominations-edit",
    "ajax-videogame" => "add-video-game",
    "applications" => "applications-view",
    "categories" => EVERYONE,
    //"credits" => EVERYONE, // Change to EVERYONE
    //"feedback" => "feedback-view",  // Change to EVERYONE
    //"launcher" => EVERYONE,
    "news" => EVERYONE,
    "nominations" => "nominations-view",
    "nomination-submit" => EVERYONE,
    "people" => "profile-view",
    "referrers" => "referrers-view",
    //"stream" => EVERYONE,
    //"test" => EVERYONE,
    //"thanks" => EVERYONE,
    "user-search" => "add-user",
    "video-games" => EVERYONE,
    "volunteer-submission" => LOGIN,
    //"videos" => EVERYONE,
    //"voting" => EVERYONE,
    //"voting-code" => EVERYONE,
    //"voting-submission" => EVERYONE,
    //"votingpu" => "voting-view",
    //"winners" => EVERYONE // Change to EVERYONE
  );

  if (isset($ACCESS["nomination-submit"]) && $ACCOUNT_REQUIRED_TO_NOMINATE) {
    $ACCESS["nomination-submit"] = LOGIN;
  }

  // Pages that won't use the master template
  $noMaster = array(
    "login",
    "stream"
  );

  // Pages so basic they don't need a PHP file.
  $noPHP = array(
    "405" => "405 Method Not Allowed",
    "404" => "404 Not Found",
    "403" => "403 Forbidden",
    "401" => "401 Unauthorized",
    "about" => "About",
    "privacy" => "Privacy Policy",
    "sitemap" => "Sitemap",
    "stream" => "",
    "videos" => "Video Submission",
  );

  $noContainer = array("videos");

  // Pages that should only be accessed via POST requests
  $postOnly = array(
    "ajax-category-feedback",
    "ajax-nominations",
    "ajax-videogame",
    "nomination-submit",
    "volunteer-submission",
    "user-search",
    "voting-submission",
  );

  // Pages have the option of specifying this variable to load a different template
  $CUSTOM_TEMPLATE = false;

  include("includes/php.php");

  // Enforce access control
  if (!isset($ACCESS[$PAGE])) {
    header('HTTP/1.0 404 Not Found');
    $PAGE = "404";
  } else if ($ACCESS[$PAGE] != EVERYONE && !$loggedIn) {
    header('HTTP/1.0 401 Unauthorized');
    $PAGE = "401";
  } else if (!canDo($ACCESS[$PAGE])) {
    header('HTTP/1.0 403 Forbidden');
    $PAGE = "403";  
  }

  // Enforce post-only pages
  if (in_array($PAGE, $postOnly) && $_SERVER['REQUEST_METHOD'] != "POST") {
    header('HTTP/1.0 405 Method Not Allowed');
    $PAGE = "405";
  }

  // Run the page-specific code
  if (!isset($noPHP[$PAGE])) {
    require("controllers/$PAGE.php");
  } else {
    $tpl->set('title', $noPHP[$PAGE]);
  }

  // Post-only pages have no view at all
  if (!in_array($PAGE, $postOnly)) {

    // Special variable if we don't need the container
    $tpl->set('noContainer', in_array($PAGE, $noContainer));

    // Render the required templates
    $template = $CUSTOM_TEMPLATE ? $PAGE . "-" . $CUSTOM_TEMPLATE : $PAGE;
    if (!in_array($PAGE, $noMaster)) {
      $tpl->set('content', $tpl->fetch("views/$template.tpl"));
      echo $tpl->fetch("views/master.tpl" );
    } else {
      echo $tpl->fetch("views/$template.tpl");
    }
    
  }

?>