<?php
/**
 * The template for displaying all single posts
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
		'next_text' => '<span class="meta-nav" aria-hidden="true">' . esc_html__( 'Next', 'room' ) . '</span> ' .
			'<span class="screen-reader-text">' . esc_html__( 'Next post:', 'room' ) . '</span> ' .
			'<h4 class="post-title">%title</h4>',
		'prev_text' => '<span class="meta-nav" aria-hidden="true">' . esc_html__( 'Previous', 'room' ) . '</span> ' .
			'<span class="screen-reader-text">' . esc_html__( 'Previous post:', 'room' ) . '</span> ' .
			'<h4 class="post-title">%title</h4>',
	) );

	// Related posts
	get_template_part('templates/post-related');

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) {
		comments_template();
	}
	
	get_template_part( 'templates/counter' );
}

get_footer();
?>