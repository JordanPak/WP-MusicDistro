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


        // HEADER LABELS
        $output .= '<div class="musicdistro-errorcheck-header"><h3>Arrangement</h3><div class="musicdistro-error-labels"><h3>Errors &amp; Warnings</h3></div></div>';



        //-- CYCLE THROUGH ARRANGEMENTS --//
        foreach( $arrangements as $arrangement ) { // IDs


            // Get Arrangement POST
            $object = get_post( $arrangement );


            // Warning & Error Counters
            $num_warnings = 0;
            $num_errors = 0;

            // Warning & Error String Arrays
            $warnings = array();
            $errors = array();


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
                if ( $arrangement_terms == null ) {
                    $warnings[] = 'Band Not Set';
                }

                // // If terms found, list them
                // else {
                //     foreach ($arrangement_terms as $arrangement_term) {
                //         $output .= $arrangement_term->name . ' ';
                //     }
                // }


                // Arrangement TAGS (Song Types)
                $arrangement_tags = wp_get_object_terms( $arrangement, 'download_tag');

                // If No Tags
                if ( $arrangement_tags == null ) {
                    $warnings[] = 'No Arrangement Type';
                }


                //-- Arrangement FILES & URLS --//
                $files = edd_get_download_files( $arrangement );


                // Parts Found Label
                // $output .= '<br><b>Parts Found</b></p><ul>';


                //-- CYCLE THROUGH FILES OF CURRENT ARRANGEMENT --//
                foreach( $files as $file ) {

                    // Instrument Name (to cross-reference)
                    $instrument_name = '';
                    $instrument_name_words = 0;

                    // Instrument Part Number
                    $part_number = NULL;


                    //-- Explode File Into Array of Strings --//
                    $explosion = explode(" ", $file['name']);

                    // Preview Name
                    // $output .= '<li>' . $explosion[0] . ' ' . $explosion[1] . ' ' . $explosion[2];


                    // CHECK FOR TWO-WORD INSTRUMENT //
                    // Second String ISN'T a Number and isn't Null
                    if( (is_numeric($explosion[1]) == FALSE) && ($explosion[1] != NULL) ) {
                        $instrument_name = $explosion[0] . ' ' . $explosion[1];
                        $instrument_name_words = 2;
                    }


                    // CHECK FOR ONE-WORD INSTRUMENT //
                    // First String Isn't Number
                    else if ( is_numeric($explosion[0]) == FALSE ) {

                        // Second String IS a number or doesn't exist
                        if ( (is_numeric($explosion[1]) == TRUE) || ($explosion[1] == NULL) ) {
                            $instrument_name = $explosion[0];
                            $instrument_name_words = 1;
                        }

                    } // First string isn't a number


                    // ONE-WORD Checks & Sets
                    if ( $instrument_name_words == 1 ) {

                        // If a third string exists
                        if ( $explosion[2] != NULL ) {
                            $errors[] = 'File Name Error: <b>' . $file['name'] . '</b>';
                        }

                        // Set Part Number - If one-word instrument and second string exists (has to be a number)
                        if ( $explosion[1] != NULL ) {
                            $part_number = $explosion[1];
                        }

                    } // If one-word instrument Checks


                    // TWO-WORD Checks & Sets
                    if ( $instrument_name_words == 2 ) {

                        // If a fourth string exists
                        if ( $explosion[3] != NULL) {
                            $errors[] = 'File Name Error: <b>' . $file['name'] . '</b>';
                        }

                        // If a third string exists and it's not a number
                        else if ( ($explosion[2] != NULL) && (is_numeric($explosion[2]) == FALSE) ) {
                            $errors[] = 'File Name Error: <b>' . $file['name'] . '</b>';
                        }

                        // Set Part Number - If third string exists it has to be a part number; Double-check Through
                        if ( ($explosion[2] != NULL) && (is_numeric($explosion[2]) == TRUE) ) {
                            $part_number = $explosion[2];
                        }

                    } // If one-word instrument Checks


                    // CHECK INSTRUMENT RECOGNITION //
                    $match_found = in_array($instrument_name, $instrument_names);

                    if ( $match_found == null ) {
                        $errors[] = 'Unrecognized Instrument: <b>' . $instrument_name . '</b>';
                    }


                    // CHECK PART NUMBER FEASIBILITY //
                    if ( $part_number > 3 ) {
                        $warnings[] = 'High Part Number: ' . $instrument_name . ' <b>' . $part_number . '</b>';
                    }

                    // $output .= '</li>';


                    // CHECK URL VALIDITY //

                    // No URL
                    if ( $file['file'] == NULL) {
                        $errors[] = 'No URL for <b>' . $instrument_name . '</b>';
                    }

                    // Invalid URL
                    else if (filter_var($file['file'], FILTER_VALIDATE_URL) === FALSE) {
                        $errors[] = 'Bad URL for <b>' . $instrument_name . '</b>';
                    }


                    // CHECK FOR 404
                    $file_headers = @get_headers($file['file']);
                    if ( strpos($file_headers[0], '404') !== FALSE ) {
                        $errors[] = 'File Not Found (404): <b>' . $instrument_name . '</b>';
                    }


                } // foreach file


                // $output .= '</ul>';


                // NO ERRORS or WARNINGS?
                if ( (sizeof($errors) == 0) && (sizeof($warnings) == 0) ) {  // if ( ($num_errors == 0) && ($num_warnings == 0) ) {
                    $output .= '<span class="musicdistro-label musicdistro-label-noerror"><i class="fa fa-check-square-o"></i>&nbsp;&nbsp;No Errors Found</span>';
                }

                else {

                    // Display Errors
                    if ( sizeof($errors) > 0 ) {

                        $output .= '<span class="musicdistro-label musicdistro-label-error"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;<b>Errors</b><ul>';

                            foreach ($errors as $error) {
                                $output .= '<li>' . $error . '</li>';
                            }

                        $output .= '</ul></span>';

                    } // Display Errors


                    // Display Warnings
                    if ( sizeof($warnings) > 0 ) {

                        $output .= '<span class="musicdistro-label musicdistro-label-warning"><i class="fa fa-exclamation-triangle"></i>&nbsp;&nbsp;<b>Warnings</b><ul>';

                            foreach ($warnings as $warning) {
                                $output .= '<li>' . $warning . '</li>';
                            }

                        $output .= '</ul></span>';

                    } // Display Warnings


                    // Resolve link
                    $output .= '<br><a class="musicdistro-errorcheck-resolve" target="_BLANK" href="' . get_edit_post_link($arrangement) . '"><i class="fa fa-wrench"></i>&nbsp;&nbsp;Click To Resolve</a>';


                } // If Errors or Warnings found

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
