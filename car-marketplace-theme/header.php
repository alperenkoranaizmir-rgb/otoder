<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="site-header">
    <div class="site-logo"><a href="<?php echo esc_url(home_url('/')); ?>">Otoder</a></div>
    <nav class="primary-nav">
        <?php wp_nav_menu(['theme_location' => 'primary', 'container' => false]); ?>
    </nav>
</header>
<div class="container">
