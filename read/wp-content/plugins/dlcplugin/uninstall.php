<?php

/**
* Triggers plugin uninstall
*@package DLCPlugin
*/

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
     die;
}

// Clear DB stored data
$tests = get_posts( array( 'post_type' =>'dlcpost', 'numberposts' => -1 ) );

foreach ( $tests as $test) {
     wp_delete_post( $test->ID, true );
}

// Access DB via SQL
global $wpdb;
$wpdb->query( "DELETE FROM rd_posts WHERE post_type = 'dlcpost'" );
$wpdb->query( "DELETE FROM rd_postmeta WHERE post_id NOT IN (SELECT id FROM rd_posts)" );
$wpdb->query( "DELETE FROM rd_term_relationships WHERE object_id NOT IN (SELECT id FROM rd_posts)" );
