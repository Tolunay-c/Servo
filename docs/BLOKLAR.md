# Sayfa blokları (flexible content)

Ana sayfa, `uygulama` kayıtları ve `page.php` kullanan tüm sayfalar, SCF'nin **Sayfa blokları** (`bloklar`) alanıyla kurulur. Her blok `template-parts/blocks/{blok-adi}.php` dosyasıdır ve tasarımdaki bir `<section>`'a karşılık gelir. Alan tanımları `acf-json/group_servo_bloklar.json` içindedir. Proje kararı: CLAUDE.md, madde 16.

## Ortak yapı

- **Arka plan** (`arka_plan`): varsayılan (gri) / beyaz / koyu. Koyu zeminde bölüm açıklaması tasarımdaki açık renkle (`#b4c2d1`) basılır.
- **Bölüm başlığı** (`template-parts/blocks/_bolum-basligi.php`): `baslik_stili` (standart `.section-head` / çizgili `.sec-hd`), `etiket`, `baslik`, `aciklama`.
- **Bölüm bağlantı adı** (`capa`): serbest çapa. Aynı sayfada tekrar ederse sonraki bloğa sıra eki verilir (`servo_block_id()`).
- **Stile bağlı ID:** sadece Ürün kartları bloğunda `#sistemler`; panelden değiştirilemez.
- Metin alanlarında satır sonu `<br>`, boş satır yeni paragraf olur. Satır içi HTML (`<b>`, `<a>`) `wp_kses_post` ile korunur. Ürün ve marka adları düz yazılır; `<bdi lang="en">` işaretini blok çıktısında `servo_bdi()` ekler (CLAUDE.md, madde 17).

## Bloklar

