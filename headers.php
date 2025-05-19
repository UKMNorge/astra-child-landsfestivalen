<?php
/**
 * The header for Astra Theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?><!DOCTYPE html>
<?php astra_html_before(); ?>
<html <?php language_attributes(); ?>>
<head>
<?php astra_head_top(); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
if ( apply_filters( 'astra_header_profile_gmpg_link', true ) ) {
	?>
	<!-- Social share meta tags -->
	<?php
		// Default OG values; customize or replace with dynamic logic
		$og_title       = get_bloginfo( 'name' );
		$og_description = get_bloginfo( 'description' );
		$og_image       = 'http://ukm.no/festivalen/files/2025/05/UKM_40_Bla.png';
	?>
	<meta property="og:title" content="<?php echo esc_attr( $og_title ); ?>" />
	<meta property="og:description" content="<?php echo esc_attr( $og_description ); ?>" />
	<meta property="og:image" content="<?php echo esc_url( $og_image ); ?>" />
	<meta property="og:type" content="website" />
	<meta name="twitter:card" content="summary_large_image" />
	<meta name="twitter:image" content="<?php echo esc_url( $og_image ); ?>" />
	<?php
} 
?>

<?php if ( is_page( 'festivalen' ) ) : ?>
    <link rel="canonical" href="<?php echo esc_url( home_url( '/festivalen/' ) ); ?>" />
    <meta property="og:url" content="<?php echo esc_url( home_url( '/festivalen/' ) ); ?>" />
<?php endif; ?>

<?php wp_head(); ?>
<?php astra_head_bottom(); ?>
</head>

<body <?php astra_schema_body(); ?> <?php body_class(); ?>>
<?php astra_body_top(); ?>
<?php wp_body_open(); ?>

<a
	class="skip-link screen-reader-text"
	href="#content"
	title="<?php echo esc_attr( astra_default_strings( 'string-header-skip-link', false ) ); ?>">
		<?php echo esc_html( astra_default_strings( 'string-header-skip-link', false ) ); ?>
</a>

<div
<?php
	echo wp_kses_post(
		astra_attr(
			'site',
			array(
				'id'    => 'page',
				'class' => 'hfeed site',
			)
		) 
	);
	?>
>
	<?php
	astra_header_before();

	astra_header();

	astra_header_after();

	astra_content_before();
	?>
	<div id="content" class="site-content">
		<div class="ast-container">
		<?php astra_content_top(); ?>
