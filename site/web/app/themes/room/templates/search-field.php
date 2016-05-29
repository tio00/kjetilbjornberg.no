<div class="search_wrap search_ajax search_state_closed">
	<div class="search_form_wrap">
		<form method="get" class="search_form" action="<?php echo esc_url(home_url('/')); ?>">
			<span class="label"><?php esc_attr_e('search', 'room'); ?></span>
			<input type="text" class="search_field" placeholder="<?php esc_attr_e('Enter keyword', 'room'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s">
			<button type="submit" class="search_submit icon-search" title="<?php esc_attr_e('Start search', 'room'); ?>"></button>
		</form>
	</div>
	<div class="search_results widget_area"><a class="search_results_close icon-cancel"></a><div class="search_results_content"></div></div>
</div>