| Blok (layout) | Dosya | Görünümler | Tasarımdaki karşılığı |
|---|---|---|---|
| `ana_hero` | `ana-hero.php` | — | Ana sayfa `.hero` |
| `sayfa_hero` | `sayfa-hero.php` | standart (`.phero`; yan içerik: yok / rakamlar / görsel; isteğe bağlı üst yol `.crumb`; butonlarda PDF rozeti ve indirme), kurumsal (`.company-hero`), iletişim (`.ch`) | İç sayfaların üst bölümü |
| `urun_kartlari` | `urun-kartlari.php` | — | Ana sayfa `#sistemler` |
| `numarali_kartlar` | `numarali-kartlar.php` | kazanım (`.benefits`), adım (`.steps2`), sade adım (`.pack`), akış (`.flow`), zaman (`.milestones`) | Ana sayfa, servis, iletişim, kurumsal |
| `iki_yaklasim` | `iki-yaklasim.php` | — | Ana sayfa `.catband` |
| `tablo` | `tablo.php` | karşılaştırma (`.cmp` / `.ctable`), parametre (`.meas`) | Ana sayfa, uygulama sayfaları |
| `madde_listesi` | `madde-listesi.php` | belirti (`.symlist`), ihtiyaç (`.needrow`) | Uygulama sayfaları, servis |
| `uygulama_alanlari` | `uygulama-alanlari.php` | — | Ana sayfa `#uygulama-alanlari` |
| `metin` | `metin.php` | iki kolon (`.about`), hikâye (`.company-story`) | Ana sayfa, kurumsal |
| `kart_izgarasi` | `kart-izgarasi.php` | marka (`.brand-grid`, isteğe bağlı `.fotorow`), talep (`.reqgrid`) | Kurumsal, dokümanlar |
| `sistem_onerisi` | `sistem-onerisi.php` | — | Uygulama sayfaları `.pickbox` + `.casebox` |
| `ilgili_baglantilar` | `ilgili-baglantilar.php` | — | Uygulama sayfaları `.related-links` |
| `vaka_listesi` | `vaka-listesi.php` | alt alta, iki sütun (`.cases`) | Başarı hikayeleri `article.case` |
| `iletisim_bilgileri` | `iletisim-bilgileri.php` | dört kutu (`.igrid`), iki kolon (`.company-contact`) | İletişim, kurumsal |
| `uygulama_ozeti` | `uygulama-ozeti.php` | — | İletişim `#ozet` (JS'e bağlı form) |
| `cta` | `cta.php` | iletişim bandı (`.contact`), çağrı bandı (`.cta-band`) | Ana sayfa, iç sayfalar |
| `dokuman_listesi` | `dokuman-listesi.php` | — | Dokümanlar `.dgrid` + `.catnote` notları |
| `sss_listesi` | `sss-listesi.php` | — | SSS `.qwrap` (`section.qgroup` > `details.qa`) + FAQPage JSON-LD |
| `sozluk` | `sozluk.php` | — | Sözlük `.gsearchbox` + `.terms` (`sozluk.js`'e bağlı, sayfada bir kez) |
| `uzun_metin` | `uzun-metin.php` | — | KVKK `.legal` (bölüm: başlık, `.kv` bilgi kutusu, editör metni) |
| `talep_formu` | `talep-formu.php` | doküman, destek, iade, kılavuz | Dokümanlar ve servis alt sayfalarının formları (`section.talep-formu > .formbox`; REST `servomotion/v1/talep`) |
| `servis_kanallari` | `servis-kanallari.php` | — | Servis `.svcgrid > .svc` (simge `assets/img/svg/servis-*.svg`) |
| `indirme_listesi` | `indirme-listesi.php` | — | Kılavuzlar `.dl` (Medya dosyası veya sayfa bağlantısı; rozet dosya türünden) |

## İçerik kaynakları

- **Uygulama kartları:** `uygulama` kaydının "Ana sayfa kartı ve ayarlar" grubu (`group_servo_uygulama_kart`). "Kendi sayfası var" kapalıysa kayıt sadece kartta görünür, tekil adresi 404 döner.
- **Vakalar:** `vaka` kaydının "Vaka bilgileri" grubu. Başlık `h3`, slug çapa (`#lastik`).
- **Dokümanlar:** `dokuman` kaydının "Doküman bilgileri" grubu (dosya, erişim, dil, talep kodu, form yazısı, kart başlığı, açıklama, alt bilgi satırı). Kartın etiketi dosya uzantısından gelir; dosyası olmayan doküman gösterilmez. Erişimi "Talep formuyla" olan dokümanın dosyası `uploads/servo-korumali/` dizinine taşınır (doğrudan erişime kapalı); kartta "TALEP EDİN" (`#talep`) düğmesi çıkar. Dosyalar yalnızca imzalı, 7 gün geçerli `?servo-indir=` bağlantısıyla verilir (`inc/talep.php`).
- **SSS:** `sss` kaydı (başlık = soru, klasik editör = cevap), `sss_grup` taxonomy'si. Grup sırası grubun "Sıra" alanı (term meta `sira`), soru sırası "Sıralama" (`menu_order`). SSS bloğu olan sayfada `servo_sss_jsonld()` aynı sorulardan FAQPage JSON-LD basar.
- **Sözlük:** `terim` kaydı (başlık = terim, klasik editör = açıklama, "Alternatif adlar" alanı). Sıralama blokta seçilir: "Sıralama" alanı veya alfabetik.
- **İletişim bilgileri:** Site Ayarları > İletişim. Vaka çalışmaları sayfası: Site Ayarları > Sayfalar.
- **Sayfa ayarları** (yan kutu): "Bölüm açıklamalarını iki yana yasla" → `assets/css/hizalama.css`.

## Sayfa – blok eşleşmesi (tasarım)

| Sayfa | Bloklar |
|---|---|
| Ana sayfa | ana_hero → urun_kartlari → numarali_kartlar (kazanım, koyu) → iki_yaklasim → tablo (karşılaştırma, çizgili) → uygulama_alanlari (beyaz) → metin (iki kolon, beyaz) → cta (iletişim bandı) |
| Uygulama (×6) | sayfa_hero (görsel + rakamlar) → madde_listesi (beyaz) → tablo (parametre) → sistem_onerisi (beyaz) → ilgili_baglantilar → cta |
| Başarı hikayeleri | sayfa_hero → vaka_listesi (iki sütun, beyaz) → cta |
| Servis | sayfa_hero (rakamlar) → servis_kanallari (beyaz) → numarali_kartlar (adım, beyaz) → madde_listesi (+ not) → cta |
| Servis alt sayfaları (destek hattı, iade ve sevkiyat, kılavuzlar) | sayfa_hero (üst yol) → madde_listesi / numarali_kartlar (sade adım) / indirme_listesi → (talep formu: FAZ 2) → cta |
| Kurumsal | sayfa_hero (kurumsal) → metin (hikâye) → numarali_kartlar (zaman, koyu, `yolculuk`) → metin (hikâye, beyaz) → kart_izgarasi → kart_izgarasi (+ fotoğraflar, beyaz) → iletisim_bilgileri (iki kolon, `iletisim`); iki yana yaslama açık |
| İletişim | sayfa_hero (iletişim) → uygulama_ozeti (`ozet`) → numarali_kartlar (akış) → iletisim_bilgileri (dört kutu, `kunye`) |
| Dokümanlar | sayfa_hero → dokuman_listesi (beyaz, `veri-sayfalari`) → kart_izgarasi (talep, `uygulama-notu`) → cta |
| SSS | sayfa_hero → sss_listesi (beyaz) |
| Sözlük | sayfa_hero → sozluk (beyaz) → cta |
| KVKK | sayfa_hero → uzun_metin (beyaz) |

## CSS

Flexible sayfalar sırasıyla `fonts.css`, `base.css`, `brandfont.css` ve `bloklar.css` yükler; `servis_kanallari` veya `talep_formu` bloğu olan sayfalar ayrıca `servis.css`, tüm sayfalar en sonda `navfit.css` yükler. `servis.css`'teki form kuralları iletişim sayfasının `.fld`/`.two` kurallarıyla çakışmasın diye `.talep-formu` altına kapsanmıştır. `bloklar.css`, tasarım sayfalarındaki `<style>` bloklarından değerleri değiştirilmeden üretilmiştir; bölümleri dosyanın başındaki yorumda listelenir. Uygulama özeti ve sözlük script'leri sadece ilgili blok sayfadaysa yüklenir (`servo_page_has_block()`).

## Sabit şablonlar

- **404 (`404.php`):** metin ve bağlantılar Site Ayarları > 404 sayfası sekmesinden gelir; boş alanlarda tasarımdaki metinler kullanılır.
- **Arama (`search.php`):** tasarımda yok; `.phero`, `.gsearchbox` ve `.reqgrid > .req` bileşenleriyle kurulur. Blok sayfalarının metni kayıt sırasında düz metne çevrilip `_servo_arama_metni` meta alanında tutulur ve arama bu alana da bakar (`inc/arama.php`). Kendi sayfası kapalı uygulama kayıtları sonuçlara girmez.
