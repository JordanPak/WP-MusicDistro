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


    //-- ARCHIVE PAGE INFORMATION --//
    $band_slug = $atts['band'];                                             // SLUG of the band (parent category)
    $band_term = get_term_by('slug', $band_slug, 'download_category');      // Get the whole term BY slug, using the BAND SLUG (Parent Category Slug) as the term, INSIDE the download_category taxonomy
    $band_id = $band_term->term_id;                                         // Get the ID from that term
    $band_name = $band_term->name;                                          // Get the Name from that term also



    //-- SELECTED INSTRUMENT INFORMATION --//
    $selected = isset($_REQUEST['cat']) && $_REQUEST['cat'] != '' ? $_REQUEST['cat'] : 0;           // Get selected instrument

    $selected_instrument_id = $selected;                                                            // ID       of selected instrument
    $selected_instrument_term = get_term_by('id', $selected_instrument_id, 'download_category');    // TERM     of selected instrument
    $selected_instrument_slug = $selected_instrument_term->slug;                                    // SLUG     of selected instrument
    $selected_instrument_name = $selected_instrument_term->name;                                    // NAME     of selected instrument


    //-- MUSICDISTRO WRAPPER --//
    $output .= '<div class="musicdistro-wrap">';


        //-- START GRID --//
        $output .= '<section class="masonry-6col-grid">';
            $output .= '<div class="masonry-6col-grid-sizer"></div>';
            $output .= '<div class="masonry-6col-gutter-sizer"></div>';



            //-- INSTRUMENT FORM --//
            $output .= musicdistro_archive_instrument_form( $selected, $band_id, $selected_instrument_id );


            //-- BREAKER BLOCK --//
