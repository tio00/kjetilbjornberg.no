<?php
/*
Template Name: Homepage style "Grid"

@package WordPress
@subpackage Mariana_Blog
@since Mariana Blog 1.0
*/

room_storage_set('blog_streampage', true);

get_header(); 

if (have_posts()) {

	if (!is_paged() && !in_array(room_get_theme_option('body_style'), array('fullwide', 'fullscreen'))) {
		the_post();
		get_template_part( 'content', 'excerpt' );
	}
	
	?><div class="columns_wrap"><?php
	
	while ( have_posts() ) { the_post(); 
		get_template_part( 'content', 'grid' );
	}
	
	?></div><?php


	get_template_part( 'templates/pagination' );

} else {

	if ( is_search() )
		get_template_part( 'content', 'none-search' );
	else
		get_template_part( 'content', 'none-archive' );

}

get_footer();
?>