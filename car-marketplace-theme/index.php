<?php get_header(); ?>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
    <article class="card">
        <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
        <div class="meta"><?php the_time(get_option('date_format')); ?></div>
        <div><?php the_excerpt(); ?></div>
    </article>
<?php endwhile; the_posts_pagination(); else : ?>
    <p><?php _e('İçerik bulunamadı.', 'otoder'); ?></p>
<?php endif; ?>
<?php get_footer(); ?>
