<?php
defined('ABSPATH') or die('No script kiddies please!');

require_once 'prizma-widget-i18n.php';

class Prizma_Widget_Settings {

  private static $options = array();

  // labeling for main article
  public static $availableLayouts = array(
      "MOSAIC" => "Mosaic",
      "FILMSTRIP" => "Filmstrip"
  );
  
  // alternate labeling for sidebar
  public static $availableLayoutsSidebar = array(
    "MOSAIC" => "Text On Thumbnails",
    "FILMSTRIP" => "Text Under Thumbnails"
  );

  public static function render() {
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    // Set class property
    self::$options = get_option('fem-inc-widget-options');
    self::$options['layout'] = self::normalizeLayout(self::$options['layout']);

    self::setDefaultOptions();
    wp_enqueue_style('prizma-widget-admin-css', plugins_url('prizma-widget-admin.css', __FILE__));
    $active_tab = isset($_GET[ 'tab' ]) ? $_GET[ 'tab' ] : 'general';

    ?>
    <div class="wrap">
      <div class="logo-image"></div>
      <h2><?=Text::get('settingsHeader');?></h2>
      <p><?=Text::get('settingsDescription');?></p>

      
      <h2 class="nav-tab-wrapper">
        <a href="?page=prizma-widget-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General</a>
        <a href="?page=prizma-widget-settings&tab=advanced" class="nav-tab <?php echo $active_tab == 'advanced' ? 'nav-tab-active' : ''; ?>">Advanced</a>
      </h2>
      
      <?php 
      if('general' === $active_tab && self::$options['partnerID']){
      ?>
        <table class="dashboard-description">
          <tbody>
            <tr>
              <th scope="row"><?=Text::get('dashboardTitle');?><span class="prizma-widget-plugin-description"><?=Text::get('dashboardDescription');?></span></th>
              <td rowspan="2"><a href="https://dashboard.prizma.tv" class="dashboard-link">My Prizma Dashboard</a></td>
            </tr>
            <tr>
              <th scope="row" class="prizma-dashboard-partner-id"><span class="prizma-widget-plugin-description">Your Partner ID is <?=self::$options['partnerID'];?> (<a id="prizma-edit-partner-id" href="#">edit</a>)</span></th>
            </tr>
          </tbody>
        </table>      
        <script>
          jQuery(document).ready(function(){
            jQuery("#partnerID").parent().parent().hide()
            jQuery("#prizma-edit-partner-id").click(function(){
              jQuery("#partnerID").parent().parent().show();
            });
          })
        </script>
      <?php 
      }
      ?>
      
      <form method="post" action="options.php" class="prizma-widget-settings">
        <?php
        if('general' === $active_tab){
          $sectionName = 'prizma-widget-general-settings';
        }
        else{
          $sectionName = 'prizma-widget-advanced-settings';
        }
        
        // This prints out all hidden setting fields
        settings_fields('prizma-widget-general-settings');
        do_settings_sections($sectionName);

        echo sprintf(Text::get("sidebarInformation"), admin_url('widgets.php'));
        
        submit_button();
        ?>
      </form>
    </div>
    <?php
  }

  static public function normalizeLayout($layout){
    if(!$layout || "THUMBS" === $layout){
      return "MOSAIC";
    }
    else if("SINGLE_ROW" === $layout){
      return "FILMSTRIP";
    }
    return $layout;
  }

  static private function setDefaultOptions() {
    if (!isset(self::$options['layout']) || "" === self::$options['layout']) {
      self::$options['layout'] = "MOSAIC";
    }
    if (!isset(self::$options['transformYT']) || "" === self::$options['transformYT']) {
      self::$options['transformYT'] = "on";
    }
    if (!isset(self::$options['displayPages'])) {
      self::$options['displayPages'] = "off";
    }
    if (!isset(self::$options['displayPosts'])) {
      self::$options['displayPosts'] = "on";
    }
    
    self::$options['layout'] = self::normalizeLayout(self::$options['layout']);
  }

