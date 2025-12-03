<?php get_header(); ?>
<section class="hero">
    <div class="hero-content">
        <div>
            <h1><?php _e('arabam.com görünümünde ücretsiz otomobil ilanları', 'otoder'); ?></h1>
            <p><?php _e('Sahibinden veya arabam tarzı vitrin, hızlı üyelik, güçlü filtreler ve otomatik ilan ithalatı tek pakette.', 'otoder'); ?></p>
            <div class="hero-badges">
                <span class="badge">🚗 <?php _e('Birebir arabam.com UI', 'otoder'); ?></span>
                <span class="badge">⚡ <?php _e('Ön yüz ilan girişi', 'otoder'); ?></span>
                <span class="badge">🤖 <?php _e('Saatlik otomatik toplayıcı', 'otoder'); ?></span>
            </div>
            <div class="stat-row">
                <?php $member_ids = get_users(['fields' => 'ID', 'role__not_in' => ['Administrator']]); ?>
                <div class="stat"><strong><?php echo wp_count_posts('listing')->publish; ?></strong><span><?php _e('Yayında ilan', 'otoder'); ?></span></div>
                <div class="stat"><strong><?php echo number_format_i18n(wp_count_terms('brand')); ?></strong><span><?php _e('Marka ve model', 'otoder'); ?></span></div>
                <div class="stat"><strong><?php echo number_format_i18n(is_array($member_ids) ? count($member_ids) : 0); ?></strong><span><?php _e('Ücretsiz üye', 'otoder'); ?></span></div>
            </div>
        </div>
        <div>
            <form class="search-box" method="get" action="<?php echo esc_url(get_post_type_archive_link('listing')); ?>">
                <div>
                    <label><?php _e('Marka', 'otoder'); ?></label>
                    <input type="text" name="brand" placeholder="BMW, Toyota">
                </div>
                <div>
                    <label><?php _e('Model', 'otoder'); ?></label>
                    <input type="text" name="model" placeholder="320i, Corolla">
                </div>
                <div>
                    <label><?php _e('Şehir', 'otoder'); ?></label>
                    <input type="text" name="location" placeholder="İstanbul">
                </div>
                <div>
                    <label><?php _e('Yakıt', 'otoder'); ?></label>
                    <select name="fuel">
                        <option value=""><?php _e('Hepsi', 'otoder'); ?></option>
                        <option value="Benzin">Benzin</option>
                        <option value="Dizel">Dizel</option>
                        <option value="Hibrit">Hibrit</option>
                        <option value="Elektrik">Elektrik</option>
                    </select>
                </div>
                <div>
                    <label><?php _e('Min Fiyat', 'otoder'); ?></label>
                    <input type="number" name="min_price" placeholder="150000">
                </div>
                <div>
                    <label><?php _e('Max Fiyat', 'otoder'); ?></label>
                    <input type="number" name="max_price" placeholder="2500000">
                </div>
                <div style="grid-column: 1 / -1; display:flex; gap:10px; flex-wrap:wrap;">
                    <button class="button primary" type="submit"><?php _e('İlan Bul', 'otoder'); ?></button>
                    <a class="button outline" href="<?php echo esc_url(home_url('/ilan-gonder')); ?>"><?php _e('Ücretsiz İlan Ver', 'otoder'); ?></a>
                </div>
            </form>
        </div>
    </div>
</section>

<div class="section-title">
    <h2><?php _e('Öne Çıkan İlanlar', 'otoder'); ?></h2>
    <a href="<?php echo esc_url(get_post_type_archive_link('listing')); ?>"><?php _e('Tüm ilanlar', 'otoder'); ?> →</a>
</div>
<div class="grid-listings">
    <?php
    $featured = new WP_Query([
        'post_type' => 'listing',
        'posts_per_page' => 8,
    ]);
    if ($featured->have_posts()) : while ($featured->have_posts()) : $featured->the_post(); ?>
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
    <?php endwhile; wp_reset_postdata(); else : ?>
        <div class="alert"><?php _e('Henüz ilan yok.', 'otoder'); ?></div>
    <?php endif; ?>
</div>

<div class="section-title">
    <h2><?php _e('Kategorilere göz at', 'otoder'); ?></h2>
</div>
<div class="card-grid">
    <?php
    $terms = get_terms([
        'taxonomy' => 'brand',
        'hide_empty' => false,
        'number' => 6,
    ]);
    if (!empty($terms) && !is_wp_error($terms)) {
        foreach ($terms as $term) {
            echo '<div class="card"><h3>' . esc_html($term->name) . '</h3><p class="meta">' . sprintf(__('%d ilan', 'otoder'), intval($term->count)) . '</p><a class="button ghost" href="' . esc_url(get_term_link($term)) . '">' . __('Listeyi aç', 'otoder') . '</a></div>';
        }
    }
    ?>
</div>

<div class="section-title">
    <h2><?php _e('İlan ver ve vitrine çık', 'otoder'); ?></h2>
</div>
<div class="card-grid">
    <div class="card">
        <h3><?php _e('Ön yüz formu', 'otoder'); ?></h3>
        <p class="meta"><?php _e('Eksiksiz marka/model, fiyat, kilometre alanları ve galeri desteği.', 'otoder'); ?></p>
        <?php echo do_shortcode('[otoder_submit]'); ?>
    </div>
    <div class="card">
        <h3><?php _e('Hesap ve ilan yönetimi', 'otoder'); ?></h3>
        <p class="meta"><?php _e('Kullanıcılar giriş yapıp ilanlarını görebilir, düzenleyebilir.', 'otoder'); ?></p>
        <?php echo do_shortcode('[otoder_account]'); ?>
    </div>
</div>
<?php get_footer(); ?>
