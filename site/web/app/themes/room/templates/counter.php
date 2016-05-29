<?php
if (room_get_theme_setting('ajax_views_counter')) {
	?>
	<!-- Post/page views count increment -->
	<script type="text/javascript">
		jQuery(document).ready(function() {
			setTimeout(function() {
				jQuery.post(ROOM_STORAGE['ajax_url'], {
					action: 'post_counter',
					nonce: ROOM_STORAGE['ajax_nonce'],
					post_id: <?php echo (int) get_the_ID(); ?>,
					views: 1
				}).done(function(response) {
					var rez = {};
					try {
						rez = JSON.parse(response);
					} catch (e) {
						rez = { error: ROOM_STORAGE['ajax_error'] };
						console.log(response);
					}
					if (rez.error === '') {
						jQuery('.post_counters_single .post_counters_views .post_counters_number').html(rez.counter);
					}
				});
			}, 10);
		});
	</script>
	<?php
} else 
	room_inc_post_views(get_the_ID(), 1);
?>