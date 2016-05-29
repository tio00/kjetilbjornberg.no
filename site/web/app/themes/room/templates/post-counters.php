<?php
$args = room_template_get_args('post_counters');
$counters = $args['counters'];
?>
<div class="post_counters<?php echo !empty($args['class']) ? ' '.esc_attr($args['class']) : ''; ?>"><?php

	$post_id = get_the_ID();
	
	// Comments
	if (strpos($counters, 'comments')!==false && (!is_singular() || have_comments() || comments_open())) {
		$post_comments = get_comments_number();
		?>
		<a href="<?php echo esc_url(get_comments_link()); ?>" class="post_counters_item post_counters_comments icon-comment-new"><span class="post_counters_number"><?php echo trim($post_comments); ?></span><span class="post_counters_label"><?php esc_html_e('Comments', 'room'); ?></span></a>
		<?php
	}

	// Views
	if (strpos($counters, 'views')!==false) {
		$post_views = room_get_post_views($post_id)+1;
		?>
		<a href="<?php echo esc_url(get_permalink()); ?>" class="post_counters_item post_counters_views icon-view-new"><span class="post_counters_number"><?php echo trim($post_views); ?></span><span class="post_counters_label"><?php esc_html_e('Views', 'room'); ?></span></a>
		<?php
	}
	
	// Likes
	if (strpos($counters, 'likes')!==false) {
		$post_likes = room_get_post_likes($post_id);
		$likes = isset($_COOKIE['room_likes']) ? $_COOKIE['room_likes'] : '';
		$allow = strpos($likes, ','.($post_id).',')===false;
		?>
		<a class="post_counters_item post_counters_likes icon-like-new <?php echo !empty($allow) ? 'enabled' : 'disabled'; ?>" title="<?php echo !empty($allow) ? esc_attr__('Like', 'room') : esc_attr__('Dislike', 'room'); ?>" href="#"
			data-postid="<?php echo esc_attr(get_the_ID()); ?>"
			data-likes="<?php echo esc_attr($post_likes); ?>"
			data-title-like="<?php esc_attr_e('Like', 'room'); ?>"
			data-title-dislike="<?php esc_attr_e('Dislike', 'room'); ?>"><span class="post_counters_number"><?php echo trim($post_likes); ?></span><span class="post_counters_label"><?php esc_html_e('Likes', 'room'); ?></span></a>
		<?php
	}
	
	// Socials share
	if ( !empty($args['share']) ) {
		$output = room_get_share_links(array(
				'type' => 'drop',
				'caption' => esc_html__('Share', 'room'),
				'echo' => false
			));
		if ($output) {
			?><div class="post_counters_item post_share"><?php echo trim($output); ?></div><?php
		}
	}

	// Edit page link
	edit_post_link( esc_html__( 'Edit', 'room' ), '<span class="post_counters_item post_counters_edit icon-pencil">', '</span>' );

?></div><?php
