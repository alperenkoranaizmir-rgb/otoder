# Otoder Auto Marketplace (WordPress 6.8.3)

Tam işlevsel, demo içerik yüklü otomobil ilan teması. arabam.com ve sahibinden.com benzeri görünüm, ön yüzden ilan gönderme, ücretsiz üyelik akışı ve filtreli arama içerir. Otomatik kategori/sayfa tohumlama ve ilan toplayıcı için `Otoder Essentials` eklentisi ile birlikte kullanılması önerilir.

## Kurulum
1. `car-marketplace-theme` klasörünü `wp-content/themes/` içine kopyalayın.
2. WordPress yönetiminde **Görünüm > Temalar** ekranından **Otoder Auto Marketplace** temasını etkinleştirin.
3. Tema etkinleşince demo ilanlar otomatik oluşturulur ve “otoder-demo” adlı kullanıcı eklenir. Ek olarak `Otoder Essentials` eklentisini etkinleştirerek kategorileri, sayfaları ve toplayıcı cron görevini otomatik yükleyebilirsiniz.
4. Menüleri **Görünüm > Menüler** alanında `Primary` konumuna atayın.
5. Hızlı kullanım için yeni bir sayfa açıp **Otoder Dashboard** şablonunu seçin veya içerikte `[otoder_account]` kısa kodunu kullanın.

## Kısa Kodlar
- `[otoder_submit]`: Üye girişi yaptıktan sonra ilan gönderme formu.
- `[otoder_account]`: Kayıt/ giriş formları ve kullanıcı ilan listesi.
- `[otoder_dashboard]`: Sadece ilan listesi (giriş zorunlu).
- `[otoder_search]`: Fiyat ve taksonomi filtreli arama bölümü.

## Özellikler
- Custom post type: `listing` ve taksonomiler (marka, model, yakıt, vites, kasa tipi, renk, konum).
- Meta alanları: fiyat, yıl, kilometre, motor gücü, vites, çekiş, durum, video URL, galeri.
- Ön yüzden ilan gönderme ve demo içerik seed işlemi.
- Modern kart tabanlı tasarım, hero alanı, hızlı arama ve ücretsiz üyelik vurguları.

## Notlar
- İletişim ya da ödeme modülü yoktur; tamamen ücretsiz ilan odaklıdır.
- Formlar WordPress çekirdeğini kullanır; güvenlik için nonce kontrolleri eklenmiştir.
