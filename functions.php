<?php
require_once( get_template_directory() . '/../../../wp-includes/ID3/getid3.php' );
add_theme_support( 'post-thumbnails' );

function getMedia($post) {

	// The media array
	$media = [];

	// Should we display the text?
	$notext = false;
	$tags = get_the_tags($post);
	if($tags) {
		foreach($tags as $tag):
			if($tag->name == "notext") { $notext = true; }
		endforeach;
	}

	// Get title and subtitle, when it should be displayed
	if($notext == false) {
		$title = get_the_title($post);
		$subtitle = strip_shortcodes(wp_trim_words( get_the_content($post), 80 )); 
	} else {
		$title = "";
		$subtitle = ""; 
	}

	// Look for videos in the post
	$post_content = $post->post_content;
	$start = strpos( $post_content, '<!-- wp:video' );
	if ($start !== false) {
		$start = strpos( $post_content, '<!-- wp:video' );
		$end = strpos( $post_content, '</figure>', $start );
		$video_block = substr( $post_content, $start, $end - $start + 8 );
		preg_match( '/id":(\d+)/', $video_block, $matches );
		$video_id = $matches[1];
		$video_path = get_attached_file( $video_id );
		$getID3 = new getID3;
		$video_info = $getID3->analyze( $video_path );
		$video_length = $video_info['playtime_seconds'];
		$video_url = wp_get_attachment_url( $video_id );

		array_push($media,
			array('type' => 'video',
				'url' => $video_url,
				'length' => $video_length * 1000,
				'mimetype' => $video_info['mime_type'],
				'title' => $title,
				'subtitle' => $subtitle,
				'uuid' => "video_".uniqid()));
	}

	//Add the featured image to the array
	$post_thumbnail_id = get_post_thumbnail_id($post);
	if($post_thumbnail_id) 
		array_push($media, 
			array('type' => 'image',
				'url' => wp_get_attachment_image_url($post_thumbnail_id, $size = 'full'),
				'title' => $title,
				'subtitle' => $subtitle,
				'uuid' => "image_".uniqid()));

	//Add all gallery images to the array
	if($gallery = get_post_gallery( get_the_ID($post), false )) {
		$gallery_ids = explode(",", $gallery['ids']);

		foreach($gallery_ids as $image_id) {
			array_push($media, 
				array('type' => 'image',
					'url' => wp_get_attachment_image_url($image_id, $size = 'full'),
					'title' => $title,
					'subtitle' => $subtitle,
					'uuid' => "image_".uniqid()));
		}
	}

	shuffle($media);
	return $media;
}

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

// Add a custom section to the theme options page
function iamklaus_fullscreenslidertheme_options_section() {
    add_settings_section( 'iamklaus_fullscreenslidertheme_options_section', 'IAMKLAUS Fullscreen Slider Theme Options', 'iamklaus_fullscreenslidertheme_options_section_callback', 'general' );
}
add_action( 'admin_init', 'iamklaus_fullscreenslidertheme_options_section' );

// Add a numeric field option to the custom section
function iamklaus_fullscreenslidertheme_options_field() {
    add_settings_field( 'iamklaus_fullscreenslidertheme_options_field', 'Slider Interval for Images in Milliseconds', 'iamklaus_fullscreenslidertheme_options_field_callback', 'general', 'iamklaus_fullscreenslidertheme_options_section' );
    register_setting( 'general', 'iamklaus_fullscreenslidertheme_options_field', 'intval' );
}
add_action( 'admin_init', 'iamklaus_fullscreenslidertheme_options_field' );

// Display the section description
function iamklaus_fullscreenslidertheme_options_section_callback() {
    echo '<p>Add custom theme options here.</p>';
}

// Display the numeric field option
function iamklaus_fullscreenslidertheme_options_field_callback() {
    $value = get_option( 'iamklaus_fullscreenslidertheme_options_field', 0 );
    echo '<input type="number" min="1000" step="1" name="iamklaus_fullscreenslidertheme_options_field" value="' . $value . '" />';
}

?>
