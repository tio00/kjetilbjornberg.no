<?php
/**
 * The "News Magazine" template for displaying content
 *
 * Used for widget Recent News.
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.5
 */

$args = room_template_get_args('recent_news');
$style = $args['style'];
$number = $args['number'];
$count = $args['count'];
$post_format = get_post_format();
$post_format = empty($post_format) ? 'standard' : str_replace('post-format-', '', $post_format);
$animation = room_get_theme_option('blog_animation');

?><article
	<?php post_class( 'post_item post_layout_'.esc_attr($style)
					.' post_format_'.esc_attr($post_format)
					.' post_accented_off'
					.' post_accented_border'
					); ?>
	<?php echo (!room_param_is_off($animation) ? ' data-animation="'.esc_attr(room_get_animation_classes($animation)).'"' : ''); ?>
	>
	<div class="number"><?php echo esc_html($number); ?></div>
	<div class="post_content">
		<?php
		if ( !in_array($post_format, array('link', 'aside', 'status', 'quote')) ) {
			?>
			<div class="post_header entry-header">
				<?php
					the_title( '<h6 class="post_title entry-title"><a href="'.esc_url(get_permalink()).'" rel="bookmark">', '</a></h6>' );
				?>
			</div><!-- .entry-header -->
			<?php
		}
		if ( in_array( get_post_type(), array( 'post', 'attachment' ) ) ) {	?>
			<div class="post_footer entry-footer">
				<span class="post_date"><?php echo get_the_date(); ?></span>
			</div><!-- .entry-footer -->
		<?php
		}
		?>
	</div>
</article>