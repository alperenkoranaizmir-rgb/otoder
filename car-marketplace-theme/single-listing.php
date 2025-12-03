<?php get_header(); ?>
<?php while (have_posts()) : the_post(); ?>
<article class="card">
    <div class="section-title">
        <div>
            <p class="meta">#<?php echo get_the_ID(); ?> · <?php echo get_the_term_list(get_the_ID(), 'location', '', ', '); ?></p>
            <h1><?php the_title(); ?></h1>
        </div>
        <div>
            <div class="price"><?php echo esc_html(get_post_meta(get_the_ID(), 'price', true)); ?> ₺</div>
            <div class="listing-meta">
                <?php echo get_the_term_list(get_the_ID(), 'fuel', '<span class="badge">', '</span><span class="badge">', '</span>'); ?>
                <?php echo get_the_term_list(get_the_ID(), 'transmission', '<span class="badge">', '</span><span class="badge">', '</span>'); ?>
            </div>
        </div>
    </div>
    <?php if (has_post_thumbnail()) { the_post_thumbnail('large', ['class' => 'cover']); } ?>
    <div class="details-grid">
        <?php
        $fields = [
            'year' => __('Model Yılı', 'otoder'),
            'kilometer' => __('Kilometre', 'otoder'),
            'hp' => __('Beygir', 'otoder'),
            'gear' => __('Vites', 'otoder'),
            'drivetrain' => __('Çekiş', 'otoder'),
            'condition' => __('Durum', 'otoder'),
        ];
        foreach ($fields as $key => $label) {
            $value = get_post_meta(get_the_ID(), $key, true);
            if ($value) {
                echo '<div class="detail-tile"><span>' . esc_html($label) . '</span><strong>' . esc_html($value) . '</strong></div>';
            }
        }
        ?>
    </div>
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