  static private function getFieldTitle($field) {
    $description = Text::get($field . 'Description');
    
    return Text::get($field . 'Title') . "<span class='prizma-widget-plugin-description'>" . $description . "</span>";
  }

  static public function registerSettings() {
    register_setting(
            'prizma-widget-general-settings', // Option group
            'fem-inc-widget-options', // Option name
            array('Prizma_Widget_Settings', 'sanitize') // Sanitize
    );

    add_settings_section(
            'fem_inc_main_section', // ID
            '', // Title
            null, // array('Prizma_Widget_Settings', 'print_section_info'), // Callback
            'prizma-widget-general-settings' // Page
    );

    add_settings_section(
            'fem_inc_main_section', // ID
            '', // Title
            null, // array('Prizma_Widget_Settings', 'print_section_info'), // Callback
            'prizma-widget-advanced-settings' // Page
    );

    add_settings_field(
            'partnerID', // ID
            self::getFieldTitle('partnerID'), // Title 
            array('Prizma_Widget_Settings', 'clbPartnerID'), // Callback
            'prizma-widget-general-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'cssFiles', // ID
            self::getFieldTitle('cssFiles'), // Title 
            array('Prizma_Widget_Settings', 'clbCssFiles'), // Callback
            'prizma-widget-advanced-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'displayPages', // ID
            self::getFieldTitle('displayPages'), // Title 
            array('Prizma_Widget_Settings', 'clbDisplayPages'), // Callback
            'prizma-widget-advanced-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'displayPosts', // ID
            self::getFieldTitle('displayPosts'), // Title 
            array('Prizma_Widget_Settings', 'clbDisplayPosts'), // Callback
            'prizma-widget-advanced-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'headerText', // ID
            self::getFieldTitle('headerText'), // Title 
            array('Prizma_Widget_Settings', 'clbHeaderText'), // Callback
            'prizma-widget-general-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'layout', // ID
            self::getFieldTitle('layout'), // Title 
            array('Prizma_Widget_Settings', 'clbLayout'), // Callback
            'prizma-widget-advanced-settings', // Page
            'fem_inc_main_section' // Section           
    );
    
    add_settings_field(
            'transformYT', // ID
            self::getFieldTitle('transformYT'), // Title 
            array('Prizma_Widget_Settings', 'clbTransformYT'), // Callback
            'prizma-widget-advanced-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'gaTrackingID', // ID
            self::getFieldTitle('gaTrackingID'), // Title 
            array('Prizma_Widget_Settings', 'clbGATrackingID'), // Callback
            'prizma-widget-advanced-settings', // Page
            'fem_inc_main_section' // Section           
    );

    add_settings_field(
            'containerID', // ID
            self::getFieldTitle('containerID'), // Title 
            array('Prizma_Widget_Settings', 'clbContainerID'), // Callback
            'prizma-widget-advanced-settings', // Page
            'fem_inc_main_section' // Section           
    );

  }

  static public function sanitize($input) {
    $new_input = array();
    
    if (isset($input['partnerID'])) {
      $new_input['partnerID'] = sanitize_text_field($input['partnerID']);
    }
    if (isset($input['cssFiles'])) {
      $new_input['cssFiles'] = sanitize_text_field($input['cssFiles']);
    }
    if (isset($input['headerText'])) {
      $new_input['headerText'] = sanitize_text_field($input['headerText']);
    }
    if (isset($input['containerID'])) {
      $new_input['containerID'] = sanitize_text_field($input['containerID']);
    }
    if (isset($input['gaTrackingID'])) {
      $new_input['gaTrackingID'] = sanitize_text_field($input['gaTrackingID']);
    }
    if (isset($input['displayPages'])) {
      $new_input['displayPages'] = sanitize_text_field($input['displayPages']);
    }
    if (isset($input['displayPosts'])) {
      $new_input['displayPosts'] = sanitize_text_field($input['displayPosts']);
    }
    if (isset($input['transformYT'])) {
      $new_input['transformYT'] = sanitize_text_field($input['transformYT']);
    }    
    if (isset($input['layout']) && array_key_exists($input['layout'], self::$availableLayouts)) {
      $new_input['layout'] = $input['layout'];
    }

    // not everytime we save all options, but we don't want to lose them
    $options = get_option('fem-inc-widget-options');

    if(true === is_array($options)){
      foreach ($options as $key => $val){
        if(!array_key_exists($key, $new_input)){
          $new_input[$key] = $val;
        }
      }
    }
    
    return $new_input;
  }
  static public function clbPartnerID() {
    self::clbInputText("partnerID");
  }

