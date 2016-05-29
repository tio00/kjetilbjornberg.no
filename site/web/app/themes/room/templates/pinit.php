<?php
if ( room_get_theme_setting('pinit_images') || (is_single() && !is_attachment())) {
	$img = wp_get_attachment_image_src(get_post_thumbnail_id(),'full');
	$link = 'https://pinterest.com/pin/create/button/?url='. urlencode(get_permalink(get_the_ID())).'&media='. urlencode($img[0]).'&description=' . urlencode(get_the_title());
	?><a href="<?php echo esc_url($link); ?>"
	     class="post_label label_pinit"
	><?php esc_html_e('Pin It', 'room'); ?></a><?php
}
?>