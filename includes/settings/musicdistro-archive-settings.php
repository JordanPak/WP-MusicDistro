<?php
/**
 * WP MusicDistro Archive Settings
 *
 * @copyright	Copyright (c) 2015, Jordan Pakrosnis
 * @since		2.0
 */



/**
 * WP MusicDistro Archive Settings: Get Defaults
 * 
 * @return      array with default options
 */
function musicdistro_archive_options_defaults() {

    $options[] = '';
    
    $options['band']                    =   'marching-knights';
    $options['show_header']             =   'false';
    $options['select_instrument_text']  =   'Get Music';
    $options['category_box_width']      =   'one-third';
    $options['single_part_dl_icon']     =   'arrow-down';
    
    return $options;
    
} // musicdistro_archive_options_set()




/**
 * WP MusicDistro Archive Settings: Set
 *
 * @param       array containing options
 * @return      array with new options
 */
function musicdistro_archive_options_set( $options ) {
    
} // musicdistro_archive_options_set()