  static public function clbCssFiles() {
    self::clbInputText("cssFiles");
  }

  static public function clbHeaderText() {
    self::clbInputText("headerText");
  }

  static public function clbContainerID() {
    self::clbInputText("containerID", "prizma-custom-placement");
  }

  static public function clbGATrackingID() {
    self::clbInputText("gaTrackingID", "UA-XXXXXXXX-X");
  }

  static public function clbDisplayPages() {
    self::clbInputCheckbox("displayPages", "pages");
  }

  static public function clbDisplayPosts() {
    self::clbInputCheckbox("displayPosts", "posts");
  }

  static public function clbLayout() {
    $name = "layout";

    foreach (self::$availableLayouts as $key => $layout) {
      echo "<div class='prizma-widget-radio-group'>";
      printf('<label class="prizma-widget-layout-label ' . $key . '" for="' . $key . '">%s</label>', $layout);
      printf('<input class="prizma-widget-layout-radio" type="radio" id="' . $key . '" name="fem-inc-widget-options[' . $name . ']" value="' . $key . '" %s />', (isset(self::$options[$name]) && $key === self::$options[$name]) ? "checked" : "");
      echo "</div>";
    }
    
    echo "<div class='prizma-layout-warning'>" . Text::get("layoutWarning") . "</div>";
    ?>
      <script>
        jQuery(document).ready(function() {
          var prizmaOnChangeLayout = function() {
            if ('MOSAIC' !== this.value) {
              jQuery(".prizma-layout-warning").show();
            }
            else{
              jQuery(".prizma-layout-warning").hide();
            }
          }
          
          jQuery('input.prizma-widget-layout-radio').change(prizmaOnChangeLayout);
          prizmaOnChangeLayout.call({value: "<?= self::$options[$name]; ?>"});
      });
      </script>
    <?php
    
    
  }

    static public function clbTransformYT() {
    $name = "transformYT";

    $options = array("off" => "off", "on" => "for posts and pages", "wholePage" => "for whole site (including widgets)");
    
    foreach ($options as $key => $value) {
      echo "<div class='prizma-widget-radio-group'>";
      printf('<label class="prizma-widget-transform-yt-label ' . $key . '" for="' . $key . '">%s</label>', $value);
      printf('<input type="radio" id="' . $key . '" name="fem-inc-widget-options[' . $name . ']" value="' . $key . '" %s />', (isset(self::$options[$name]) && $key === self::$options[$name]) ? "checked" : "");
      echo "</div>";
    }
  }
//  

  static public function clbInputText($name, $placeholder ="") {
    printf('<input type="text" id="' . $name . '" placeholder="' . $placeholder . '" name="fem-inc-widget-options[' . $name . ']" value="%s" />', isset(self::$options[$name]) ? esc_attr(self::$options[$name]) : '');
  }

  static public function clbInputCheckbox($name, $label = "") {
    // place hidden input before checkbox to get falsy values
    printf('  <input type="hidden" value="off" name="fem-inc-widget-options[' . $name . ']"><input type="checkbox" id="' . $name . '" name="fem-inc-widget-options[' . $name . ']" %s /> %s', (isset(self::$options[$name]) && "on" === self::$options[$name]) ? "checked" : "", $label);
  }

}
?>
