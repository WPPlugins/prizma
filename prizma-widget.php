<?php

/**
 * Plugin Name: Prizma for WordPress
 * Plugin URI: http://prizma.tv
 * Description: Maximize, measure, and monetize video engagement on your site. Grow your business by distributing premium Prizma Syndication Network content. 
 * Version: 2.1.1
 * Author: FEM, Inc.
 * Author URI: http://prizma.tv
 * License: MIT
 */
defined('ABSPATH') or die('No script kiddies please!');

require_once 'prizma-widget-sidebar.php';
require_once 'prizma-widget-settings.php';
require_once 'prizma-widget-meta-box.php';
require_once 'prizma-widget-mce.php';

class Prizma_Widget {

  private static $options;
  private static $startOBLevel;

  static public function init() {
    ob_start();
    self::$startOBLevel = ob_get_level();
    self::$options = get_option('fem-inc-widget-options');
    
    self::addHooks();
  }

  static private function addHooks() {
    add_action('widgets_init', array('Prizma_Widget', 'registerWidget'));
    add_filter('the_content', array('Prizma_Widget', 'displayBelowPost'));
    add_action('admin_menu', array('Prizma_Widget', 'displaySettingsMenu'));
    add_action('add_meta_boxes', array('Prizma_Widget', 'displayMetaBox'));
    add_action('admin_init', array('Prizma_Widget_Settings', 'registerSettings'));
    add_action('save_post', array('Prizma_Widget_Meta_Box', 'save'));
    add_action('shutdown', array('Prizma_Widget', 'onShutdown'), 0);
    add_filter('plugin_action_links_' . plugin_basename(__FILE__), array('Prizma_Widget', 'displaySettingsLinkOnPluginPage'));
    
    if("wholePage" === self::$options["transformYT"]){
      $outputFilter = 'final_output';
    }
    else{
      $outputFilter = 'the_content';
    }
    
    add_filter($outputFilter, array('Prizma_Widget', 'exchangeYTPlayer'), 100);
  }
  
  static public function onShutdown() {
    $final = '';
    $levels = ob_get_level();

    while (ob_get_level() > self::$startOBLevel){
      ob_end_flush();
    }
    
    $final = ob_get_clean();

    // Apply any filters to the final output
    echo apply_filters('final_output', $final);
  }

  static public function registerWidget() {
    register_widget('Prizma_Widget_Sidebar');
  }

  static public function render($data = array()) {
    wp_enqueue_script('prizma-widget', "http://cdn.prizma.tv/widget/prizma-widget.js");
    wp_enqueue_style('prizma-widget-css', plugins_url('prizma-widget.css', __FILE__));

    $container_id = "fem-widget-container-" . uniqid("fem");
    
    if ("" !== $data['cssFiles']) {
      $data['cssFiles'] = preg_replace("#\\\\/#", '/', json_encode(explode(' ', $data['cssFiles'])));
    }
    if(!$data['videoURL']){
      unset($data['width']);  // it's not possible to set width for any mode beside spotlight (videoURL parameter)
    }

    $data['layout'] = Prizma_Widget_Settings::normalizeLayout($data['layout']);
    if (isset(self::$options["gaTrackingID"]) && "" !== self::$options["gaTrackingID"]){
      $data['gaTrackingID'] = self::$options["gaTrackingID"];
    }
    
    $params = "";
    foreach ($data as $key => $val) {
      if ('cssFiles' === $key) {
        if($val){
          $params .= "{$key}: $val,\n";
        }
        continue;
      }

      $params .= "{$key}: '$val',\n";
    }


    $ret = "
      <div class='prizma-widget-container' id='{$container_id}'></div>
      <script data-prizma-wp='true'>
      // Prizma Widget - WP Plugin
      var prizmaOptions = prizmaOptions || [];
      document.addEventListener('DOMContentLoaded', function(){

        prizmaOptions.push({
          {$params}
          id: (function(){
            if(!'{$data['containerID']}' || !document.getElementById('{$data['containerID']}')){
              return '{$container_id}';
            }
            return '{$data['containerID']}';
          })(),".
          "integration: 'WordPressPlugin'".
        "});
        if('function' === typeof(window.prizmaAsyncInit)) {
          window.prizmaAsyncInit();
        }      
      });      
    </script>";

    return $ret;
  }

  private static function getPropValue($name) {
    $meta = get_post_meta(get_the_ID(), "prizma-widget-meta-" . $name, true);
    return $meta === "" ? self::$options[$name] : $meta;
  }

  static public function displayBelowPost($content) {
    if ('page' == get_post_type(get_the_ID())) {
      $isEnabledProp = 'displayPages';
    } else {
      $isEnabledProp = 'displayPosts';
    }
    
    // $partnerID is mandatory
    $partnerID = self::$options["partnerID"];
    
    if ("off" !== self::getPropValue($isEnabledProp) && $partnerID) {
      $data = array(
          "partnerID" => $partnerID,
          "cssFiles" => self::$options["cssFiles"],
          "layout" => self::getPropValue("layout"),
          "headerText" => self::getPropValue("headerText"),
          "title" => get_the_title(get_the_ID()),
          "containerID" => self::$options["containerID"],
      );
      
      $content .= self::render($data);
    }

    return $content;
  }
  
  static public function exchangeYTPlayer($content) {
    // $partnerID is mandatory
    $partnerID = self::$options["partnerID"];
    $transformYT = self::$options["transformYT"];
    
    if ("off" === $transformYT || !$partnerID) {
      return $content;
    }
        
    return preg_replace_callback("#<iframe\s[^>]+>.*?</iframe>#is", array('Prizma_Widget', 'replaceYTCode'), $content);
  }

  static private function replaceYTCode($iframe) {
    preg_match('#youtube.com/embed/([^"\']+)#', $iframe[0], $ytId);
    
    if(!$ytId[1]){
      return $iframe[0];
    }

    preg_match("#width=[\"']?([0-9a-zA-Z%]+)#", $iframe[0], $width);

    $data = array(
      "partnerID" => self::$options["partnerID"],
      "videoURL" => "https://www.youtube.com/watch?v=" . $ytId[1],
      "cssFiles" => self::$options["cssFiles"],
//      "width" => $width[1],
    );

    return Prizma_Widget::render($data);
  }
  
  static public function displaySettingsMenu() {
    add_options_page('Prizma for WordPress Settings', Text::get('settingsMenuLink'), 'manage_options', 'prizma-widget-settings', array("Prizma_Widget_Settings", "render"));
  }

  static public function displayMetaBox() {
    add_meta_box("prizma-post-meta-box", "Prizma options", array("Prizma_Widget_Meta_Box", "render"), "post", "side", "high", null);
    add_meta_box("prizma-page-meta-box", "Prizma options", array("Prizma_Widget_Meta_Box", "render"), "page", "side", "high", null);
  }

  function displaySettingsLinkOnPluginPage($links) {
    $links[] = '<a href="' . esc_url(get_admin_url(null, 'options-general.php?page=prizma-widget-settings')) . '">Settings</a>';
    return $links;
  }

}

Prizma_Widget::init();
?>
