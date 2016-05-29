<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Mariana_Blog
 * @since Mariana Blog 1.0
 */
if ( substr(room_get_theme_option('blog_style'), 0, 4) == 'grid' )
	get_template_part( 'index', 'grid' );
else
	get_template_part( 'index', 'excerpt' );
?>