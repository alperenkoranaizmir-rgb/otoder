# Otoder Essentials Eklentisi

Tema ile birlikte yüklenmesi gereken otomatik kurulum ve toplayıcı eklentisidir. WordPress 6.8.3 ve Otoder Auto Marketplace teması ile çalışır.

## Kurulum
1. `plugins/otoder-essentials` klasörünü WordPress kurulumunuzdaki `wp-content/plugins/` içine kopyalayın.
2. WordPress yönetiminde **Eklentiler > Yüklü Eklentiler** ekranından **Otoder Essentials** eklentisini etkinleştirin.
3. Etkinleştirme sonrası otomatik olarak aşağıdakiler yapılır:
   - `listing` özel yazı tipi ve marka/model/yakıt/vites/kasa/rengine/konuma ait taksonomiler kayıt edilir.
   - Ana sayfa, ilan gönder, hesap ve vitrin sayfaları ilgili kısa kodlar ile oluşturulur.
   - Örnek kategoriler ve 3 adet demo ilan eklenir.
   - Saatlik çalışan ilan toplayıcı cron işi başlatılır.

## Kullanım
- Yönetim panelinde **Araçlar > Otoder Otomasyon** sayfasından tek tuşla tohumlama işlemini yeniden çalıştırabilir veya ilan toplayıcıyı hemen tetikleyebilirsiniz.
- Toplayıcı, arabam.com ve sahibinden.com sayfalarını HTTP isteği ile yoklar, bulduğu başlıkları benzersiz açıklamalarla yeni ilanlara dönüştürür. Kaynak yanıtı alınamazsa örnek ilanlar eklemeye devam eder.
- Oluşturulan sayfalarda tema kısa kodları ( `[otoder_search]`, `[otoder_submit]`, `[otoder_account]` ) hazır gelir.

## Özellikler
- Kategori ve sayfa tohumlama, demo ilan ekleme
- arabam.com / sahibinden.com tarayıcı (cron) + manuel tetikleme
- Benzersiz metin üretimi için senkronize açıklama yeniden yazım mantığı
- Saatlik cron planlaması ve nonce kontrollü yönetim formları
