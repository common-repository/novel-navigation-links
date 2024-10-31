<?php
/*
Plugin Name: Novel Navigation Links
Plugin URI: https://wordpress.org/plugins/novel-navigation-links
Description: Adds associated navigation links to bottom and top of content if there is a page or post with similar slug.
Author: Mehmet Ali İLGAR
Version: 0.1.1
Author URI: https://www.milgar.net/
Creation time: 23.09.2016
Text Domain: novel-navigation-links
Domain Path: /languages
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );


function nnl_novel_nav( $content ) {
	if ( is_page() || is_single() ) {
		load_plugin_textdomain( 'novel-navigation-links', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );

		global $post;

		$prev_link = nnl_get_chapter_permalink_in_direction( $post->post_name, $post->post_type, -1 );
		$next_link = nnl_get_chapter_permalink_in_direction( $post->post_name, $post->post_type, +1 );

		$nav_links = "<div class='nnl_container'>";
		if ( $prev_link ) {
			$nav_links = "$nav_links<a class='nnl_previous_chapter' href='$prev_link'>".esc_html__( 'Previous Chapter', 'novel-navigation-links' )."</a>";
		}
		if ( $next_link ) {
			$nav_links = "$nav_links<a class='nnl_next_chapter' href='$next_link'>".esc_html__( 'Next Chapter', 'novel-navigation-links' )."</a>";
		}


		$nav_links = "$nav_links</div>";

		return "$nav_links$content$nav_links";
	}

	return $content;
}

add_filter( 'the_content', 'nnl_novel_nav' );

function nnl_get_permalink_from_slug( $slug, $post_type ) {
	$args = array(
	  'name'        => $slug,
	  'post_type'   => $post_type,
	  'post_status' => 'publish',
	  'numberposts' => 1
	);
	$my_posts = get_posts($args);
	if( $my_posts )
		return get_permalink($my_posts[0]->ID);
	return NULL;
}

function nnl_get_chapter_permalink_in_direction( $slug, $post_type, $dir ) {
	$slug_pieces = explode( '-', $slug );
	$chapter_number = array_pop( $slug_pieces );

	if( is_numeric( $chapter_number ) )
		$chapter_number = (int)$chapter_number + $dir;
	else
		return NULL;

	$prev_slug = implode( '-',$slug_pieces )."-$chapter_number";
	return nnl_get_permalink_from_slug( $prev_slug, $post_type );
}

add_action('wp_head','nnl_hook_css');

function nnl_hook_css() {
	if ( is_page() || is_single() ) {
		$previous_float = is_rtl() ? 'right' : 'left';
		$next_float = is_rtl() ? 'left' : 'right';

		echo "
		<style type='text/css'>
		.nnl_previous_chapter {
			margin-$previous_float: 20px;
			float: $previous_float;
		}
		.nnl_next_chapter {
			margin-$next_float: 20px;
			float: $next_float;
		}
		.nnl_container {
			overflow: auto;
			margin: 20px auto;
		}
		</style>
		";
	}
}

?>
