<?php
  namespace Metatavu\Instagram;
  
  if (!defined('ABSPATH')) { 
    exit;
  }

  require_once( __DIR__ . '/settings.php');
  
  if (!class_exists( '\Metatavu\Instagram\SettingsUI' ) ) {

    /**
     * UI for settings
     */
    class SettingsUI {

      /**
       * Constructor
       */
      public function __construct() {
        
        add_action('admin_init', array($this, 'adminInit'));
        add_action('admin_menu', array($this, 'adminMenu'));
      }

      /**
       * Admin menu action. Adds admin menu page
       */
      public function adminMenu() {
        add_options_page (__( "Instagram Settings", 'instagramFeed' ), __( "Instagram", 'instagramFeed' ), 'manage_options', 'instagramFeed', [$this, 'settingsPage']);
      }

      /**
       * Admin init action. Registers settings
       */
      public function adminInit() {
        register_setting('instagramFeed', 'instagramFeed');

        add_settings_section('api', __( "Api", 'instagramFeed' ), null, 'instagramFeed');
        $this->addFieldOption('api', 'text', 'accesstoken', __( "Access token", 'instagramFeed'));
      }

      /**
       * Adds new option
       * 
       * @param string $group option group
       * @param string $type option type
       * @param string $name option name
       * @param string $title option title
       */
      private function addFieldOption($group, $type, $name, $title) {
        add_settings_field($name, $title, array($this, 'createFieldUI'), 'instagramFeed', $group, [
          'name' => $name, 
          'type' => $type
        ]);
      }

      /**
       * Prints field UI
       * 
       * @param array $opts options
       */
      public function createFieldUI($opts) {
        $name = $opts['name'];
        $type = $opts['type'];
        $value = Settings::getValue($name);

        echo "<input type='$type' style='width:100%;' name='" . 'instagramFeed' . "[$name]' value='$value'/>";
      }


      /**
       * Prints settings page
       */
      public function settingsPage() {
        if (!current_user_can('manage_options')) {
          wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
        }

        echo '<div class="wrap">';
        echo "<h2>" . __( "Options", 'instagramFeed') . "</h2>";
        echo '<form action="options.php" method="POST">';
        settings_fields('instagramFeed');
        do_settings_sections('instagramFeed');
        submit_button();
        echo "</form>";
        echo "</div>";
      }
    }

  }
  
  if (is_admin()) {
    $settingsUI = new SettingsUI();
  }

?>