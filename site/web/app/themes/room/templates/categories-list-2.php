<?php
$args = room_template_get_args('categories_list');
$cat_img = $args['image'];
$columns = $args['columns'];
if ($columns > 1) {
	?><div class="column-1_<?php echo esc_attr($columns); ?>"><?php
}
?>
<div class="categories_list_item">
	<div class="categories_list_image">
		<img src="<?php echo esc_url(empty($cat_img) 
								? room_get_file_url('images/no_img.jpg') 
								: room_add_thumb_sizes($cat_img, room_get_thumb_size($columns > 3 ? 'avatar' : 'med'))
								); ?>" alt="">
	</div>
	<h5 class="categories_list_title"><span class="categories_list_label"><?php echo esc_html($args['cat']->name); ?></span><?php
		if ($args['show_posts']) { 
			?><span class="categories_list_count">(<?php echo esc_html($args['cat']->count); ?>)</span><?php
		}
	?></h5>
</div>
<?php
if ($columns > 1) {
	?></div><?php
}
?>