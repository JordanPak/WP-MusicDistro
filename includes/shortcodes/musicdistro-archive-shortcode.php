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
    
    

//    //-- ARCHIVE PAGE INFORMATION --//
//    
//    // Array for Storing Options
//    $options[] = '';
//    
//    
//    // SLUG of the band (parent category)
//    $options['band_slug'] = $atts['band'];
//
//
//    // Get the whole term BY slug, using the BAND SLUG (Parent Category Slug) 
//    // as the term, INSIDE the download_category taxonomy
//    $options['band_term'] = get_term_by( 'slug' , $options['band_slug'], 'download_category' );
//
//
//    // Get the ID from that term
//    $options['band_id'] = $options['band_term']->term_id;
//
//
//    // Get the Name from that term also
//    $options['band_name'] = $options['band_term']->name;


    //-- ARCHIVE PAGE INFORMATION --//
    
    $band_slug = $atts['band'];                                             // SLUG of the band (parent category)
    $band_term = get_term_by('slug', $band_slug, 'download_category');      // Get the whole term BY slug, using the BAND SLUG (Parent Category Slug) 
                                                                            // as the term, INSIDE the download_category taxonomy
    $band_id = $band_term->term_id;                                         // Get the ID from that term
    $band_name = $band_term->name;                                          // Get the Name from that term also


    
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
    
    
    // Show Form
    $output .= musicdistro_archive_instrument_form( $selected, $band_id, $selected_instrument_id );
    
    
    
    // Return Output String
	return $output;
    
} // musicdistro_archive_shortcode()

// Register the shortcode
add_shortcode( 'musicdistro', 'musicdistro_archive_shortcode' );



/**
 * WP MusicDistro Archive Form
 *
 * @author Jordan Pakrosnis
 */
function musicdistro_archive_instrument_form( $selected, $band_id, $selected_instrument_id ) {
    
    // Output
    $output = '';
    
    
    $output .= '<form class="form-horizontal" role="form">';


        if( $selected != 0 ) {
            $output .= '<p><b>Different Instrument? Recordings?</b></p>';
        }
        else {
            $output .= '<p><b>Select an Instrument or Recordings</b></p>';
        }


        // Parameters for category dropdown
        $catArgs = array(
            'show_option_all'    => '',
            'show_option_none'   => '',
            'orderby'            => 'ID', 
            'order'              => 'ASC',
            'show_count'         => 0, // Shows number of arrangements for that instrument
            'hide_empty'         => 0, 
            'child_of'           => $band_id,
            'exclude'            => '',
            'echo'               => 1,
            'selected'           => $selected_instrument_id,
            'hierarchical'       => 0, 
            'name'               => 'cat',
            'id'                 => '',
            'class'              => 'form-control',
            'depth'              => 0,
            'tab_index'          => 0,
            'taxonomy'           => 'download_category',
            'hide_if_empty'      => false,
            'walker'             => ''
        );


        // Display dropdown for categories
        $output .= wp_dropdown_categories( $catArgs );
    

        // Submit Button for Selecting Instrument
        $output .= '<button type="submit" class="button">Get Music</button>';

    
    $output .= '</form>';

            
    return $output;
            
} // musicdistro_archive_instrument_form()
