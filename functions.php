<?php

add_theme_support( 'post-thumbnails' );

/**
 * Retrieves the image ids from a gallery.
 *
 * @param int|WP_Post $post Post ID or object.
 * @return array            An array of image ids.
 */
function get_gallery_image_ids( $post ) {
	$post = get_post( $post );
	if ( ! $post ) {
		return array();
	}

	if ( ! has_shortcode( $post->post_content, 'gallery' ) ) {
		return array();
	}

	$images_ids = array();
	if ( preg_match_all( '/' . get_shortcode_regex() . '/s', $post->post_content, $matches, PREG_SET_ORDER ) ) {
		foreach ( $matches as $shortcode ) {
			if ( 'gallery' === $shortcode[2] ) {
				$data = shortcode_parse_atts( $shortcode[3] );
				if ( ! empty( $data['ids'] ) ) {
					$images_ids = explode( ',', $data['ids'] );
				}
			}
		}
	}

	return $images_ids;
}

// Our custom post type function
function create_posttype() {
  
    register_post_type( 'visitors',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Visitors' ),
                'singular_name' => __( 'Visitor' )
                ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'movies'),
            'show_in_rest' => true,
  
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );

?>
