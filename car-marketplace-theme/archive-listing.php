<?php get_header(); ?>
<h1><?php post_type_archive_title(); ?></h1>
<?php echo do_shortcode('[otoder_search]'); ?>
<div class="card-grid">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article class="card">
        <?php if (has_post_thumbnail()) { the_post_thumbnail('medium'); } ?>
        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        <div class="price"><?php echo esc_html(get_post_meta(get_the_ID(), 'price', true)); ?> ₺</div>
        <div class="meta"><?php echo esc_html(get_post_meta(get_the_ID(), 'year', true)); ?> · <?php echo esc_html(get_post_meta(get_the_ID(), 'kilometer', true)); ?> km</div>
    </article>
<?php endwhile; the_posts_pagination(); else : ?>
    <div class="alert"><?php _e('İlan bulunamadı.', 'otoder'); ?></div>
<?php endif; ?>
</div>
<?php get_footer(); ?>
