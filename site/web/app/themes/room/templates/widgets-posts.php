<?php
/**
 * The template for displaying posts in widget and/or in the search results
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */

$post_id    = get_the_ID();
$post_date  = sprintf( esc_html__('%s ago', 'room'), human_time_diff(get_the_time('U')) );
$post_title = get_the_title();
$post_link  = get_permalink();
$post_author_id   = get_the_author_meta('ID');
$post_author_name = get_the_author_meta('display_name');
$post_author_url  = get_author_posts_url($post_author_id, '');

$args = room_template_get_args('widgets-posts');
$show_date = isset($args['show_date']) ? (int) $args['show_date'] : 1;
$show_image = isset($args['show_image']) ? (int) $args['show_image'] : 1;
$show_author = isset($args['show_author']) ? (int) $args['show_author'] : 1;
$show_counters = isset($args['show_counters']) ? (int) $args['show_counters'] : 1;
$show_categories = isset($args['show_categories']) ? (int) $args['show_categories'] : 1;

$allow = false;

$output = room_storage_get('output');
$post_counters_output = '';

if ( $show_counters && ($counters_list = room_get_theme_setting('widget_counters')) !='' ) {
	$post_counters_link = strpos($counters_list, 'comments')!==false 
								? get_comments_link( $post_id ) 
								: (strpos($counters_list, 'likes')!==false
								    ? '#'
								    : $post_link
								    );
	if (strpos($counters_list, 'views')!==false) {
		$post_counters = room_get_post_views($post_id);
		$post_counters_icon = 'post_counters_views icon-view-new';
	} else if (strpos($counters_list, 'likes')!==false) {
		$likes = isset($_COOKIE['room_likes']) ? $_COOKIE['room_likes'] : '';
		$allow = strpos($likes, ','.($post_id).',')===false;
		$post_counters = room_get_post_likes($post_id);
		$post_counters_icon = 'post_counters_likes '.($allow ? 'icon-like-new enabled' : 'icon-heart disabled');
	} else {
		$post_counters = get_comments_number($post_id);
		$post_counters_icon = 'post_counters_comments icon-comment-new';
		$post_counters_link = get_comments_link($post_id);
	}
	$post_counters_output = '<span class="post_info_item post_info_counters">'
		. ($post_counters_link ? '<a href="' . esc_url($post_counters_link) . '"' : '<span') 
				. ' class="post_counters_item ' . esc_attr($post_counters_icon) . '"'
				. (strpos($counters_list, 'likes')!==false
					? ' title="'.($allow ? esc_attr__('Like', 'room') : esc_attr__('Dislike', 'room')).'"'
						. ' data-postid="' . esc_attr($post_id) . '"'
                        . ' data-likes="' . esc_attr($post_counters) . '"'
                        . ' data-title-like="' . esc_attr__('Like', 'room') . '"'
                        . ' data-title-dislike="' . esc_attr__('Dislike', 'room') . '"'
					: ''
				)
			. '>'
		. '<span class="post_counters_number">' . ($post_counters) . '</span>'
		. ($post_counters_link ? '</a>' : '</span>')
		. '</span>';
}


$output .= '<article class="post_item with_thumb">';

if ($show_image) {
	$post_thumb = get_the_post_thumbnail($post_id, room_get_thumb_size('tiny'), array(
		'alt' => get_the_title()
	));
	if ($post_thumb) $output .= '<div class="post_thumb">' . ($post_link ? '<a href="' . esc_url($post_link) . '">' : '') . ($post_thumb) . ($post_link ? '</a>' : '') . '</div>';
}

$output .= '<div class="post_content">'
			. ($show_categories ? '<div class="post_category">'.room_get_post_categories().trim($post_counters_output).'</div>' : '')
			. '<h6 class="post_title">' . ($post_link ? '<a href="' . esc_url($post_link) . '">' : '') . ($post_title) . ($post_link ? '</a>' : '') . '</h6>'
			. '<div class="post_info">'
				. ($show_date 
					? '<span class="post_info_item post_info_posted">'
						. ($post_link ? '<a href="' . esc_url($post_link) . '" class="post_info_date">' : '') 
						. ($post_date) 
						. ($post_link ? '</a>' : '')
						. '</span>'
					: '')
				. ($show_author 
					? '<span class="post_info_item post_info_posted_by">' 
						. esc_html__('by', 'room') . ' ' 
						. ($post_link ? '<a href="' . esc_url($post_author_url) . '" class="post_info_author">' : '') 
						. ($post_author_name) 
						. ($post_link ? '</a>' : '') 
						. '</span>'
					: '')
				. (!$show_categories && $post_counters_output
					? $post_counters_output
					: '')
			. '</div>'
		. '</div>'
	. '</article>';
room_storage_set('output', $output);
?>