<?php
/**
 * The template for displaying image attachments
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */


get_header();

while ( have_posts() ) { the_post();

	get_template_part( 'content', get_post_format() );

	// Previous/next post navigation.
	the_post_navigation( array(
		'prev_text' => '<span class="meta-nav" aria-hidden="true">' . esc_html__( 'Published in', 'room' ) . '</span> ' .
			'<span class="screen-reader-text">' . esc_html__( 'Published in the post:', 'room' ) . '</span> ' .
			'<h4 class="post-title">%title</h4>',
	) );

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
	
	get_template_part( 'templates/counter' );
}

get_footer();
?>