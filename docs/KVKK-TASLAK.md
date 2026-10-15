# KVKK aydınlatma metni: form güncellemesi taslağı

**Durum:** Taslak. Siteye uygulanmadı; müşteriye ve hukuk danışmanına onaya gönderilecek. Köşeli parantezli yerler müşterinin cevaplaması gereken sorulardır.

**Neden gerekli:** Sitedeki KVKK sayfası "sunucuya veri gönderen bir form bulunmuyor; uygulama özeti ve bülten alanları bilgileri sunucuya iletmez" diyor. Artık dört form türü bilgileri sunucuya gönderiyor ve ekibe e-postayla iletiyor. Metnin buna göre değişmesi gerekir. Ayrıca çerez bölümündeki "tek dış kaynak font servisi" cümlesi de eskimiş durumda: yazı tipleri artık siteden (self-host) yükleniyor, Google Fonts kullanılmıyor.

## 1. Sitedeki formlar ve topladıkları veriler

| Form | Nerede | Toplanan veriler | Ne olur |
|---|---|---|---|
| **Uygulama özeti** | İletişim sayfası | Hat/uygulama bilgileri, çizgi sayısı, strok, çalışma mesafesi, reçete sıklığı, montaj yapısı, hazırdaki bilgiler, firma, ilgili kişi ve iletişim (ad, telefon veya e-posta) | Özet, ekibin e-posta adresine gönderilir. Ziyaretçi isterse özeti kendi e-posta veya WhatsApp uygulamasından da gönderebilir (bu durumda veri sitemizden geçmez). |
| **Doküman talebi** | Dokümanlar sayfası | İstenen dokümanlar (veri sayfaları, sertifika, İngilizce sürümler), firma, ilgili kişi, e-posta, telefon (isteğe bağlı), uygulama/hat (isteğe bağlı) | Talep ekibe e-postayla iletilir. Ziyaretçiye, istenen dokümanlar için 7 gün geçerli indirme bağlantıları e-postayla gönderilir. |
| **Destek talebi** | Servis > Destek hattı | Firma, ilgili kişi, e-posta, telefon (isteğe bağlı), sistem, seri numarası, öncelik, belirtinin açıklaması | Ekibe e-postayla iletilir. |
| **İade / gönderim bildirimi** | Servis > İade ve sevkiyat | Firma, ilgili kişi, e-posta, telefon (isteğe bağlı), sistem, seri numarası, gönderilecek bileşen, arıza notu | Ekibe e-postayla iletilir. |
| **Kılavuz talebi** | Servis > Kılavuzlar | Firma, ilgili kişi, e-posta, seri numarası, istenen doküman, not | Ekibe e-postayla iletilir. |

Her gönderimde ayrıca gönderimin yapıldığı sayfanın adresi ve gönderim tarihi/saati e-postaya eklenir.

**Bu formlarda toplanmayanlar:** Kimlik numarası, ödeme bilgisi, özel nitelikli kişisel veri istenmez. Bülten aboneliği şu an bir listeye bağlı değildir; "KAYDOLUN" alanı yalnızca ziyaretçinin e-posta uygulamasını açar.

## 2. Teknik olarak ne saklanıyor?

- Formdan gelen bilgiler **sitenin veritabanında saklanmaz**; yalnızca e-postayla ekibe (ve doküman talebinde ziyaretçiye) gönderilir. Kayıt, ekibin e-posta kutusunda ve e-posta sağlayıcısının sunucusunda kalır.
- Kötüye kullanımı önlemek için gönderen IP adresi, **geri döndürülemez bir özet (hash) olarak ve en fazla 15 dakika** boyunca sayaç amacıyla tutulur; IP adresinin kendisi saklanmaz.
- Süreli indirme bağlantısı imzalıdır; içinde kişisel veri yoktur, 7 gün sonra çalışmaz. İndirme işlemini site ayrıca kaydetmez; barındırma sağlayıcısının standart erişim kayıtları (log) tutulabilir.
- Formlar çerez veya izleme aracı kullanmaz. Gönderim güvenliği için sayfaya gömülü, kişiye özel olmayan bir doğrulama anahtarı kullanılır.
- Geliştirme ortamında bulunan "mail kaydı" özelliği canlıda kapalıdır.

