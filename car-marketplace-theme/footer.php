</div>
<footer class="footer">
    <div class="footer-inner">
        <div>
            <h3>arabam style ilan sistemi</h3>
            <p><?php _e('Ücretsiz üyelik, hızlı ilan girişi ve otomatik vitrin ithalatı ile tam dolu otomobil pazaryeri.', 'otoder'); ?></p>
            <small>Otoder · <?php echo date('Y'); ?></small>
        </div>
        <div>
            <h4><?php _e('Hızlı Linkler', 'otoder'); ?></h4>
            <ul>
                <li><a href="<?php echo esc_url(home_url('/ilan-gonder')); ?>"><?php _e('İlan Ver', 'otoder'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/vitrin')); ?>"><?php _e('Vitrin', 'otoder'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/hesabim')); ?>"><?php _e('Hesabım', 'otoder'); ?></a></li>
            </ul>
        </div>
        <div>
            <h4><?php _e('Güven', 'otoder'); ?></h4>
            <p><?php _e('İlanlar açıklama yeniden yazımı ve marka/model doğrulamalarıyla SEO uyumlu hazırlanır.', 'otoder'); ?></p>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
