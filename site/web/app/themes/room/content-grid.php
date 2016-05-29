<?php
/**
 * The Grid template for displaying content
 *
 * Used for index/archive/search.
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */
$blog_style = explode('_', room_get_theme_option('blog_style'));
$columns = empty($blog_style[1]) ? 2 : max(2, $blog_style[1]);
$post_format = get_post_format();
$post_format = empty($post_format) ? 'standard' : str_replace('post-format-', '', $post_format);
$animation = room_get_theme_option('blog_animation');

?><div class="column-1_<?php echo esc_attr($columns); ?>"><article id="post-<?php the_ID(); ?>" 
	<?php post_class( 'post_item post_layout_grid post_format_'.esc_attr($post_format) ); ?>
	<?php echo (!room_param_is_off($animation) ? ' data-animation="'.esc_attr(room_get_animation_classes($animation)).'"' : ''); ?>
	>

	<?php
	if ( is_sticky() && is_home() && !is_paged() ) {
		?><span class="post_label label_sticky"></span><?php
	}

	room_template_set_args('post_featured', array(
		'thumb_size' => room_get_thumb_size($columns > 2 ? 'small' : 'med')
	));
	get_template_part( 'templates/post-featured' );

	if ( !in_array($post_format, array('link', 'aside', 'status', 'quote')) ) {
		?>
		<div class="post_header entry-header">
			<div class="post_categories"><?php the_category( ' ' ); ?></div>
			<?php the_title( sprintf( '<h4 class="post_title entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h4>' ); ?>
		</div><!-- .entry-header -->
		<?php
	}
	?>

	<div class="post_content entry-content">
		<?php
		echo wpautop(room_strshort(get_the_excerpt(), max(1, room_get_theme_setting('max_excerpt_length')) * 3));
		?>
	</div><!-- .entry-content -->

	<div class="post_footer entry-footer">
		<?php get_template_part( 'templates/post-footer' ); ?>
	</div><!-- .entry-footer -->

</article></div>