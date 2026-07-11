<?php
/**
 * الهيدر — يستبدل هيدر Astra الافتراضي بالكامل ويعرض القالب المطابق
 * لاختيار Shop Manager في حقل header_style.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header id="masthead" class="site-header ms-store-header" role="banner">
	<?php ms_render_store_part( 'header_style' ); ?>
</header><!-- #masthead -->

<div id="content" class="site-content">
