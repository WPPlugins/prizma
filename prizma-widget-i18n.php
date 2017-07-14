<?php

class Text {

  private static $language = array(
      'settingsMenuLink' => "Prizma Widget",
      'settingsHeader' => "Manage Default Widget Settings",
      'settingsDescription' => 'Some settings can also be adjusted on indvidual posts and pages.',
      "dashboardTitle" => "Your Prizma Dashboard",
      "dashboardDescription" => "View engagement metrics and manage content settings.",
      "partnerIDTitle" => "Activate Prizma with your Partner ID",
      "partnerIDDescription" => "Enter your partner ID or get an account by getting in touch with our business development team <a href='http://prizma.tv/get-started?SQF_ORIGIN=wordpress'>here</a>",
      "cssFilesTitle" => "CSS Files",
      "cssFilesDescription" => "Advanced users can change the look and feel of the Prizma widget using the guidelines <a href='http://www.prizma.tv/docs/customization'>here</a>.",
      "displayPagesTitle" => "Show the widget on the following types of pages",
      "displayPagesDescription" => "You can toggle the widget on individual pages, if necessary.",
      "displayPostsTitle" => "",
      "displayPostsDescription" => "",
      "headerTextTitle" => "Widget Title",
      "headerTextDescription" => 'This text appears above the widget. The default is "Recommended Videos."',
      "transformYTTitle" => "Automatically add recommendations to YouTube videos",
      "transformYTDescription" => "Prizma for WordPress automatically adds End Slate recommendations to YouTube videos embedded in your posts by default. You can learn more about this <a href='http://www.prizma.tv/docs/wordpress'>here</a>.",
      "layoutTitle" => "Select layout",
      "layoutDescription" => "This setting changes the display of the widget that appears at the end of your pages. We recommend sticking with Mosaic, as it typically performs best in that context. Learn more about formats <a href='http://www.prizma.tv/docs/formats'>here</a>.",
      "containerIDTitle" => "Custom container ID",
      "containerIDDescription" => "Set #id of the container in which Prizma Widget will be rendered",
      "gaTrackingIDTitle" => "Google Analytics tracking ID",
      "gaTrackingIDDescription" => "Log into your <a href='https://analytics.google.com/' target='_blank'>Google Analytics account</a> and copy the code next to your website name (<a href='http://cdn.prizma.tv/images/google-analytics-code.png' target='_blank'>example</a>)",
      "sidebarInformation" => "To add a sidebar widget, go to <a href='%s'>Appearance > Widgets</a>.",
      "layoutWarning" => "Mosaic is our highest-engagement widget format. To ensure your visitors spend the most possible time on your site and watch the most video content, we recommend using Mosaic.",
      "noPartnerIDErrorMsg" => 'Please provide a valid partnerID. You can find your partnerID at <a href="https://dashboard.prizma.tv">https://dashboard.prizma.tv</a>. If you do not have a Prizma account, please visit <a href="http://prizma.tv/get-started">http://prizma.tv/get-started</a>.',
  );

  static public function get($key) {
    return isset(self::$language[$key]) ? self::$language[$key] : "";
  }

}
