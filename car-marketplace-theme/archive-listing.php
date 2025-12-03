<?php get_header(); ?>
<div class="section-title">
    <h1><?php post_type_archive_title(); ?></h1>
    <span class="meta"><?php _e('arabam.com görünümünde listeleme', 'otoder'); ?></span>
</div>
<div class="filters">
    <aside class="sidebar">
        <h3><?php _e('Filtrele', 'otoder'); ?></h3>
        <?php echo do_shortcode('[otoder_search]'); ?>
    </aside>
    <div>
        <div class="grid-listings">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                <article class="card">
                    <?php if (has_post_thumbnail()) { the_post_thumbnail('medium', ['class' => 'cover']); } else { echo '<div class="badge">' . __('Görsel yok', 'otoder') . '</div>'; } ?>
                    <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                    <div class="price"><?php echo esc_html(get_post_meta(get_the_ID(), 'price', true)); ?> ₺</div>
                    <div class="meta"><?php echo esc_html(get_post_meta(get_the_ID(), 'year', true)); ?> · <?php echo esc_html(get_post_meta(get_the_ID(), 'kilometer', true)); ?> km</div>
                    <div class="tags">
                        <?php echo get_the_term_list(get_the_ID(), 'fuel', '<span class="badge">', '</span><span class="badge">', '</span>'); ?>
                        <?php echo get_the_term_list(get_the_ID(), 'location', '<span class="badge">', '</span><span class="badge">', '</span>'); ?>
                    </div>
                </article>
            <?php endwhile; else : ?>
                <div class="alert"><?php _e('İlan bulunamadı.', 'otoder'); ?></div>
            <?php endif; ?>
        </div>
        <div style="margin-top:20px;">
            <?php the_posts_pagination(); ?>
        </div>
    </div>
</div>
<?php get_footer(); ?>