## 3. Önerilen metin (KVKK sayfasında değişecek bölümler)

> Aşağıdaki başlıklar mevcut sayfadaki aynı adlı bölümlerin yerine geçer. Diğer bölümler (veri sorumlusu, haklar, değişiklikler) aynı kalır.

### HANGİ VERİLERİ İŞLİYORUZ

Bize e-posta, telefon veya WhatsApp ile ulaştığınızda ya da sitedeki formları doldurduğunuzda aşağıdaki veriler işlenir:

- **Kimlik ve iletişim bilgisi:** ad-soyad, firma, e-posta adresi, telefon numarası
- **Talebinizin içeriği:** hat ve uygulama bilgileri, teknik ölçüler, sistem ve seri numarası, belirti ve arıza açıklamaları, istediğiniz dokümanlar, paylaştığınız belge ve görseller
- **Yazışma kayıtları:** e-posta ve mesaj geçmişi
- **Gönderim bilgisi:** formun gönderildiği sayfa ve gönderim zamanı

Sitemizde beş form bulunur: uygulama özeti, doküman talebi, destek talebi, iade/gönderim bildirimi ve kılavuz talebi. Bu formlara yazdığınız bilgiler sitemiz üzerinden e-posta ile ekibimize iletilir. Bülten alanı şu anda bir listeye bağlı değildir; yalnızca e-posta uygulamanızda hazır bir mesaj oluşturur.

### İŞLEME AMAÇLARI VE HUKUKİ SEBEP

- Talebinize teknik olarak yanıt vermek, teklif ve uygulama değerlendirmesi hazırlamak
- Talep ettiğiniz dokümanları (veri sayfası, sertifika, kılavuz) size iletmek
- Sözleşme öncesi görüşmeleri yürütmek ve kurulan sözleşmenin ifasını sağlamak
- Satış sonrası servis, destek, iade ve yedek parça süreçlerini yürütmek
- Yasal saklama ve belgelendirme yükümlülüklerini yerine getirmek

Bu işlemler KVKK m.5/2 uyarınca sözleşmenin kurulması veya ifası, hukuki yükümlülüğün yerine getirilmesi ve meşru menfaat hukuki sebeplerine dayanır. Ticari elektronik ileti gönderimi yalnızca açık rızanızla yapılır ve her iletide çıkma imkânı sunulur. Doküman talebiniz, size gelecekte pazarlama iletisi gönderilmesi için rıza sayılmaz.

### DOKÜMAN İNDİRME BAĞLANTILARI

Veri sayfası ve sertifika gibi dokümanlar doğrudan indirmeye açık değildir. Doküman talep formunu doldurduğunuzda, istediğiniz dokümanların indirme bağlantıları e-posta adresinize gönderilir. Bağlantılar **7 gün** geçerlidir; süre dolduktan sonra çalışmaz ve dokümanı yeniden talep edebilirsiniz. Bağlantı yalnızca sizin talebiniz için üretilir; başkalarıyla paylaşmamanızı öneririz.

### AKTARIM

Kişisel verileriniz, yalnızca yukarıdaki amaçlarla sınırlı olmak üzere; e-posta ve barındırma hizmeti aldığımız tedarikçilerimize [e-posta sağlayıcısının adı], yasal olarak yetkili kamu kurumlarına ve talebiniz gerektiriyorsa proje kapsamında çalıştığımız iş ortaklarına aktarılabilir. [E-posta sağlayıcısı yurt dışında ise: Sağlayıcının sunucuları yurt dışında bulunduğundan veriler, KVKK m.9 kapsamında yurt dışına aktarılabilir.] Pazarlama amacıyla üçüncü taraflara satılmaz veya devredilmez.

### SAKLAMA SÜRESİ

