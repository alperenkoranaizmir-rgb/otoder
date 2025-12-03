<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<div class="topbar">
    <span><?php _e('arabam.com görünümünde ücretsiz otomobil ilanları', 'otoder'); ?></span>
    <span><?php _e('Hızlı kayıt · Ücretsiz ilan · Otomatik ithalat', 'otoder'); ?></span>
</div>
<header class="site-header">
    <div class="header-inner">
        <div class="site-logo">
            <span class="mark">A</span>
            <a href="<?php echo esc_url(home_url('/')); ?>">arabam style</a>
        </div>
        <nav class="primary-nav">
            <?php
            wp_nav_menu([
                'theme_location' => 'primary',
                'container' => false,
                'fallback_cb' => function() {
                    echo '<a href="' . esc_url(home_url('/ana-sayfa')) . '">' . __('Ana Sayfa', 'otoder') . '</a>';
                    echo '<a href="' . esc_url(home_url('/vitrin')) . '">' . __('Vitrin', 'otoder') . '</a>';
                    echo '<a href="' . esc_url(home_url('/ilan-gonder')) . '">' . __('İlan Ver', 'otoder') . '</a>';
                    echo '<a href="' . esc_url(home_url('/hesabim')) . '">' . __('Hesabım', 'otoder') . '</a>';
                }
            ]);
            ?>
        </nav>
        <div class="header-actions">
            <a class="button-link button outline" href="<?php echo esc_url(home_url('/hesabim')); ?>"><?php _e('Giriş Yap', 'otoder'); ?></a>
            <a class="button-link button primary" href="<?php echo esc_url(home_url('/ilan-gonder')); ?>"><?php _e('Ücretsiz İlan Ver', 'otoder'); ?></a>
        </div>
    </div>
</header>
<div class="container">
