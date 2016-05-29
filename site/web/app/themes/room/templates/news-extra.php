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
	.' post_accented_on'
	.' post_accented_border'
); ?>
<?php echo (!room_param_is_off($animation) ? ' data-animation="'.esc_attr(room_get_animation_classes($animation)).'"' : ''); ?>
	>

<?php
if ( is_sticky() && is_home() && !is_paged() ) {
	?><span class="post_label label_sticky"></span><?php
}

room_template_set_args('post_featured', array(
	//'post_info' => $number<=$featured || $featured==0 ? '<div class="post_info"><span class="post_categories">'.room_get_post_categories().'</span></div>' : '',
	'pin_it' => false,
	'thumb_size' => room_get_thumb_size('med')
));
get_template_part( 'templates/post-featured' );

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
		<?php
		if (!is_singular() || have_comments() || comments_open()) {
			$post_comments = get_comments_number();
			?>
			<a href="<?php echo esc_url(get_comments_link()); ?>" class="post_counters_comments icon-comment"><span class="post_counters_number"><?php echo trim($post_comments); ?></span></a>
		<?php
		}
		?>
	</div><!-- .entry-footer -->
<?php
}
?>

	</article>