//            $output .= '<div class="masonry-block masonry-block-size--one-whole masonry-breaker-block"></div>';


            //-- IF AN INSTRUMENT HAS BEEN SELECTED --//
            if( $selected ) {


                // Arrangements Query Args
                $arrangementSelection = array(
                    'post_type'			=> 'download',
                    'download_category'	=> $band_slug,
                    'fields'            => 'ids',                       // This is so only the ID is returned instead of the WHOLE post object (Performance)
                    'orderby'           => 'title',
                    'order'             => 'ASC',
                    'posts_per_page'    => -1
                );

                // ARRAY OF ALL SONGS FOR THE SELECTED INSTRUMENT
                $arrangements = new WP_Query( $arrangementSelection );



                // No Arrangements Found
                if( ($arrangements->have_posts()) == false )
                    $output .=  '
                        <div class="block masonry-block masonry-block-size--one-third">
                            <i class="fa fa-exclamation-triangle"></i> No ' . $band_name . ' ' . $selected_instrument_name . ' arrangements found!
                        </div>
                    ';


                // GET ARRANGEMENT POSTS
                $arrangements = $arrangements->get_posts();


                // SONG TYPES (Tags)
                $tags = wp_get_object_terms( $arrangements, 'download_tag');


                // Remove Duplicate Tags
                $tags = array_unique($tags, SORT_REGULAR);




                //-- CATEGORY BOXES (TAGS) --//
                foreach( $tags as $tag ) {


                    // Wrap in Block
                    $output .= '<div class="musicdistro-masonry-block block masonry-block masonry-block-size--one-third">';


                        // ARRANGEMENT TYPE LABEL
                        $output .= '<h3 class="musicdistro-type-heading">' . $tag->name . '</h3>';


                        // ARRANGEMENT TYPE BODY
                        $output .= '<div class="musicdistro-type-body">';


                                //-- CYCLE THROUGH ARRANGEMENTS --//

                                // Just the IDs of the arrangements
                                foreach( $arrangements as $arrangement ) {


                                    // Get the arrangement post from the ID
                                    $object = get_post( $arrangement );



                                    //-- CHECK IF CURRENT ARRANGEMENT HAS TAG FOR THIS BOX --//
                                    if( has_term( $tag, 'download_tag', $object ) ) {


                                        // Arrangement Wrap
                                        $output .= '<div class="musicdistro-arrangement">';

                                            //-- Display Arrangement Title --//
                                            $output .=  '<span class="musicdistro-arrangement-title">' . get_the_title( $arrangement ) . '</span>';



                                            //-- Arrangement Buttons --//
                                            $output .= '<div class="musicdistro-arrangement-buttons">';


                                                //-- Get Files (Names & URLSs) For Current Arrangement --//
                                                $files = edd_get_download_files( $arrangement );


                                                // Set counter for unsetting, keeps track of what index we're at for removing
                                                $counter_a = 0;


                                                // Used to see if any matches were found
                                                $match_found = false;


                                                //-- CYCLE THROUGH FILES OF CURRENT ARRANGEMENT --//
                                                //--     AND REMOVE UNMATCHING INSTRUMENTS      --//
                                                foreach( $files as $file ) {


                                                    //-- Explode File Into Array of Strings --//
                                                    $explosion = explode(" ", $file['name']);



                                                    //---------------------//
                                                    // TWO WORD INSTRUMENT //
                                                    //------------------------------------------------//
                                                    // If the second word is NOT a number and EXISTS  //
                                                    //------------------------------------------------//
                                                    if( (is_numeric($explosion[1]) == FALSE) && ($explosion[1] != NULL) ) {

                                                        //-- Unset Current File If It's Not For Selected Instrument --//
                                                        if ( ($explosion[0] . ' ' . $explosion[1]) !== $selected_instrument_name )
                                                            unset($files[$counter_a]);

                                                        else
                                                            $match_found = true;

                                                    }



                                                    //---------------------//
                                                    // ONE WORD INSTRUMENT //
                                                    //-------------------------------------------//
                                                    // If it's NOT a two word instrument (else)  //
                                                    //-------------------------------------------//
                                                    else {

                                                        if ($explosion[0] !== $selected_instrument_name)
                                                            unset($files[$counter_a]);

                                                        else
                                                            $match_found = true;

                                                    } // else


                                                    // Increment counter
                                                    $counter_a++;

                                                } // foreach files as file



                                                // Sorts the array alphabetically
                                                asort( $files );


                                                // Counter for pipes (USING BUTTONS NOW)
                                                $counter = 1;


                                                //-- PROCEED WITH DOWNLOAD LINKS IF MATCH FOUND --//
                                                if ($match_found == true) {


                                                    //-- CYCLE THROUGH FILTERED FILES PRINT APPROPRIATE --//
                                                    //--        LINKS FOR THE SELECTED INSTRUMENT       --//
                                                    foreach( $files as $file ) {

                                                        //-- Explode File Into Array of Strings --//
                                                        $explosion = explode(" ", $file['name']);


                                                        //-- If the first word OR first two words = the slected instrument --//
                                                        if (
                                                              ($explosion[0] == $selected_instrument_name) ||
                                                              (($explosion[0] . ' ' . $explosion[1]) == $selected_instrument_name)
                                                           ) {


                                                            // Remove the instrument name and space from file name (variable)
                                                            $name = str_replace($selected_instrument_name." ","",$file['name']);


                                                            // Exception for Recordings: Different Icon!
                                                            if( $selected_instrument_name == "Recordings" ) {
                                                                $output .=  '<a class="musicdistro-download musicdistro-recording" href="'.$file['file'].'" target="_blank"><i class="fa fa-play"></i></a>';
                                                            }

                                                            // Not recording
                                                            else {

                                                                // If the arrangment only has one part for a given instrument
                                                                // (Detected by the input name not having a number)
                                                                if ( ( is_numeric($explosion[1]) == FALSE ) && ( is_numeric($explosion[2]) == FALSE ) )
                                                                    $output .=  '<a class="musicdistro-download" href="'.$file['file'].'" target="_blank"><i class="fa fa-download"></i></a>';


                                                                // For sheet music with more than one part for a given instrument
                                                                else
                                                                    $output .=  '<a class="musicdistro-download" href="'.$file['file'].'" target="_blank">' . $name . '</a>';

                                                            } // Else: Not recording


                                                            // If it's not the last item, put in a space
                                                            if ( $counter != count($files)){
                                                                $output .=  '&nbsp';
                                                            }


                                                            //-- Unset / Reset Array --//
                                                            $explosion = array();


                                                            $counter++;


                                                        } // IF the name of the file = selected instrument

                                                    } // foreach: files as file

                                                } // MATCH FOUND


                                                //-- NO MATCH FOUND --//
                                                else {
                                                    $output .= '<span class="md-not-available">N/A</span>';
                                                }


                                            // Finish dl-buttons
                                            $output .=  '</div>';


                                        // Close Arrangement
                                        $output .= '</div>';


                                    } // if: has_term download tag


                                } // foreach: arrangements as arrangement


                            // Close Body
                            $output .= '</div>';


                        // Close Masonry Block
                        $output .= '</div>';

                    } // foreach: $tags


            } // If $selected


            /* Restore original Post Data */
            wp_reset_postdata();


			// Display Error Checking if logged in
			if ( current_user_can('shop_manager') || current_user_can('administrator') ) {

				// Breaker Block
				$output .= '<div class="masonry-block masonry-block-size--one-whole masonry-breaker-block"></div>';

				// Form
				$output .= '<div class="musicdistro-errorcheck-wrapper block masonry-block masonry-block-size--two-thirds block-featured">';
					$output .= musicdistro_error_check();
				$output .= '</div></div>';

			} // If user is Shop Manager or Admin


        // Close Grid
        $output .= '</section>';


    // Close Wrap
    $output .= '</div>';


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


    $output .= '<div class="musicdistro-form-block block masonry-block masonry-block-size--one-third">';


        $output .= '<form class="musicdistro-instrument-form" role="form">';


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
                'echo'               => 0,
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
            $output .= '<button type="submit">Get Music</button>';


        $output .= '</form>';


    // Close Masonry Block
    $output .= '</div>';


    return $output;

} // musicdistro_archive_instrument_form()
