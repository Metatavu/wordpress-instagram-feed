<?php
  namespace Metatavu\Instagram;
  
  if (!defined('ABSPATH')) { 
    exit;
  }

  require_once( __DIR__ . '/../vendor/autoload.php');
  require_once( __DIR__ . '/../settings/settings.php');

  use GuzzleHttp\Client;
  use GuzzleHttp\Psr7\Request;

  if (!class_exists( 'Metatavu\Instagram\Shortcodes' ) ) {
    
    class Shortcodes {
      
      /**
       * Constructor
       */
      public function __construct() {
        add_filter('the_content', [$this, 'filter_content_for_replace_text']);
      }

      public function filter_content_for_replace_text($content) {
        $newContent = $this->instagramShortCode();
        $content = preg_replace('/\[instagram_feed([^\]]*)\]/', $newContent, $content, -1);

        return $content;
      }

      /**
       * Update instagram feed to post meta
       */
      public function updateInstagramFeed($postId) {
        $accessToken = Settings::getValue('accesstoken');
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_RETURNTRANSFER => 1,
          CURLOPT_URL => sprintf('https://api.instagram.com/v1/users/self/media/recent/?access_token=%s', $accessToken)
        ));

        $resp = curl_exec($curl);
        curl_close($curl);

        $feedArray = json_decode($resp); 
        $feed = $feedArray->data;
        $html = "";
        
        foreach($feed as $feedItem) {
          $html = $html . '<div class="instagram-feed-item">';
          $html = $html . sprintf('<a href="https://instagram.com/%s">', $feedItem->user->username);
          $html = $html . sprintf('<img src="%s" alt="Instagram image"/>', $feedItem->images->thumbnail->url);
          $html = $html . '</a></div>';
        }

        update_post_meta($postId, 'instagram_feed', $html);
      }

      /**
       * Build shortcode
       */
      public function instagramShortCode () {
        if (is_admin()) {
          return;
        }

        $postId = 15;
        $updateTime = get_option('update_instagram_feed_time');
        $currentDate = new \DateTime();

        if (empty($updateTime)) {
          $currentDate->modify("+5 minutes");
          $dateString = $currentDate->format('Y-m-d H:i:s');
          $this->updateInstagramFeed($postId);
          $this->updateTimeOption($dateString);
        } else {
          $updateAt = new \DateTime($updateTime);
          if ($currentDate > $updateAt) {
            $currentDate->modify("+5 minutes");
            $this->updateInstagramFeed($postId);
            $this->updateTimeOption($currentDate->format('Y-m-d H:i:s'));
          }
        }

        $html = get_post_meta($postId, 'instagram_feed', true);
        return '<div id="instagramFeedContainer">'. $html .'</div>';
      }

      /**
       * Update time option
       */
      private function updateTimeOption($time) {
        $optionName = 'update_instagram_feed_time';

        if (get_option($optionName) !== false) {
          update_option($optionName, $time);
        } else {
          $deprecated = null;
          $autoload = 'no';
          add_option($optionName, $time, $deprecated, $autoload);
        }
      }
    }
  }
  
  add_action('init', function () {
    new Shortcodes();
  });
  
?>
