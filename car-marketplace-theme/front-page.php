<?php get_header(); ?>
<section class="hero">
    <div>
        <h1><?php _e('Ücretsiz Otomobil İlan Sistemi', 'otoder'); ?></h1>
        <p><?php _e('arabam.com ve sahibinden.com tarzında tamamen yönetilebilir ve modern ilan teması.', 'otoder'); ?></p>
    </div>
    <div class="listing-meta">
        <span class="badge"><?php _e('Ücretsiz Üyelik', 'otoder'); ?></span>
        <span class="badge"><?php _e('Demo İlanlar Hazır', 'otoder'); ?></span>
        <span class="badge"><?php _e('Ön Yüzden İlan Gönderme', 'otoder'); ?></span>
    </div>
</section>

<h2><?php _e('Öne Çıkan İlanlar', 'otoder'); ?></h2>
<div class="card-grid">
    <?php
    $featured = new WP_Query([
        'post_type' => 'listing',
        'posts_per_page' => 6,
    ]);
    if ($featured->have_posts()) : while ($featured->have_posts()) : $featured->the_post(); ?>
        <article class="card">
            <?php if (has_post_thumbnail()) { the_post_thumbnail('medium'); } else { echo '<div class="badge">' . __('Görsel yok', 'otoder') . '</div>'; } ?>
            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <div class="price"><?php echo esc_html(get_post_meta(get_the_ID(), 'price', true)); ?> ₺</div>
            <div class="meta"><?php echo esc_html(get_post_meta(get_the_ID(), 'year', true)); ?> · <?php echo esc_html(get_post_meta(get_the_ID(), 'kilometer', true)); ?> km</div>
        </article>
    <?php endwhile; else : ?>
        <p><?php _e('Henüz ilan yok.', 'otoder'); ?></p>
    <?php endif; wp_reset_postdata(); ?>
</div>

<h2><?php _e('Hızlı Arama', 'otoder'); ?></h2>
<?php echo do_shortcode('[otoder_search]'); ?>

<h2><?php _e('İlan Ver', 'otoder'); ?></h2>
<?php echo do_shortcode('[otoder_submit]'); ?>
<?php get_footer(); ?>
