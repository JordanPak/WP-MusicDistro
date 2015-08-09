<?php
/**
 * WP MusicDistro Archive Shortcode
 *
 * @author Jordan Pakrosnis
 * @copyright	Copyright (c) 2015, Jordan Pakrosnis
 * @since		2.0
 */


/**
 * WP MusicDistro Archive Shortcode Registration
 *
 * @author Jordan Pakrosnis
 */
function musicdistro_archive_shortcode( $atts ) {
    
    
    //-- ATTRIBUTES --//
	$atts = shortcode_atts( array(
        'size'      => 'med',
	), $atts, 'musicdistro' );

    
    
    //-- SET VARS --//
    
    // Attributes
    $button_size =      $atts['size'];
    
    // Attribute Helpers
    $button_classes = '';
    
    // Output
    $output = '';
    
    
    
    //=========//
    //  LOGIC  //
    //=========//
    
    
    //-- CLASSES --//
    
    // Default
    $button_classes .= 'button ';
    

    
    
    //==========//
    //  OUTPUT  //
    //==========//
    
    
    
    // Return Output String
	return $output;
    
} // musicdistro_archive_shortcode()

// Register the shortcode
add_shortcode( 'musicdistro', 'musicdistro_archive_shortcode' );
