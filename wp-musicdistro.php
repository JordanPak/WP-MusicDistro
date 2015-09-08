<?php
/*
Plugin Name: WP MusicDistro
Plugin URI:  http://JordanPak.com/
Description: Music Distribution System Easy Digital Downloads Addon for Bands
Version:     2.0
Author:      Jordan Pakrosnis
Author URI:  http://JordanPak.com/
*/


//============//
//  INCLUDES  //
//============//

// Admin
require_once( 'includes/admin/musicdistro-admin.php' );

// Settings
require_once( 'includes/settings/musicdistro-archive-settings.php' );

// Shortcodes
require_once( 'includes/shortcodes/musicdistro-archive-shortcode.php' );

// Error Checking
require_once( 'includes/musicdistro-error-check.php' );



//==========//
//  STYLES  //
//==========//

// ENQUEUE GLOBAL STYLES
add_action( 'wp_enqueue_scripts', 'musicdistro_styles' );
function musicdistro_styles() {
    wp_enqueue_style( 'musicdistro',  plugins_url() . '/wp-musicdistro/includes/css/musicdistro-styles.css', array() );
} // musicdistro_styles()
