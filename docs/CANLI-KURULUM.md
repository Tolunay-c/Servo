# Canlı kurulumda yapılacak ayarlar

Tema bu ayarları kendisi yapmaz; canlı kurulumda (ve her yeni ortamda) elle veya WP-CLI ile yapılır.

## WordPress ayarları

| Ayar | Değer | WP-CLI |
|---|---|---|
| Ayarlar > Kalıcı bağlantılar > Özel yapı | `/rehber/%postname%/` | `wp rewrite structure '/rehber/%postname%/' --category-base='rehber/kategori'` |
| Ayarlar > Kalıcı bağlantılar > Kategori temeli | `rehber/kategori` (adres: `/rehber/kategori/secim/`) | (yukarıdaki komutla birlikte) |
| Ayarlar > Okuma > Yazılar sayfası | Rehber sayfası | `wp option update show_on_front page` ve `wp option update page_for_posts <ID>` |

Kalıcı bağlantı ayarı değiştikten sonra `.htaccess` kuralları yenilenmelidir. WP-CLI ile yapıldıysa ardından panelde **Ayarlar > Kalıcı bağlantılar** ekranı bir kez açılmalıdır: WP-CLI'nin yenilemesi Rank Math'in site haritası kurallarını içermez.

Eski `.html` adreslerinin yönlendirmeleri için `docs/YONLENDIRMELER.md`'deki kural bloğu `.htaccess`'e eklenir.

Kurulumla gelen "Örnek sayfa", "Gizlilik Politikası" taslağı ve "Merhaba dünya!" yazısı silinir.

## wp-config.php

- `WP_ENVIRONMENT_TYPE` `production` olmalı (veya hiç tanımlanmamalı). `local` / `development` iken form mailleri gönderilmez, Araçlar > Mail kayıtları'na yazılır.
- `SERVO_MAIL_LOG` tanımlanmamalı (tanımlıysa `false` olmalı).

## Rank Math

- Rank Math kurulum sihirbazı tamamlanır (yerelde tamamlanmadığı için modüller çalışmıyor). **Site haritası** modülü açılır; `/sitemap_index.xml` adresinin açıldığı kontrol edilir.
- Site haritasında Yazılar, Sayfalar, Uygulama alanları ve Kategoriler açık kalır. Vaka, doküman, SSS ve terim kayıtlarının kendi sayfası yoktur, site haritasında olmamalıdır. Kendi sayfası kapalı uygulama alanlarını tema otomatik olarak çıkarır.
- Tek yazar olduğu için **Başlıklar ve Meta > Yazarlar**'da yazar arşivleri kapatılır (`/rehber/author/<kullanıcı-adı>/` adresi kullanıcı adını gösterir). Yönetici kullanıcı adı `admin` olmamalıdır.
- Google Search Console'a site haritası gönderilir.

## Site Ayarları

- **Formlar > Uygulama özeti gönderim adresi:** özetlerin gideceği adres. Boşsa İletişim e-postası kullanılır.

## Mail (SMTP)

WordPress varsayılan olarak maili web sunucusunun kendisinden (PHP `mail()`) gönderir. Bu mailler çoğu zaman SPF/DKIM doğrulamasından geçmez ve alıcıda **spam'e düşebilir** veya hiç ulaşmaz. Bu yüzden canlıda bir SMTP eklentisi kurulur ve müşterinin kendi mail hesabına bağlanır:

1. Eklentilerden **FluentSMTP** (ücretsiz, önerilen) veya **WP Mail SMTP** kurulur.
2. Müşterinin mail sağlayıcısı seçilir (Google Workspace, Microsoft 365, Yandex, hosting maili vb.) ve bir gönderen hesabı bağlanır (ör. `web@servomotion.com.tr`). Mümkünse uygulama şifresi veya OAuth kullanılır, hesabın asıl şifresi yazılmaz.
3. Gönderen adı "ServoMotion", gönderen adresi bağlanan hesapla aynı olur ("Force From Email" açık).
4. Alan adının DNS'inde SPF, DKIM ve DMARC kayıtlarının mail sağlayıcısına göre tanımlı olduğu kontrol edilir.
5. Eklentinin "test maili" özelliğiyle deneme yapılır; ardından iletişim sayfasındaki uygulama özeti aracından bir deneme özeti gönderilip gelen kutusu (ve spam klasörü) kontrol edilir.

Tema gönderen adresini değiştirmez; bu ayar SMTP eklentisinden yönetilir.

## Önbellek

- Uygulama özeti adresi, en fazla 24 saat geçerli bir nonce içerir. Tam sayfa önbelleği kullanılırsa iletişim sayfası önbellekten hariç tutulmalı veya önbellek süresi 10 saatten kısa olmalı; aksi hâlde eski sayfadan yapılan gönderimler 403 alır.

## Tema paketi

Tema `bin/paket.sh` ile paketlenir (`dist/servo-<sürüm>.zip`). Paket **Görünüm > Temalar > Yeni ekle > Tema yükle** ile yüklenir; tema klasörü `servo` olmalıdır (favicon yönlendirmeleri bu yolu kullanır). Secure Custom Fields eklentisi temadan önce kurulup etkinleştirilir. Alan grupları temadaki `acf-json/` klasöründen otomatik gelir.

## İçerik

- Rehber yazıları: `wp servo rehber-aktar --kaynak=<tasarım klasörü> --dry-run`, ardından `--dry-run` olmadan.
