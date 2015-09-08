<?php

/**
 * WP MusicDistro Error Check
 *
 * @author Jordan Pakrosnis
 */
function musicdistro_error_check() {

    // Output variable
    $output = '';


    // Selection setter
    $selected = isset($_REQUEST['do-error-check']);

    // Display form
    $output .= '<form role="form"><button class="button button-outline button-red" type="submit" name="do-error-check">Check For Errors</button></form>';


    // Check if Selected
    if ( $selected ) {

        // Spacer
        $output .= '<br>';


        // Arrangements Query Args
        $arrangementSelection = array(
            'post_type'			=> 'download',
            'fields'            => 'ids',                       // This is so only the ID is returned instead of the WHOLE post object (Performance)
            'orderby'           => 'title',
            'order'             => 'ASC',
            'posts_per_page'    => -1,
            'post_status'       => 'any',
        );

        // ARRAY OF ALL ARRANGEMENTS
        $arrangements = new WP_Query( $arrangementSelection );


        // No Arrangements Found
        if( ($arrangements->have_posts()) == false )
            $output .=  '<i class="fa fa-exclamation-triangle"></i> No arrangements found!';


        // GET ARRANGEMENT POSTS
        $arrangements = $arrangements->get_posts();


        // SONG TYPES (Tags)
        $tags = wp_get_object_terms( $arrangements, 'download_tag');


        // GET TRUE CATEGORIES (INSTRUMENTS)
        $get_categories_args = array(
            'type' => 'download',
            'orderby' => 'name',
            'order' => 'ASC',
            'hide_empty' => 0,
            'taxonomy' => 'download_category'
        );
        $instrument_categories = get_categories($get_categories_args);


        // Array of Instrument Names
        $instrument_names = array();


        // Bands & Instruments Found Label
        // $output .= '<p><b>Instruments &amp; Bands Found:</b></p><ul>';

        // Cycle Through Each Category/Band/Instrument and Display Name
        foreach ($instrument_categories as $instrument_category) {
            // $output .= '<li>' . $instrument_category->name . '</li>';
            $instrument_names[] = $instrument_category->name;
        }

        // $output .= '</ul><hr>';



        //-- CYCLE THROUGH ARRANGEMENTS --//
        foreach( $arrangements as $arrangement ) { // IDs


            // Get Arrangement POST
            $object = get_post( $arrangement );


            // Error Counter


            // Arrangement Wrapper
            $output .= '<div class="musicdistro-errorcheck-arrangement-wrapper">';


                // Arrangement TITLE
                $output .=  '<b>' . get_the_title( $arrangement ) . '</b>';


                // Arrangement POST STATUS
                if ( get_post_status( $arrangement ) != 'publish' )
                    $output .= ' <span class="musicdistro-errorcheck-unpublished">(unpublished)</span> ';


                // Arrangement Post Edit link
                $output .= '<a class="musicdistro-errorcheck-post-link" href="' . get_edit_post_link($arrangement) . '" target="_BLANK"><i class="fa fa-edit"></i></a>';


                // Error Labels Wrapper
                $output .= '<div class="musicdistro-error-labels">';


                // Arrangement Terms (Band / Bands) Label
                // $output .= '<p><b>Band(s):</b> ';


                // Arrangement TERMS
                $arrangement_terms = wp_get_post_terms( $arrangement, 'download_category' );

                // If No Terms
                if ( $arrangement_terms == null )
                    $output .= '<span class="musicdistro-label musicdistro-label-warning"><i class="fa fa-exclamation-triangle"></i> Band Not Set</span>';

                // // If terms found, list them
                // else {
                //     foreach ($arrangement_terms as $arrangement_term) {
                //         $output .= $arrangement_term->name . ' ';
                //     }
                // }


                // Arrangement TAGS (Song Types)
                $arrangement_tags = wp_get_object_terms( $arrangement, 'download_tag');

                // If No Tags
                if ( $arrangement_tags == null )
                    $output .= '<span class="musicdistro-label musicdistro-label-warning"><i class="fa fa-exclamation-triangle"></i> No Arrangement Type</span>';


                //-- Arrangement FILES & URLS --//
                $files = edd_get_download_files( $arrangement );


                // Parts Found Label
                // $output .= '<br><b>Parts Found</b></p><ul>';


                //-- CYCLE THROUGH FILES OF CURRENT ARRANGEMENT --//
                foreach( $files as $file ) {

                    // Instrument Name (to cross-reference)
                    $instrument_name = '';


                    //-- Explode File Into Array of Strings --//
                    $explosion = explode(" ", $file['name']);

                    // Preview Name
                    // $output .= '<li>' . $explosion[0] . ' ' . $explosion[1] . ' ' . $explosion[2];


                    // CHECK FOR TWO-WORD INSTRUMENT
                    if( (is_numeric($explosion[1]) == FALSE) && ($explosion[1] != NULL) ) {
                        $instrument_name = $explosion[0] . ' ' . $explosion[1];
                    }
                    else {
                        $instrument_name = $explosion[0];
                    }


                    // CHECK FOR INSTRUMENT VALIDITY
                    $match_found = in_array($instrument_name, $instrument_names);

                    if ( $match_found == null )
                        $output .= '<span class="musicdistro-label musicdistro-label-error"><i class="fa fa-exclamation-triangle"></i> Unrecognized Instrument: <b>' . $instrument_name . '</b></span>';
                        // $output .= '(<i>Instrument Not Found. array_search: ' . $match_found . '</i>)';


                    // $output .= '</li>';

                } // foreach file


                // $output .= '</ul>';

                // Close Error Labels Wrap
                $output .= '</div>';

            // Close Arrangement Wrap
            $output .= '</div>';

            // Add Divider
            $output .= '<hr>';

        } // foreach: arrangements as arrangement


    } // if $selected

    return $output;

} // musicdistro_error_check();