Formlar aracılığıyla ilettiğiniz bilgiler sitenin veritabanında tutulmaz; ekibimizin e-posta kutusunda ve e-posta sağlayıcımızın sunucusunda, talebinizin sonuçlanmasından sonra [___ yıl / müşteri belirleyecek] süreyle, sözleşme ilişkisi kurulursa ilgili yasal saklama süreleri boyunca saklanır. Süre sonunda silinir, yok edilir veya anonim hâle getirilir. Kötüye kullanımı önleme amacıyla gönderen adresin geri döndürülemez özeti en fazla 15 dakika tutulur.

### ÇEREZ POLİTİKASI (güncelleme)

Bu sitede reklam, izleme veya profilleme amaçlı çerez kullanılmıyor; ziyaretçi davranışını izleyen üçüncü taraf analiz aracı da bulunmuyor. Yazı tipleri dahil tüm kaynaklar sitemizin kendi sunucusundan yüklenir; sayfaları görüntülerken üçüncü taraf bir servise istek gönderilmez. Barındırma hizmeti sağlayıcımız, güvenlik ve hata takibi amacıyla standart sunucu kayıtları (erişim logları) tutabilir. Çerez kullanımı ileride değişirse bu sayfa güncellenir ve gerekli olduğu durumda onay mekanizması eklenir.

> Not: "yazı tipleri sitemizden yüklenir" cümlesi, **Rank Math** ve olası harici eklentiler (ör. SMTP, Google Search Console, analitik) eklendiğinde yeniden kontrol edilmelidir.

## 4. Formların altındaki kısa not (sitede yayında)

Her formun gönder düğmesinin altında (talep formları ve uygulama özeti) şu not görünür; metin **Site Ayarları > Formlar > "Formların altındaki aydınlatma notu"** alanından düzenlenir:

> Bu formda paylaştığınız bilgiler yalnızca talebinizi yanıtlamak için kullanılır ve e-posta ile ekibimize iletilir. Ayrıntılar için [KVKK aydınlatma metnimizi] inceleyin.

## 5. Müşteriye sorulacaklar

1. **E-posta sağlayıcısı nedir** (Google Workspace, Microsoft 365, hosting maili…)? Sunucuları yurt dışındaysa aktarım cümlesine m.9 eklenmeli.
2. **Saklama süresi** kaç yıl olsun (talep sonuçlandıktan sonra)?
3. Dokümanı talep edenlere **sonradan bilgi/pazarlama iletisi** gönderilecek mi? Gönderilecekse ayrı bir açık rıza kutusu eklenmeli; şu an eklenmedi.
4. Bülten bir servise bağlandığında (Mailchimp vb.) ayrı bir aydınlatma ve rıza metni gerekir.
5. Veri sorumlusu bilgileri (unvan, adres, vergi dairesi/no) güncel mi?
6. Mevcut metindeki "Başvurular en geç otuz gün içinde sonuçlandırılır" ifadesi KVKK'ya uygundur; başvuru kanalı olarak sadece e-posta ve posta mı kalsın?

## 6. Uygulama adımları (onay sonrası)

1. Onaylanan metin KVKK sayfasında (Sayfalar > KVKK ve Çerezler > "Uzun metin" bloğu) ilgili bölümlere yazılır; "Son güncelleme" tarihi değiştirilir.
2. Yalnızca KVKK sayfasının metni değişir; formlarda ek bir işlem gerekmez.

## 7. İngilizce sürüm notu (FAZ 4)

- EN formların altındaki not, EN Site Ayarları'nda boşsa şu varsayılanı kullanır: *"The details you share in this form are used only to answer your request and are sent to our team by email. See our privacy notice for details."*
- EN gizlilik sayfası (`/en/privacy/`) taslaktır. Paketteki EN metin, TR KVKK sayfasındaki "sunucuya veri gönderen form yok" cümlesinin karşılığını içeriyor olabilir; bu bölümdeki Türkçe metin onaylanınca EN sayfa da **aynı değişikliklerle birlikte** güncellenmeli (talep formları, e-posta ile iletim, korumalı doküman bağlantıları, çerezler).
- Müşteri onayı TR ve EN metni birlikte kapsamalı; EN metin onay öncesi yayınlanmamalı.
