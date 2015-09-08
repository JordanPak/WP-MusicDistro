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
    $output .= '<form role="form"><button class="button button-red" type="submit" name="do-error-check">START CHECK</button></form>';


    // Check if Selected
    if ( $selected )
    {
        // Spacer
        $output .= '<br>';


        // Arrangements Query Args
        $arrangementSelection = array(
            'post_type'			=> 'download',
            'fields'            => 'ids',                       // This is so only the ID is returned instead of the WHOLE post object (Performance)
            'orderby'           => 'title',
            'order'             => 'ASC',
            'posts_per_page'    => -1
        );

        // ARRAY OF ALL ARRANGEMENTS
        $arrangements = new WP_Query( $arrangementSelection );

        // No Arrangements Found
        if( ($arrangements->have_posts()) == false )
            $output .=  '
                    <i class="fa fa-exclamation-triangle"></i> No arrangements found!
            ';


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


        $instrument_names = array(); // Array of instrument Names


        $output .= '<p><b>Instruments &amp; Bands Found:</b></p><ul>';

        foreach ($instrument_categories as $instrument_category) {
            $output .= '<li>' . $instrument_category->name . '</li>';
            $instrument_names[] = $instrument_category->name;
        }

        $output .= '</ul><hr>';


        //-- CYCLE THROUGH ARRANGEMENTS --//

        // Just the IDs of the arrangements
        foreach( $arrangements as $arrangement ) {


            // Get the arrangement post from the ID
            $object = get_post( $arrangement );


            // Arrangement Title
            $output .=  '<span class="musicdistro-arrangement-title">' . get_the_title( $arrangement ) . '</span>';


            //-- Get Files (Names & URLSs) For Current Arrangement --//
            $files = edd_get_download_files( $arrangement );
            

            $output .= '<br>Parts found for...<br><br><ul>';


            //-- CYCLE THROUGH FILES OF CURRENT ARRANGEMENT --//
            foreach( $files as $file ) {

                // Instrument Name (to cross-reference)
                $instrument_name = '';

                //-- Explode File Into Array of Strings --//
                $explosion = explode(" ", $file['name']);

                $output .= '<li>' . $explosion[0] . ' ' . $explosion[1] . ' ' . $explosion[2];


                // CHECK FOR TWO-WORD INSTRUMENT
                if( (is_numeric($explosion[1]) == FALSE) && ($explosion[1] != NULL) ) {
                    $instrument_name = $explosion[0] . ' ' . $explosion[1];
                }
                else {
                    $instrument_name = $explosion[0];
                }

                $match_found = array_search($instrument_name, $instrument_names);

                if ( $match_found == '' )
                    $output .= '(<i>Instrument Not Found!</i>)';

                // $output .= '<br>Match Found: ' . $match_found . '<br><br>';

                // $match_found = in_array($instrument_name, $true_instrument_names);
                // $output .= '&nbsp;&nbsp;&nbsp;&nbsp;Match Found: ' . print_r($match_found);

                $output .= '</li>';

            } // foreach file

            $output .= '</ul>';


            $output .= '<hr>';

        } // foreach: arrangements as arrangement






    } // if $selected

    return $output;

} // musicdistro_error_check();
