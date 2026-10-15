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

Rank Math'in ayarları, sayfa başlıkları/açıklamaları ve paylaşım görselleri yerelde `wp servo faz3` komutuyla kuruldu ve veritabanıyla birlikte canlıya gelir. Sihirbazı yeniden çalıştırmak gerekmez; canlıda sadece şunlar kontrol edilir:

- **Hesap bağlantısı:** Yerelde sihirbazın "şimdilik atla" adımı yapıldı (`rank_math_registration_skip`). Veritabanı canlıya aktarılınca canlıdaki eski Rank Math bağlantısı bu ayarla değişmiş olur; Rank Math'in ön yüz çıktısı (başlık, OG, site haritası) bağlantı olmadan çalışır. İsterseniz Rank Math > Genel Bakış'tan hesabı yeniden bağlayabilirsiniz (zorunlu değil).
- **Komutla yeniden kurmak gerekirse** (ör. temiz bir WordPress'e): `wp servo faz3 og rankmath seo tarihler` (tema klasöründeki paket klasörü gerekir; aksi hâlde Rank Math ayarlarını panelden yapın).
- **Site haritası:** `/sitemap_index.xml` açılmalı; Yazılar, Sayfalar, Uygulama alanları ve Kategoriler listelenmeli. Vaka, doküman, SSS ve terim kayıtlarının kendi sayfası yoktur, haritada olmamalıdır. Kalıcı bağlantılar ekranı bir kez kaydedilir (site haritası kuralları için).
- **robots.txt:** `https://servomotion.com.tr/robots.txt` açılmalı ve `Sitemap: …/sitemap_index.xml` satırını içermeli (WordPress/Rank Math üretir; kökte kurulu olduğu için çalışır, yerelde alt klasörde olduğu için çalışmaz).
- **Paylaşım önizlemesi:** Ana sayfa ve birkaç sayfa LinkedIn Post Inspector (`linkedin.com/post-inspector`) ve Facebook Sharing Debugger ile denenir; görsel 1200×630 olarak çıkmalıdır. Önizleme önbelleği için "yeniden tara" yapılır.
- **Yazar arşivleri** kapalı (`disable_author_archives`); rehber yazılarının Article şemasında yazar olarak kurum (ServoMotion) yer alır, kullanıcı adı çıkmaz. Yönetici kullanıcı adı yine de `admin` olmamalıdır.
- Google Search Console'a site haritası gönderilir.

## Güvenlik

- **İki adımlı doğrulama:** Yönetici girişi için bir iki adımlı doğrulama eklentisi kurulur (ör. **Two Factor** veya Wordfence'in 2FA'sı) ve yönetici hesabında açılır. Tema bunu sağlamaz.
- WordPress sürüm bilgisi sayfa kaynağında ve beslemelerde görünmez (tema kaldırdı). Kök dizindeki `readme.html`, `license.txt` ve `wp-config-sample.php` dosyaları da sürümü ele verir; canlı pakette bulunmamalıdır.
- Yönetici kullanıcı adı `admin` olmamalı; güçlü parola kullanılmalıdır.

## Site Ayarları

- **Formlar > Form alıcı adresi:** uygulama özeti, doküman talebi ve servis formlarının gideceği adres. Boşsa İletişim e-postası kullanılır.
- **Formlar > Doküman talebi: gönderim:** "Otomatik gönder" (varsayılan: ziyaretçiye 7 gün geçerli indirme bağlantıları e-postayla gider) veya "Sadece ekibe bildir".

## Korumalı doküman dizini

- Veri sayfaları ve sertifikalar `wp-content/uploads/servo-korumali/` altındadır; dizinin `.htaccess` dosyası doğrudan erişimi kapatır (Apache). Zip/aktarımda bu **gizli dosya da gelmeli** (`ls -la` ile kontrol edin). Nginx kullanan bir sunucuda `.htaccess` işlemez: dizin için sunucu kuralıyla `deny all` eklenmelidir.
- Kontrol: `https://servomotion.com.tr/wp-content/uploads/servo-korumali/servomotion-core-precisor-veri-sayfasi.pdf` adresi **403** vermeli. Formdan gelen bağlantı (`/?servo-indir=...`) 7 gün boyunca dosyayı indirtmeli, süre dolunca 410 dönmeli.
- **Zorunlu test (canlıya aldıktan hemen sonra):** Korumalı dizindeki bir PDF'in doğrudan adresini tarayıcıdan veya `curl -I` ile açın. **403 vermiyorsa** (dosya açılıyorsa ya da indiriliyorsa) sunucu LiteSpeed/Apache `.htaccess`'i okumuyor demektir (ör. `AllowOverride None` ya da LiteSpeed'in `.htaccess` desteğinin kapalı olması). Bu durumda veri sayfaları herkese açıktır; **sunucu yöneticisine bildirilmeli** ve düzeltilene kadar `servo-korumali` içindeki PDF'ler Medya'dan ve sunucudan kaldırılmalı ya da dizin için sunucu düzeyinde `deny all` kuralı eklettirilmelidir.
- Formlar canlıda gerçek mail gönderir; SMTP (aşağıda) kurulmadan doküman bağlantıları spam'e düşebilir.

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

## Polylang (İngilizce sürüm)

- Polylang eklentisi etkin olmalı. Veritabanı taşınıyorsa dil ayarları gelir; sıfırdan kuruluyorsa `wp servo faz4 dil ayarlar` çalıştırılır.
- Ayarlar > Diller: `tr` varsayılan (ön eksiz), `en` (`/en/`), adreste dil ön eki; çevrilebilir türler `uygulama, vaka, sss, terim`; taxonomy'ler `urun_alani, sss_grup`.
- **Ayarlar > Kalıcı bağlantılar > Kaydet** ve `wp rewrite flush`: EN ön ekleri (`/en/guide/`, `/en/application/`) kurallarla birlikte üretilir.
- İngilizce içerik: `wp servo faz4` (tekrar çalıştırılabilir). EN Site Ayarları "Site Settings (EN)" sayfasındadır.
- Dil düğmesi, hreflang ve `<html lang>` kontrol edilir; `/en/` → `/en/home/` yönlenmesi Polylang davranışıdır.
- EN sayfaların eski `*.html` adresleri için `docs/YONLENDIRMELER.md`'deki 301/302 bloğu.

## Tema paketi

Tema `bin/paket.sh` ile paketlenir (`dist/servo-<sürüm>.zip`). Paket **Görünüm > Temalar > Yeni ekle > Tema yükle** ile yüklenir; tema klasörü `servo` olmalıdır (favicon yönlendirmeleri bu yolu kullanır). Secure Custom Fields eklentisi temadan önce kurulup etkinleştirilir. Alan grupları temadaki `acf-json/` klasöründen otomatik gelir.

## İçerik

- Rehber yazıları: `wp servo rehber-aktar --kaynak=<tasarım klasörü> --dry-run`, ardından `--dry-run` olmadan.
