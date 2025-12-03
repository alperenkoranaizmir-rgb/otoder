<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
<article class="card">
    <h1><?php the_title(); ?></h1>
    <?php if (has_post_thumbnail()) { the_post_thumbnail('large'); } ?>
    <div class="price"><?php echo esc_html(get_post_meta(get_the_ID(), 'price', true)); ?> ₺</div>
    <?php otoder_render_listing_meta(get_the_ID()); ?>
    <div class="meta"><?php echo get_the_term_list(get_the_ID(), 'location', '', ', '); ?></div>
    <div class="listing-gallery">
        <?php $gallery = array_filter(array_map('trim', explode("\n", (string) get_post_meta(get_the_ID(), 'gallery', true))));
        foreach ($gallery as $image) {
            echo '<img src="' . esc_url($image) . '" alt="">';
        } ?>
    </div>
    <div class="content"><?php the_content(); ?></div>
    <?php if ($video = get_post_meta(get_the_ID(), 'video_url', true)) : ?>
        <div class="card">
            <h3><?php _e('Video', 'otoder'); ?></h3>
            <iframe width="100%" height="320" src="<?php echo esc_url($video); ?>" allowfullscreen></iframe>
        </div>
    <?php endif; ?>
</article>
<?php endwhile; ?>
<?php get_footer(); ?>
