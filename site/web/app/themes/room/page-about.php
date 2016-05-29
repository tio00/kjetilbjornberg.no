<?php
/**
Template Name: Page style "About Me"

@package WordPress
@subpackage Mariana_Blog
@since Mariana Blog 1.0
*/

room_template_set_args('header_title', array(
		'show_page_title' => false
	));

get_header();

while ( have_posts() ) { the_post();

	get_template_part( 'content', 'page-about' );

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
	
	get_template_part( 'templates/counter' );
}

get_footer();
?>