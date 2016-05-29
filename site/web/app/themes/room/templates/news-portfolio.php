<?php
/**
 * The "Portfolio" template for displaying content
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
	<?php post_class( 'post_item post_layout_'.esc_attr($style).' post_format_'.esc_attr($post_format) ); ?>
	<?php echo (!room_param_is_off($animation) ? ' data-animation="'.esc_attr(room_get_animation_classes($animation)).'"' : ''); ?>
	>
	<?php
	if ( has_post_thumbnail() ){
		$image_attributes = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
		$thumb_image = $image_attributes[0];
	}
	else {
		$thumb_image = room_get_file_url('images/no_img.jpg');
	}
	if($number%2 == 1){ ?>
		<div class="post_featured" style="background-image: url('<?php echo esc_url($thumb_image); ?>')"></div>
	<?php }
	echo '<div class="post_info">'
		. ( in_array( get_post_type(), array( 'post', 'attachment' ) )
		? '<div class="post_meta">'
		. '<span class="post_date"><a href="'.esc_url(get_permalink()).'">'.get_the_date().'</a></span>'
		. '</div>'
		: '')
	. '<h5 class="post_title entry-title"><a href="'.esc_url(get_permalink()).'" rel="bookmark">'.get_the_title().'</a></h5>'
	. '<a class="link" href="'.esc_url(get_permalink()).'"><span>'.esc_html__('Read More','room').'</span></a>'
	. '</div>';
	if($number%2 != 1){ ?>
		<div class="post_featured" style="background-image: url('<?php echo esc_url($thumb_image); ?>')"></div>
	<?php } ?>
</article>