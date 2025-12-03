<?php
/**
 * Otoder Auto Marketplace theme functionality.
 */

add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    register_nav_menus([
        'primary' => __('Primary Menu', 'otoder'),
    ]);
});

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('otoder-style', get_stylesheet_uri(), [], '1.0.0');
});

/**
 * Register the listing post type.
 */
add_action('init', function () {
    register_post_type('listing', [
        'label' => __('Listings', 'otoder'),
        'public' => true,
        'has_archive' => true,
        'rewrite' => ['slug' => 'ilanlar'],
        'supports' => ['title', 'editor', 'thumbnail', 'author'],
        'menu_icon' => 'dashicons-car',
        'show_in_rest' => true,
    ]);

    $taxonomies = [
        'brand' => __('Brand', 'otoder'),
        'model' => __('Model', 'otoder'),
        'fuel' => __('Fuel', 'otoder'),
        'transmission' => __('Transmission', 'otoder'),
        'body_type' => __('Body Type', 'otoder'),
        'color' => __('Color', 'otoder'),
        'location' => __('Location', 'otoder'),
    ];

    foreach ($taxonomies as $taxonomy => $label) {
        register_taxonomy($taxonomy, 'listing', [
            'label' => $label,
            'rewrite' => ['slug' => $taxonomy],
            'hierarchical' => true,
            'show_in_rest' => true,
        ]);
    }
});

/**
 * Listing meta fields.
 */
function otoder_get_meta_fields() {
    return [
        'price' => __('Price', 'otoder'),
        'year' => __('Model Year', 'otoder'),
        'kilometer' => __('Kilometer', 'otoder'),
        'hp' => __('Horsepower', 'otoder'),
        'gear' => __('Gear', 'otoder'),
        'drivetrain' => __('Drivetrain', 'otoder'),
        'condition' => __('Condition', 'otoder'),
        'video_url' => __('Video URL', 'otoder'),
        'gallery' => __('Gallery (one URL per line)', 'otoder'),
    ];
}

add_action('init', function () {
    foreach (otoder_get_meta_fields() as $key => $label) {
        register_post_meta('listing', $key, [
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => function() { return current_user_can('edit_posts'); },
        ]);
    }
});

