<?php
/**
Template Name: Homepage style "Excerpt"

@package WordPress
@subpackage Mariana_Blog
@since Mariana Blog 1.0
*/

room_storage_set('blog_streampage', true);

get_header(); 

if (have_posts()) {

	while ( have_posts() ) { the_post(); 
		get_template_part( 'content', 'excerpt' );
	}

	get_template_part( 'templates/pagination' );

} else {

	if ( is_search() )
		get_template_part( 'content', 'none-search' );
	else
		get_template_part( 'content', 'none-archive' );

}

get_footer();
?>