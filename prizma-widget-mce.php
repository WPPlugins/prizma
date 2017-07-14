<?php
defined('ABSPATH') or die('No script kiddies please!');
require_once 'prizma-widget-i18n.php';

class Prizma_Widget_MCE {

  private static $options;

  static public function init() {
    self::$options = get_option('fem-inc-widget-options');
    
    add_filter('mce_buttons', array('Prizma_Widget_MCE', 'registerButtons'));
    add_filter('mce_external_plugins', array('Prizma_Widget_MCE', 'registerMCEPlugin'));
    add_shortcode('prizma-spotlight', array('Prizma_Widget_MCE', 'renderShortCode'));
    add_action('admin_print_footer_scripts', array('Prizma_Widget_MCE', 'addQuickTags'));  
        
    wp_enqueue_script('prizma-spotlight-tinymce', plugins_url('/prizma-spotlight-quicktag.js',__FILE__));
  }
  
  static public function registerButtons($buttons) {
    array_push($buttons, 'wp_prizma_spotlight_add');
    return $buttons;
  }

  static public function registerMCEPlugin($plugins) {
    $plugins['prizmaspotlight'] = plugins_url('/prizma-spotlight-tinymce.js',__FILE__);
    return $plugins;
  }
  
  static public function renderShortCode($args){
    if(!$args["url"]){
      return "";
    }
    
    $data = array(
        "partnerID" => self::$options['partnerID'],
        "cssFiles" => self::$options["cssFiles"],
        "videoURL" => $args["url"],
    );
    if($args["title"]){
      $data["title"] = $args["title"];
    }

    return Prizma_Widget::render($data);    
  }
  
  static public function addQuickTags() {
      if (wp_script_is('quicktags')){
        ?>
            <script type="text/javascript">
              initPrizmaSpotlightQuicktag();
            </script>
        <?php
      }
  }
  
}

Prizma_Widget_MCE::init();
?>