add_action('add_meta_boxes', function () {
    add_meta_box('otoder_meta', __('Listing Details', 'otoder'), function ($post) {
        wp_nonce_field('otoder_meta_save', 'otoder_meta_nonce');
        echo '<table class="form-table">';
        foreach (otoder_get_meta_fields() as $key => $label) {
            $value = get_post_meta($post->ID, $key, true);
            echo '<tr><th><label for="' . esc_attr($key) . '">' . esc_html($label) . '</label></th>';
            if ($key === 'gallery') {
                echo '<td><textarea style="width:100%" rows="4" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '">' . esc_textarea($value) . '</textarea></td>';
            } else {
                echo '<td><input style="width:100%" type="text" id="' . esc_attr($key) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '"></td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }, 'listing', 'normal', 'high');
});

add_action('save_post_listing', function ($post_id) {
    if (!isset($_POST['otoder_meta_nonce']) || !wp_verify_nonce($_POST['otoder_meta_nonce'], 'otoder_meta_save')) {
        return;
    }

    foreach (otoder_get_meta_fields() as $key => $label) {
        if (isset($_POST[$key])) {
            $value = $key === 'gallery' ? sanitize_textarea_field($_POST[$key]) : sanitize_text_field($_POST[$key]);
            update_post_meta($post_id, $key, $value);
        }
    }
});

/**
 * Front-end submission shortcode.
 */
function otoder_submission_shortcode() {
    if (!is_user_logged_in()) {
        return '<div class="alert">' . __('Lütfen giriş yapın veya kayıt olun.', 'otoder') . '</div>' . otoder_account_shortcode();
    }

    $message = '';
    if (!empty($_POST['otoder_submit'])) {
        check_admin_referer('otoder_submit_listing');

        $post_id = wp_insert_post([
            'post_title' => sanitize_text_field($_POST['listing_title'] ?? ''),
            'post_content' => wp_kses_post($_POST['listing_description'] ?? ''),
            'post_type' => 'listing',
            'post_status' => 'publish',
            'post_author' => get_current_user_id(),
        ]);

        if ($post_id && !is_wp_error($post_id)) {
            foreach (otoder_get_meta_fields() as $key => $label) {
                if (isset($_POST[$key])) {
                    $value = $key === 'gallery' ? sanitize_textarea_field($_POST[$key]) : sanitize_text_field($_POST[$key]);
                    update_post_meta($post_id, $key, $value);
                }
            }

            $taxonomies = ['brand', 'model', 'fuel', 'transmission', 'body_type', 'color', 'location'];
            foreach ($taxonomies as $taxonomy) {
                if (!empty($_POST[$taxonomy])) {
                    wp_set_post_terms($post_id, array_map('sanitize_text_field', (array) $_POST[$taxonomy]), $taxonomy);
                }
            }

            $message = '<div class="alert success">' . __('İlanınız yayınlandı.', 'otoder') . '</div>';
        } else {
            $message = '<div class="alert error">' . __('Bir hata oluştu, lütfen tekrar deneyin.', 'otoder') . '</div>';
        }
    }

    ob_start();
    echo $message;
    ?>
    <form class="submit-form" method="post">
        <?php wp_nonce_field('otoder_submit_listing'); ?>
        <label><?php _e('İlan Başlığı', 'otoder'); ?></label>
        <input type="text" name="listing_title" required>

        <label><?php _e('Açıklama', 'otoder'); ?></label>
        <textarea name="listing_description" rows="5" required></textarea>

        <label><?php _e('Fiyat', 'otoder'); ?></label>
        <input type="text" name="price" required>

        <label><?php _e('Model Yılı', 'otoder'); ?></label>
        <input type="number" name="year" required>

        <label><?php _e('Kilometre', 'otoder'); ?></label>
        <input type="number" name="kilometer" required>

        <label><?php _e('Marka', 'otoder'); ?></label>
        <input type="text" name="brand" placeholder="Toyota, BMW" required>

        <label><?php _e('Model', 'otoder'); ?></label>
        <input type="text" name="model" placeholder="Corolla, 320i" required>

        <label><?php _e('Yakıt', 'otoder'); ?></label>
        <select name="fuel">
            <option value="Benzin">Benzin</option>
            <option value="Dizel">Dizel</option>
            <option value="Hibrit">Hibrit</option>
            <option value="Elektrik">Elektrik</option>
        </select>

        <label><?php _e('Vites', 'otoder'); ?></label>
        <select name="transmission">
            <option value="Manuel">Manuel</option>
            <option value="Otomatik">Otomatik</option>
        </select>

        <label><?php _e('Kasa Tipi', 'otoder'); ?></label>
        <input type="text" name="body_type" placeholder="Sedan, Hatchback">

        <label><?php _e('Renk', 'otoder'); ?></label>
        <input type="text" name="color" placeholder="Beyaz, Siyah">

        <label><?php _e('Konum', 'otoder'); ?></label>
        <input type="text" name="location" placeholder="İstanbul">

        <label><?php _e('Video URL', 'otoder'); ?></label>
        <input type="url" name="video_url" placeholder="https://www.youtube.com/watch?v=...">

        <label><?php _e('Fotoğraf URL\'leri', 'otoder'); ?></label>
        <textarea name="gallery" rows="4" placeholder="Her satıra bir görsel linki"></textarea>

        <button class="button" type="submit" name="otoder_submit" value="1"><?php _e('İlanı Yayınla', 'otoder'); ?></button>
    </form>
    <?php
    return ob_get_clean();
}
add_shortcode('otoder_submit', 'otoder_submission_shortcode');

/**
 * Account shortcode for login/register.
 */
function otoder_account_shortcode() {
    $message = '';

    if (!empty($_POST['otoder_register'])) {
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $user_id = wp_create_user($username, $password, $email);
        if (is_wp_error($user_id)) {
            $message = '<div class="alert error">' . esc_html($user_id->get_error_message()) . '</div>';
        } else {
            $message = '<div class="alert success">' . __('Kayıt başarılı, şimdi giriş yapabilirsiniz.', 'otoder') . '</div>';
        }
    }

    if (!empty($_POST['otoder_login'])) {
        $creds = [
            'user_login' => sanitize_user($_POST['username']),
            'user_password' => $_POST['password'],
            'remember' => true,
        ];
        $user = wp_signon($creds, false);
        if (is_wp_error($user)) {
            $message = '<div class="alert error">' . esc_html($user->get_error_message()) . '</div>';
        } else {
            wp_redirect(home_url('/')); exit;
        }
    }

    ob_start();
    echo $message;

    if (!is_user_logged_in()) : ?>
        <div class="card-grid">
            <form class="submit-form" method="post">
                <h3><?php _e('Ücretsiz Kayıt', 'otoder'); ?></h3>
                <label><?php _e('Kullanıcı Adı', 'otoder'); ?></label>
                <input type="text" name="username" required>
                <label><?php _e('E-posta', 'otoder'); ?></label>
                <input type="email" name="email" required>
                <label><?php _e('Şifre', 'otoder'); ?></label>
                <input type="password" name="password" required>
                <button class="button" type="submit" name="otoder_register" value="1"><?php _e('Kaydol', 'otoder'); ?></button>
            </form>
            <form class="submit-form" method="post">
                <h3><?php _e('Giriş Yap', 'otoder'); ?></h3>
                <label><?php _e('Kullanıcı Adı', 'otoder'); ?></label>
                <input type="text" name="username" required>
                <label><?php _e('Şifre', 'otoder'); ?></label>
                <input type="password" name="password" required>
                <button class="button secondary" type="submit" name="otoder_login" value="1"><?php _e('Giriş', 'otoder'); ?></button>
            </form>
        </div>
    <?php else : ?>
        <div class="alert success"><?php printf(__('Hoş geldin %s! İlanlarını aşağıdan yönetebilirsin.', 'otoder'), esc_html(wp_get_current_user()->display_name)); ?></div>
        <?php echo do_shortcode('[otoder_dashboard]'); ?>
    <?php endif;

    return ob_get_clean();
}
add_shortcode('otoder_account', 'otoder_account_shortcode');

/**
 * User dashboard shortcode.
 */
function otoder_dashboard_shortcode() {
    if (!is_user_logged_in()) {
        return '<div class="alert">' . __('İlanlarını görmek için giriş yapmalısın.', 'otoder') . '</div>';
    }

    $query = new WP_Query([
        'post_type' => 'listing',
        'author' => get_current_user_id(),
        'posts_per_page' => -1,
    ]);

    ob_start();
    ?>
    <div class="card-grid">
        <?php if ($query->have_posts()) : while ($query->have_posts()) : $query->the_post(); ?>
            <div class="card">
                <?php if (has_post_thumbnail()) { the_post_thumbnail('medium'); } ?>
                <h3><?php the_title(); ?></h3>
                <div class="meta"><?php echo esc_html(get_post_meta(get_the_ID(), 'price', true)); ?> ₺</div>
                <a class="button secondary" href="<?php the_permalink(); ?>"><?php _e('İlana Git', 'otoder'); ?></a>
            </div>
        <?php endwhile; else : ?>
            <div class="alert"><?php _e('Henüz ilanınız yok.', 'otoder'); ?></div>
        <?php endif; wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('otoder_dashboard', 'otoder_dashboard_shortcode');

/**
 * Search shortcode with filters.
 */
function otoder_search_shortcode() {
    $taxonomies = ['brand', 'model', 'fuel', 'body_type', 'location'];
    $meta_query = [];
    $tax_query = ['relation' => 'AND'];

    if (!empty($_GET['min_price']) || !empty($_GET['max_price'])) {
        $meta_query[] = [
            'key' => 'price',
            'value' => [sanitize_text_field($_GET['min_price'] ?? 0), sanitize_text_field($_GET['max_price'] ?? 999999999)],
            'compare' => 'BETWEEN',
            'type' => 'NUMERIC'
        ];
    }

    foreach ($taxonomies as $taxonomy) {
        if (!empty($_GET[$taxonomy])) {
            $tax_query[] = [
                'taxonomy' => $taxonomy,
                'field' => 'name',
                'terms' => sanitize_text_field($_GET[$taxonomy]),
            ];
        }
    }

    $query_args = [
        'post_type' => 'listing',
        'posts_per_page' => 12,
        'meta_query' => $meta_query,
        'tax_query' => count($tax_query) > 1 ? $tax_query : [],
    ];

    $listings = new WP_Query($query_args);

    ob_start();
    ?>
    <form class="search-form" method="get">
        <label><?php _e('Marka', 'otoder'); ?></label>
        <input type="text" name="brand" value="<?php echo esc_attr($_GET['brand'] ?? ''); ?>">

        <label><?php _e('Model', 'otoder'); ?></label>
        <input type="text" name="model" value="<?php echo esc_attr($_GET['model'] ?? ''); ?>">

        <label><?php _e('Konum', 'otoder'); ?></label>
        <input type="text" name="location" value="<?php echo esc_attr($_GET['location'] ?? ''); ?>">

        <label><?php _e('Minimum Fiyat', 'otoder'); ?></label>
        <input type="number" name="min_price" value="<?php echo esc_attr($_GET['min_price'] ?? ''); ?>">

        <label><?php _e('Maksimum Fiyat', 'otoder'); ?></label>
        <input type="number" name="max_price" value="<?php echo esc_attr($_GET['max_price'] ?? ''); ?>">

        <button class="button" type="submit"><?php _e('Filtrele', 'otoder'); ?></button>
    </form>

    <div class="card-grid">
        <?php if ($listings->have_posts()) : while ($listings->have_posts()) : $listings->the_post(); ?>
            <article class="card">
                <?php if (has_post_thumbnail()) { the_post_thumbnail('medium'); } else { echo '<div class="badge">' . __('Görsel Yok', 'otoder') . '</div>'; } ?>
                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                <div class="price"><?php echo esc_html(get_post_meta(get_the_ID(), 'price', true)); ?> ₺</div>
                <div class="meta"><?php echo esc_html(get_post_meta(get_the_ID(), 'year', true)); ?> · <?php echo esc_html(get_post_meta(get_the_ID(), 'kilometer', true)); ?> km</div>
                <div class="listing-meta">
                    <?php echo get_the_term_list(get_the_ID(), 'fuel', '<span class="badge">', '</span><span class="badge">', '</span>'); ?>
                    <?php echo get_the_term_list(get_the_ID(), 'location', '<span class="badge">', '</span><span class="badge">', '</span>'); ?>
                </div>
            </article>
        <?php endwhile; else : ?>
            <div class="alert"><?php _e('Kriterlere uygun ilan bulunamadı.', 'otoder'); ?></div>
        <?php endif; wp_reset_postdata(); ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('otoder_search', 'otoder_search_shortcode');

/**
 * Demo content on activation.
 */
add_action('after_switch_theme', function () {
    if (get_option('otoder_demo_loaded')) {
        return;
    }

    $demo_user = username_exists('otoder-demo');
    if (!$demo_user) {
        $demo_user = wp_create_user('otoder-demo', wp_generate_password(), 'demo@example.com');
    }

    $demo_posts = [
        [
            'title' => '2022 BMW 320i Sport Line',
            'price' => '2350000',
            'year' => '2022',
            'kilometer' => '18500',
            'fuel' => 'Benzin',
            'transmission' => 'Otomatik',
            'brand' => 'BMW',
            'model' => '320i',
            'location' => 'İstanbul',
            'body_type' => 'Sedan',
            'color' => 'Gri',
        ],
        [
            'title' => '2019 Toyota Corolla Dream',
            'price' => '890000',
            'year' => '2019',
            'kilometer' => '68500',
            'fuel' => 'Benzin',
            'transmission' => 'Otomatik',
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'location' => 'Ankara',
            'body_type' => 'Sedan',
            'color' => 'Beyaz',
        ],
    ];

    foreach ($demo_posts as $demo) {
        $post_id = wp_insert_post([
            'post_title' => $demo['title'],
            'post_type' => 'listing',
            'post_status' => 'publish',
            'post_author' => $demo_user,
            'post_content' => __('Detaylı açıklama ve servis kayıtları eklenmiştir.', 'otoder'),
        ]);

        if ($post_id && !is_wp_error($post_id)) {
            foreach (['price', 'year', 'kilometer'] as $meta_key) {
                update_post_meta($post_id, $meta_key, $demo[$meta_key]);
            }
            wp_set_post_terms($post_id, [$demo['brand']], 'brand');
            wp_set_post_terms($post_id, [$demo['model']], 'model');
            wp_set_post_terms($post_id, [$demo['fuel']], 'fuel');
            wp_set_post_terms($post_id, [$demo['transmission']], 'transmission');
            wp_set_post_terms($post_id, [$demo['body_type']], 'body_type');
            wp_set_post_terms($post_id, [$demo['color']], 'color');
            wp_set_post_terms($post_id, [$demo['location']], 'location');
        }
    }

    update_option('otoder_demo_loaded', 1);
});

/**
 * Template helpers.
 */
function otoder_render_listing_meta($post_id) {
    $fields = otoder_get_meta_fields();
    echo '<div class="listing-meta">';
    foreach (['price' => '₺', 'year' => '', 'kilometer' => ' km', 'hp' => ' hp', 'gear' => '', 'drivetrain' => '', 'condition' => ''] as $key => $suffix) {
        $value = get_post_meta($post_id, $key, true);
        if ($value) {
            echo '<span class="badge">' . esc_html($fields[$key] ?? ucfirst($key)) . ': ' . esc_html($value . $suffix) . '</span>';
        }
    }
    echo '</div>';
}
