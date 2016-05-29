<?php
/**
 * Template for displaying content of the 'About Me' page
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'post_item_single post_type_page post_style_about' ); ?>>

	<div class="post_featured">
		<?php the_post_thumbnail( room_get_thumb_size('full'), array( 'alt' => get_the_title() ) ); ?>
	</div>

	<div class="post_header entry-header">
		<?php the_title( '<h1 class="post_title entry-title">', '</h1>' ); ?>
	</div>

	<div class="post_content entry-content">
		<?php
			the_content( );

			wp_link_pages( array(
				'before'      => '<div class="page_links"><span class="page_links_title">' . esc_html__( 'Pages:', 'room' ) . '</span>',
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
				'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'room' ) . ' </span>%',
				'separator'   => '<span class="screen-reader-text">, </span>',
			) );

			room_show_contact_form(array(
				'title' => esc_html__('Contact Me', 'room')
			));
		?>
	</div><!-- .entry-content -->

</article>
