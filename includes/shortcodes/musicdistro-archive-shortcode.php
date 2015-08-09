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
        'band'      => 'marching-knights',
	), $atts, 'musicdistro' );

    
    // Output
    $output = '';
    
    
    
    //=========//
    //  LOGIC  //
    //=========//
    
    
    //-- CLASSES --//
    
    // Default
    $button_classes .= 'button ';
    
    

    //-- ARCHIVE PAGE INFORMATION --//
    
    // Array for Storing Options
    $options[] = '';
    
    
    // SLUG of the band (parent category)
    $options['band_slug'] = $atts['band'];


    // Get the whole term BY slug, using the BAND SLUG (Parent Category Slug) 
    // as the term, INSIDE the download_category taxonomy
    $options['band_term'] = get_term_by( 'slug' , $options['band_slug'], 'download_category' );


    // Get the ID from that term
    $options['band_id'] = $options['band_term']->term_id;


    // Get the Name from that term also
    $options['band_name'] = $options['band_term']->name;
    

    
    //-- SELECTED INSTRUMENT INFORMATION --//

    // Get selected instrument
    $selected = isset($_REQUEST['cat']) && $_REQUEST['cat'] != '' ? $_REQUEST['cat'] : 0;


    $selected_instrument_id = $selected;                                                                // ID of selected instrument
    $selected_instrument_term = get_term_by('id', $selected_instrument_id, 'download_category');        // TERM of selected instrument
    $selected_instrument_slug = $selected_instrument_term->slug;                                        // SLUG of selected instrument
    $selected_instrument_name = $selected_instrument_term->name;                                        // NAME of selected instrument

    
    
    //==========//
    //  OUTPUT  //
    //==========//
    
    
    
    // Return Output String
	return $output;
    
} // musicdistro_archive_shortcode()

// Register the shortcode
add_shortcode( 'musicdistro', 'musicdistro_archive_shortcode' );
