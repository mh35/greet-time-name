<?php
/*
Plugin Name: Greet Time Name
Plugin URI: https://github.com/mh35/greet-time-name
Description: Replace greeting message with time message.
Version: 1.0
Author: mh35
Author URI: https://profiles.wordpress.org/mh35/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: greet-time-name
Domain Path: /languages
*/

if(!function_exists('greet_time_name_plugins_loaded')) {
  function greet_time_name_plugins_loaded() {
    load_plugin_textdomain('greet-time-name', false, basename(
      dirname(__FILE__)) . '/languages');
  }
}
add_action('plugins_loaded', 'greet_time_name_plugins_loaded');

if(!function_exists('greet_time_name_add_menu_item')) {
  function greet_time_name_add_menu_item($wp_admin_bar) {
    $user_id = get_current_user_id();
    if(!$user_id) {
      return;
    }
    $current_user = wp_get_current_user();
    $profile_url = get_edit_profile_url($user_id);
    $avatar_size = apply_filters('greet-time-name_avatar_size', 26);
    $avatar = get_avatar($user_id, $avatar_size);
    $time = date_i18n('G', time() + get_option('gmt_offset') * HOUR_IN_SECONDS);
    $time = (int)$time;
    if($time >= 6 && $time < 12) {
      $howdy = sprintf(__('Good morning, %s', 'greet-time-name'),
        $current_user->display_name);
    } elseif($time >= 12 && $time < 18) {
      $howdy = sprintf(__('Good afternoon, %s', 'greet-time-name'),
        $current_user->display_name);
    } else {
      $howdy = sprintf(__('Good evening, %s', 'greet-time-name'),
        $current_user->display_name);
    }
    $howdy = apply_filters('greet-time-name_message', $howdy, $time);
    $class = empty($avatar)?'':'with-avatar';
    $wp_admin_bar->add_menu(array(
      'id' => 'my-account',
      'parent' => 'top-secondary',
      'title' => $howdy . $avatar,
      'href' => $profile_url,
      'meta' => array(
        'class' => $class
      ) 
    ));
  }
}

if(!function_exists('greet_time_name_admin_bar_menus')) {
  function greet_time_name_admin_bar_menus() {
    remove_action('admin_bar_menu', 'wp_admin_bar_my_account_item', 7);
    add_action('admin_bar_menu', 'greet_time_name_add_menu_item', 7);
  }
}
add_action('add_admin_bar_menus', 'greet_time_name_admin_bar_menus');
