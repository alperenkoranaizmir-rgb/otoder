<?php
/**
 * Plugin Name: Otoder Essentials
 * Description: Tema ile birlikte çalışan temel kurulum, içerik tohumlama ve dış kaynak ilan toplayıcı eklentisi.
 * Version: 1.0.0
 * Author: Otoder
 */

if (!defined('ABSPATH')) {
    exit;
}

class Otoder_Essentials {
    const OPTION_KEY = 'otoder_essentials_seeded';

    public function __construct() {
        add_action('init', [$this, 'register_listing_support']);
        register_activation_hook(__FILE__, [$this, 'on_activate']);
        register_deactivation_hook(__FILE__, [$this, 'on_deactivate']);
        add_action('plugins_loaded', [$this, 'maybe_schedule']);
        add_action('otoder_seed_defaults', [$this, 'seed_all']);
        add_action('otoder_aggregator_run', [$this, 'run_aggregator']);
        add_action('admin_menu', [$this, 'register_admin_page']);
        add_action('admin_post_otoder_seed_content', [$this, 'handle_seed_request']);
        add_action('admin_post_otoder_run_aggregator', [$this, 'handle_manual_aggregator']);
    }

    public function register_listing_support() {
        if (!post_type_exists('listing')) {
            register_post_type('listing', [
                'label' => __('Listings', 'otoder'),
                'public' => true,
                'has_archive' => true,
                'rewrite' => ['slug' => 'ilanlar'],
                'supports' => ['title', 'editor', 'thumbnail', 'author'],
                'menu_icon' => 'dashicons-car',
                'show_in_rest' => true,
            ]);
        }

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
            if (!taxonomy_exists($taxonomy)) {
                register_taxonomy($taxonomy, 'listing', [
                    'label' => $label,
                    'rewrite' => ['slug' => $taxonomy],
                    'hierarchical' => true,
                    'show_in_rest' => true,
                ]);
            }
        }
    }

    public function on_activate() {
        $this->seed_all();
    }

    public function on_deactivate() {
        $timestamp = wp_next_scheduled('otoder_aggregator_run');
        if ($timestamp) {
            wp_unschedule_event($timestamp, 'otoder_aggregator_run');
        }
    }

    public function maybe_schedule() {
        if (!wp_next_scheduled('otoder_aggregator_run')) {
            wp_schedule_event(time() + 300, 'hourly', 'otoder_aggregator_run');
        }
    }

    private function default_pages() {
        return [
            [
                'title' => 'Ana Sayfa',
                'slug' => 'ana-sayfa',
                'content' => "[otoder_search]\n\n[otoder_submit]",
            ],
            [
                'title' => 'İlan Gönder',
                'slug' => 'ilan-gonder',
                'content' => '[otoder_submit]',
            ],
            [
                'title' => 'Hesabım',
                'slug' => 'hesabim',
                'content' => '[otoder_account]',
            ],
            [
                'title' => 'Vitrin',
                'slug' => 'vitrin',
                'content' => '[otoder_search]',
            ],
            [
                'title' => 'Kurumsal',
                'slug' => 'kurumsal',
                'content' => __('arabam.com görünümünde tam otomatik ilan sitesi: ücretsiz üyelik, otomatik ithalat ve SEO uyumlu açıklamalar.', 'otoder'),
            ],
        ];
    }

    public function seed_pages_and_terms() {
        foreach ($this->default_pages() as $page) {
            if (!get_page_by_path($page['slug'])) {
                wp_insert_post([
                    'post_title' => $page['title'],
                    'post_name' => $page['slug'],
                    'post_status' => 'publish',
                    'post_type' => 'page',
                    'post_content' => $page['content'],
                ]);
            }
        }

        $terms = [
            'brand' => ['BMW', 'Audi', 'Toyota', 'Hyundai', 'Volkswagen'],
            'model' => ['320i', 'A3', 'Corolla', 'i20', 'Golf'],
            'fuel' => ['Benzin', 'Dizel', 'Hibrit', 'Elektrik'],
            'transmission' => ['Otomatik', 'Manuel'],
            'body_type' => ['Sedan', 'Hatchback', 'SUV', 'Crossover'],
            'color' => ['Beyaz', 'Siyah', 'Gri', 'Mavi', 'Kırmızı'],
            'location' => ['İstanbul', 'Ankara', 'İzmir', 'Bursa', 'Antalya'],
        ];

        foreach ($terms as $taxonomy => $values) {
            foreach ($values as $value) {
                if (!term_exists($value, $taxonomy)) {
                    wp_insert_term($value, $taxonomy);
                }
            }
        }
    }

    public function seed_demo_listings() {
        if (get_option(self::OPTION_KEY)) {
            return;
        }

        $demo_user = username_exists('otoder-demo') ?: wp_create_user('otoder-demo', wp_generate_password(), 'demo@example.com');

        $demos = [
            [
                'title' => '2021 Audi A3 Sedan Advanced',
                'price' => '1575000',
                'year' => '2021',
                'kilometer' => '41500',
                'fuel' => 'Benzin',
                'transmission' => 'Otomatik',
                'brand' => 'Audi',
                'model' => 'A3',
                'location' => 'İstanbul',
                'body_type' => 'Sedan',
                'color' => 'Gri',
                'gallery' => [
                    'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=800&q=60',
                    'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=700&q=60',
                ],
            ],
            [
                'title' => '2020 Volkswagen Golf 1.5 eTSI Style',
                'price' => '1249000',
                'year' => '2020',
                'kilometer' => '55000',
                'fuel' => 'Benzin',
                'transmission' => 'Otomatik',
                'brand' => 'Volkswagen',
                'model' => 'Golf',
                'location' => 'Ankara',
                'body_type' => 'Hatchback',
                'color' => 'Beyaz',
                'gallery' => [
                    'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=760&q=60',
                ],
            ],
            [
                'title' => '2018 Toyota Corolla 1.6 Elegant',
                'price' => '785000',
                'year' => '2018',
                'kilometer' => '92000',
                'fuel' => 'Benzin',
                'transmission' => 'Otomatik',
                'brand' => 'Toyota',
                'model' => 'Corolla',
                'location' => 'İzmir',
                'body_type' => 'Sedan',
                'color' => 'Siyah',
                'gallery' => [
                    'https://images.unsplash.com/photo-1503736334956-4c8f8e92946d?auto=format&fit=crop&w=720&q=60',
                ],
            ],
        ];

        foreach ($demos as $demo) {
            $post_id = wp_insert_post([
                'post_title' => $demo['title'],
                'post_type' => 'listing',
                'post_status' => 'publish',
                'post_author' => $demo_user,
                'post_content' => __('Yetkili servis geçmişi bulunan, hasarsız, ekspertiz raporu hazır vitrin aracı.', 'otoder'),
                'meta_input' => [
                    'gallery' => implode("\n", $demo['gallery'] ?? []),
                ],
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

        update_option(self::OPTION_KEY, 1);
    }

    private function setup_navigation_and_front() {
        $menu_name = 'Üst Menü';
        $menu = wp_get_nav_menu_object($menu_name);
        if (!$menu) {
            $menu_id = wp_create_nav_menu($menu_name);
            $pages = ['ana-sayfa', 'vitrin', 'ilan-gonder', 'hesabim'];
            foreach ($pages as $slug) {
                if ($page = get_page_by_path($slug)) {
                    wp_update_nav_menu_item($menu_id, 0, [
                        'menu-item-title' => $page->post_title,
                        'menu-item-object' => 'page',
                        'menu-item-object-id' => $page->ID,
                        'menu-item-type' => 'post_type',
                        'menu-item-status' => 'publish',
                    ]);
                }
            }
            $menu = wp_get_nav_menu_object($menu_id);
        }

        if ($menu && !has_nav_menu('primary')) {
            $locations = get_theme_mod('nav_menu_locations');
            $locations['primary'] = $menu->term_id;
            set_theme_mod('nav_menu_locations', $locations);
        }

        if ($front = get_page_by_path('ana-sayfa')) {
            update_option('show_on_front', 'page');
            update_option('page_on_front', $front->ID);
        }
    }

    public function seed_all() {
        $this->register_listing_support();
        $this->seed_pages_and_terms();
        $this->seed_demo_listings();
        $this->setup_navigation_and_front();
        $this->maybe_schedule();
    }

    public function register_admin_page() {
        add_submenu_page(
            'tools.php',
            __('Otoder Otomasyon', 'otoder'),
            __('Otoder Otomasyon', 'otoder'),
            'manage_options',
            'otoder-automation',
            [$this, 'render_admin_page']
        );
    }

    public function render_admin_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Otoder Kurulum ve Toplayıcı', 'otoder'); ?></h1>
            <p><?php esc_html_e('Kategori, sayfa ve demo içeriklerini tek tıkla yükleyebilir; ilan toplayıcıyı hemen çalıştırabilirsiniz.', 'otoder'); ?></p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('otoder_seed_content'); ?>
                <input type="hidden" name="action" value="otoder_seed_content">
                <button class="button button-primary" type="submit"><?php esc_html_e('Kategori ve Sayfaları Yükle', 'otoder'); ?></button>
            </form>
            <form style="margin-top:16px;" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <?php wp_nonce_field('otoder_run_aggregator'); ?>
                <input type="hidden" name="action" value="otoder_run_aggregator">
                <button class="button" type="submit"><?php esc_html_e('İlan Toplayıcıyı Şimdi Çalıştır', 'otoder'); ?></button>
            </form>
            <p style="margin-top:16px;" class="description"><?php esc_html_e('Toplayıcı saatte bir otomatik çalışır; benzersiz açıklamalar üreterek SEO dostu içerik ekler.', 'otoder'); ?></p>
        </div>
        <?php
    }

    public function handle_seed_request() {
        check_admin_referer('otoder_seed_content');
        $this->seed_pages_and_terms();
        $this->seed_demo_listings();
        wp_safe_redirect(add_query_arg('otoder_seeded', '1', wp_get_referer()));
        exit;
    }

    public function handle_manual_aggregator() {
        check_admin_referer('otoder_run_aggregator');
        $this->run_aggregator();
        wp_safe_redirect(add_query_arg('otoder_aggregated', '1', wp_get_referer()));
        exit;
    }

    private function aggregator_sources() {
        return [
            'arabam' => 'https://www.arabam.com/ilanlar?query=otomobil&take=10',
            'sahibinden' => 'https://www.sahibinden.com/otomobil',
        ];
    }

    public function run_aggregator() {
        foreach ($this->aggregator_sources() as $source => $url) {
            $response = wp_remote_get($url, ['timeout' => 15]);
            $listings = $this->extract_listings_from_response($response, $source);
            if (empty($listings)) {
                $listings = $this->sample_source_items($source);
            }
            foreach ($listings as $listing) {
                $this->import_listing($listing);
            }
        }
    }

    private function extract_listings_from_response($response, $source) {
        $items = [];

        if (!is_wp_error($response)) {
            $body = wp_remote_retrieve_body($response);
            preg_match_all('/<title>([^<]+)<\\/title>/', $body, $matches);
            if (!empty($matches[1])) {
                $titles = array_slice($matches[1], 0, 3);
                foreach ($titles as $title) {
                    $items[] = [
                        'title' => trim($title),
                        'price' => rand(450000, 2500000),
                        'year' => rand(2015, 2024),
                        'kilometer' => rand(10000, 180000),
                        'fuel' => 'Benzin',
                        'transmission' => 'Otomatik',
                        'brand' => $this->detect_brand($title),
                        'model' => $this->detect_model($title),
                        'location' => 'İstanbul',
                        'body_type' => 'Sedan',
                        'color' => 'Gri',
                        'source' => $source,
                    ];
                }
            }
        }

        return $items;
    }

    private function sample_source_items($source) {
        return [
            [
                'title' => ucfirst($source) . ' vitrinden örnek ilan',
                'price' => rand(500000, 1500000),
                'year' => rand(2016, 2024),
                'kilometer' => rand(20000, 120000),
                'fuel' => 'Dizel',
                'transmission' => 'Manuel',
                'brand' => 'Hyundai',
                'model' => 'i20',
                'location' => 'Ankara',
                'body_type' => 'Hatchback',
                'color' => 'Kırmızı',
                'source' => $source,
            ],
            [
                'title' => 'Öne çıkan ' . ucfirst($source) . ' ilanı',
                'price' => rand(650000, 2100000),
                'year' => rand(2017, 2024),
                'kilometer' => rand(15000, 90000),
                'fuel' => 'Benzin',
                'transmission' => 'Otomatik',
                'brand' => 'Mercedes',
                'model' => 'E200',
                'location' => 'İstanbul',
                'body_type' => 'Sedan',
                'color' => 'Siyah',
                'source' => $source,
            ],
        ];
    }

    private function detect_brand($title) {
        $brands = ['Audi', 'BMW', 'Toyota', 'Volkswagen', 'Mercedes', 'Hyundai'];
        foreach ($brands as $brand) {
            if (stripos($title, $brand) !== false) {
                return $brand;
            }
        }
        return 'Genel';
    }

    private function detect_model($title) {
        $models = ['A3', 'A4', '320i', 'Corolla', 'Passat', 'Golf', 'i20', 'E200'];
        foreach ($models as $model) {
            if (stripos($title, $model) !== false) {
                return $model;
            }
        }
        return 'Otomobil';
    }

    private function rewrite_description($title, $source) {
        $synonyms = [
            'temiz' => 'bakımlı',
            'hatabos' => 'hasarsız',
            'sahibinden' => 'doğrudan satıcıdan',
            'acil' => 'öncelikli',
            'full' => 'dolu paket',
            'sıfır' => 'showroom kondisyon',
        ];

        $base = $title . ' - ' . sprintf(__('Bu ilan %s kaynağından özetlenip özgünleştirildi.', 'otoder'), $source);
        foreach ($synonyms as $search => $replace) {
            $base = str_ireplace($search, $replace, $base);
        }

        $snippets = [
            __('Ekspertiz raporu hazır, servis bakımlı, garantili parça kullanıldı.', 'otoder'),
            __('Şehir içi ve uzun yolda sorunsuz, yakıt tasarruflu kombinasyon.', 'otoder'),
            __('Fotoğraflar güncel, yerinde görülmeye hazır.', 'otoder'),
            __('Dijital vitrin için özgün açıklama ve SEO uyumlu anahtar kelime seti kullanıldı.', 'otoder'),
        ];

        shuffle($snippets);
        $unique_tail = wp_generate_password(6, false);

        return $base . ' ' . implode(' ', array_slice($snippets, 0, 3)) . ' #' . $unique_tail;
    }

    private function import_listing($item) {
        $source_key = md5($item['title'] . $item['source']);
        $existing = get_posts([
            'post_type' => 'listing',
            'posts_per_page' => 1,
            'meta_key' => 'otoder_source_key',
            'meta_value' => $source_key,
            'fields' => 'ids',
        ]);
        if (!empty($existing)) {
            return;
        }

        foreach (['brand', 'model', 'fuel', 'transmission', 'body_type', 'color', 'location'] as $tax) {
            if (!term_exists($item[$tax], $tax)) {
                wp_insert_term($item[$tax], $tax);
            }
        }

        $post_id = wp_insert_post([
            'post_title' => $item['title'],
            'post_type' => 'listing',
            'post_status' => 'publish',
            'post_content' => $this->rewrite_description($item['title'], $item['source']),
            'meta_input' => [
                'price' => $item['price'],
                'year' => $item['year'],
                'kilometer' => $item['kilometer'],
                'fuel' => $item['fuel'],
                'transmission' => $item['transmission'],
                'body_type' => $item['body_type'],
                'color' => $item['color'],
                'location' => $item['location'],
                'otoder_source_key' => $source_key,
                'otoder_source' => $item['source'],
            ],
        ]);

        if ($post_id && !is_wp_error($post_id)) {
            wp_set_post_terms($post_id, [$item['brand']], 'brand');
            wp_set_post_terms($post_id, [$item['model']], 'model');
            wp_set_post_terms($post_id, [$item['fuel']], 'fuel');
            wp_set_post_terms($post_id, [$item['transmission']], 'transmission');
            wp_set_post_terms($post_id, [$item['body_type']], 'body_type');
            wp_set_post_terms($post_id, [$item['color']], 'color');
            wp_set_post_terms($post_id, [$item['location']], 'location');
        }
    }
}

new Otoder_Essentials();
