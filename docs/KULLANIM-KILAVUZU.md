# ServoMotion web sitesi: kullanım kılavuzu

Bu kılavuz, sitenin içeriğini yönetim panelinden nasıl değiştireceğinizi anlatır. Kod bilgisi gerekmez.

Panele `https://www.servomotion.com.tr/wp-admin` adresinden kullanıcı adınız ve şifrenizle girersiniz. Sol tarafta bir menü vardır; bu kılavuzdaki "Sayfalar", "Yazılar", "Site Ayarları" gibi adlar o menüdeki başlıklardır.

**Genel ipuçları**

- Her alanın altında küçük gri bir açıklama vardır. Emin olmadığınızda önce onu okuyun.
- Değişiklikleriniz siz **Güncelle** (veya **Yayımla**) düğmesine basana kadar sitede görünmez.
- Yanlış bir değişiklik yaptıysanız, sayfanın sağ tarafındaki **Sürümler** bağlantısından eski hâline dönebilirsiniz.
- Bir alanı boş bırakırsanız o kısım sitede hiç görünmez. Boşluk veya çizgi koymanıza gerek yoktur.
- Ürün ve marka adlarını (ServoMotion, Core Precisor, Shuttle Precisor vb.) düz yazın. Site bunları kendisi doğru biçimde işaretler.

---

## 1. Sayfa düzenleme

**Sayfalar** menüsünde sitenin tüm sayfaları listelenir: Ana sayfa, Vaka Çalışmaları, Servis, Kurumsal, İletişim, Teknik Dokümanlar, Sıkça Sorulan Sorular, Sözlük, KVKK ve Çerezler.

1. Sayfalar menüsüne girin ve düzenlemek istediğiniz sayfanın adına tıklayın.
2. Sayfa, alt alta dizilmiş **bloklardan** oluşur. Her blok sitedeki bir bölümdür (örneğin "Sayfa başlığı", "Kart ızgarası", "Çağrı bandı").
3. Bir bloğun içini görmek için başlığına tıklayın; blok açılır. Metni, görseli veya bağlantıyı değiştirin.
4. Sağ üstteki **Güncelle** düğmesine basın ve sayfayı sitede kontrol edin.

**Görsel değiştirmek:** Görsel alanındaki küçük kalem simgesine veya "Görsel ekle" düğmesine tıklayın. Bilgisayarınızdan yeni bir görsel yükleyin veya ortam kütüphanesinden seçin. Görselin **alternatif metin** kısmına görseli kısaca anlatan bir cümle yazın; bu, görme engelli ziyaretçiler ve arama motorları için önemlidir. Görsel alanı boş kalırsa çoğu yerde tasarımdaki çizim (şema) gösterilir.

**Bağlantı (buton) değiştirmek:** Bağlantı alanına tıkladığınızda açılan pencerede adresi yazın veya sitedeki bir sayfayı arayıp seçin. "Bağlantı metni" butonun üzerinde yazan yazıdır.

**Core Precisor ve Shuttle Precisor sayfaları** blok yapısında değildir. Bu sayfalarda alanlar üstteki **sekmelere** ayrılmıştır (Üst bölüm, Teknik özellikler, Diğer ürüne geçiş, İletişim bandı vb.). Simülasyon ve konfigüratör bölümlerinde sadece başlık ve açıklama değiştirilebilir; çalışan araçların kendisi sabittir. Lazer güvenliği bölümü iki ürün sayfasında ortaktır ve **Site Ayarları > Ürün güvenliği** sekmesinden değiştirilir.

## 2. Blok ekleme, sıralama, silme

Sayfa düzenleme ekranında:

- **Blok eklemek:** Blok listesinin en altındaki **Blok ekle** düğmesine basın ve listeden blok türünü seçin. İki blok arasına eklemek için bir bloğun başlık çubuğunun üzerine gelince çıkan **+** simgesini kullanın.
- **Sıralamak:** Bloğu başlık çubuğundan tutup yukarı veya aşağı sürükleyin.
- **Kopyalamak:** Başlık çubuğundaki kopyala simgesiyle bloğun bir kopyasını oluşturursunuz. Benzer bir bölüm yaparken işe yarar.
- **Silmek:** Başlık çubuğundaki **−** simgesine basın. Sildiğiniz blok, siz Güncelle'ye basana kadar geri getirilebilir (sayfayı kaydetmeden kapatmanız yeterli).
- **Kapatmak / açmak:** Uzun sayfalarda blokları başlıklarına tıklayarak kapatabilirsiniz. Kapalı blokların başlığında bölümün başlığı görünür.

**Sık kullanılan blok seçenekleri**

- **Arka plan:** Bölümün zemin rengi (varsayılan gri, beyaz veya koyu). Art arda gelen bölümlerde gri ve beyazı sırayla kullanmak sayfayı daha okunur yapar.
- **Başlık stili:** "Standart" çoğu bölüm için uygundur. "Çizgili" ürün sayfalarındaki görünümdür.
- **Görünüm:** Bazı blokların birden fazla görünümü vardır. Görünüm değişince altındaki alanlar da değişir; sadece o görünümde kullanılan alanlar gösterilir.
- **Bölüm bağlantı adı:** Menüden veya bir butondan doğrudan bu bölüme gitmek istiyorsanız kısa bir ad yazın (ör. `iletisim`). Bağlantı adresin sonuna `#iletisim` eklenerek verilir.

**Blok türleri (kısaca)**

| Blok | Ne için kullanılır |
|---|---|
| Ana sayfa hero / Sayfa başlığı (hero) | Sayfanın en üstündeki büyük başlık alanı |
| Ürün kartları | İki ürünün yan yana tanıtımı |
| Numaralı kartlar, Kart ızgarası | Kısa başlık ve metinli kart dizileri |
| İki yaklaşım + rakamlar | Karşılaştırma ve öne çıkan sayılar |
| Tablo | Satır ve sütunlu karşılaştırma tablosu |
| Madde listesi | Maddeli liste |
| Metin, Uzun metin | Düz paragraflar; uzun metin KVKK gibi sayfalar içindir |
| Uygulama alanları | Ana sayfadaki uygulama kartları |
| Sistem önerisi, İlgili bağlantılar | Uygulama sayfalarındaki öneri ve bağlantılar |
| Vaka listesi | Vaka çalışmaları |
| İletişim bilgileri, Uygulama özeti aracı | İletişim sayfası |
| Çağrı bandı (CTA) | Sayfa sonundaki "bize ulaşın" bandı |
| Doküman listesi, Sıkça sorulan sorular, Sözlük | Bu içerikleri ilgili kayıtlardan otomatik listeler (bkz. bölüm 4) |

**Uygulama alanları** (Lastik, Nonwoven, Ambalaj vb.) **Uygulama alanları** menüsündedir ve sayfalar gibi bloklarla düzenlenir. Her kaydın altındaki "Ana sayfa kartı ve ayarlar" kutusu, ana sayfadaki kartın içeriğini belirler. "Kendi sayfası var" kapalıysa kayıt sadece ana sayfada kart olarak görünür, ayrı bir sayfası olmaz.

## 3. Rehber yazısı yazma

Rehber yazıları **Yazılar** menüsündedir.

1. **Yazılar > Yeni ekle**'ye tıklayın.
2. En üste yazının başlığını yazın.
3. Metni yazmaya başlayın. Her paragraf ayrı bir bloktur; Enter'a bastığınızda yeni paragraf açılır.
4. **Ara başlık** için satırın başındaki **+** simgesinden "Başlık" bloğunu seçin ve **H2** olarak bırakın. Yazının yanındaki "İçindekiler" listesi bu ara başlıklardan otomatik oluşur. İki veya daha fazla ara başlık varsa içindekiler görünür.
5. Kalın yazı için metni seçip **B** simgesine basın. Bağlantı için metni seçip zincir simgesine basın.

**Hazır kutular (modeller):** **+** simgesine basın, **Modeller** sekmesine geçin ve **ServoMotion rehber** grubunu seçin:

- **Bilgi kutusu:** Solunda sarı çizgi olan gri kutu. Küçük bir başlık ve bir iki paragraf içerir.
- **Değer kutuları:** Yan yana sayı kutuları. Tablodaki her satır bir kutudur: 1. sütun açıklama, 2. sütun değer, 3. sütun birim.
- **Karşılaştırma tablosu:** Satır ve sütunlu tablo. İlk satır başlık satırıdır.
- **Numaralı adımlar:** Adım adım anlatım için numaralı liste.

Eklenen kutunun içindeki örnek metinleri kendi metninizle değiştirin. Editörde kutular sade görünür; sitede tasarımdaki görünümüyle çıkar. Yazıyı kaydetmeden önce sağ üstteki **Önizleme** ile kontrol edebilirsiniz.

**Sağ taraftaki ayarlar (Yazı sekmesi)**

- **Kategoriler:** Yazının konusu (ör. Seçim, Verim). Rehber sayfasındaki filtre düğmeleri kategorilerden oluşur.
- **Ürün alanları:** Yazının ilgili olduğu ürün (Core, Shuttle). Rehber sayfasındaki ürün filtresi buradan çalışır.
- **Özet:** Rehber listesindeki kartta görünen kısa açıklama. Bir iki cümle yeterlidir.
- **Öne çıkarılmış görsel:** İsteğe bağlıdır. Yüklerseniz kartta ve yazının üstünde şemanın yerine bu görsel gösterilir.
- **Sabit:** Bu kutuyu işaretlerseniz yazı rehber sayfasının en üstünde büyük kart olarak görünür. Sabitlenmiş yazı yoksa en yeni yazı orada görünür.

**Yazının altındaki "Rehber yazısı bilgileri" kutusu**

- **Konu etiketi:** Başlığın üstündeki küçük konu yazısı.
- **Giriş metni:** Başlığın altındaki büyük giriş paragrafı.
- **Yazar:** Boş bırakılırsa Site Ayarları'ndaki varsayılan yazar adı kullanılır.
- **Okuma süresi (dakika):** Boş bırakılırsa metnin uzunluğundan otomatik hesaplanır.
- **Kart şeması:** Görsel yüklemediyseniz kartta gösterilecek çizim.
- **İlgili yazılar:** Yazının altında önerilecek en fazla 3 yazı. Boş bırakılırsa en yeni 3 yazı gösterilir.

Hazır olunca **Yayımla**'ya basın. Yazı rehber sayfasında hemen görünür.

## 4. SSS, sözlük ve doküman ekleme

Bu içerikler kendi menülerinde tutulur. İlgili sayfalar bunları otomatik olarak listeler.

### Sıkça sorulan sorular (SSS menüsü)

1. **SSS > Yeni ekle**'ye tıklayın.
2. Başlık alanına **soruyu**, alttaki metin alanına **cevabı** yazın.
3. Sağ taraftaki **SSS grupları** kutusundan sorunun grubunu seçin (ör. Seçim, Kurulum).
4. **Yayımla**'ya basın. Soru, SSS sayfasında grubunun altında görünür.

- Soruların grup içindeki sırası sağ taraftaki **Sıralama** numarasıyla belirlenir. Küçük numara önce gelir.
- Grupların kendi sırası **SSS > Gruplar** ekranında her grubun **Sıra** alanıyla belirlenir.
- Sorular Google'ın SSS sonuçları için de otomatik işaretlenir; ayrıca bir şey yapmanız gerekmez.

### Sözlük (Sözlük menüsü)

1. **Sözlük > Yeni ekle**'ye tıklayın.
2. Başlık alanına **terimi**, metin alanına **açıklamasını** yazın.
3. Terimin başka yazılışları varsa (ör. İngilizcesi, kısaltması) **Alternatif adlar** alanına yazın. Sözlük sayfasındaki arama bunları da bulur.
4. **Yayımla**'ya basın. Terim sözlük sayfasında kendiliğinden yerini alır (varsayılan olarak alfabetik sırayla).

### Dokümanlar (Dokümanlar menüsü)

1. **Dokümanlar > Yeni ekle**'ye tıklayın.
2. Başlığı yazın. **Dosya** alanından PDF'i yükleyin; kart başlığını, açıklamayı ve alt bilgi satırını (ör. "PDF · 2 SAYFA · REV. 09/2026") doldurun.
3. **Yayımla**'ya basın.
4. **Önemli:** Dokümanın sitede görünmesi için **Sayfalar > Teknik Dokümanlar** sayfasını açın. "Doküman listesi" bloğundaki **Dokümanlar** alanından yeni dokümanı seçin, sürükleyerek sıralayın ve **Güncelle**'ye basın.

Bir dokümanın yeni sürümünü yayımlamak için doküman kaydını açıp **Dosya** alanından eski dosyayı kaldırın, yenisini yükleyin ve alt bilgi satırındaki revizyon tarihini güncelleyin.

**Vaka çalışmaları** da aynı şekilde **Vaka çalışmaları** menüsünden eklenir. Görünmeleri için **Sayfalar > Vaka Çalışmaları** sayfasındaki "Vaka listesi" bloğundan seçilmeleri gerekir.

## 5. Site Ayarları

Sol menüdeki **Site Ayarları**, sitenin her sayfasında ortak olan bilgileri tek yerden yönetir. Burada yaptığınız değişiklik tüm sayfalara birden yansır. Ekranın üstünde sekmeler vardır:

| Sekme | İçerik |
|---|---|
| **Genel** | Logo altı yazısı, firma unvanı, marka adı ve bağlantısı |
| **İletişim** | Telefon, e-posta, WhatsApp, faks, adres, harita bağlantısı, vergi bilgisi, footer'daki diğer markalar. Telefon ve WhatsApp numaralarını sitede görünmesini istediğiniz biçimde yazın; arama bağlantıları otomatik oluşur. |
| **Bülten** | Sayfa altındaki bülten bandının başlığı ve metni |
| **Formlar** | İletişim sayfasındaki uygulama özeti aracından gelen mesajların gideceği e-posta adresi. Boşsa İletişim sekmesindeki e-posta kullanılır. |
| **Sayfalar** | Vaka çalışmalarının listelendiği sayfa (diğer sayfalardaki vaka bağlantıları buraya gider) |
| **Rehber** | Rehber sayfasının başlığı ve açıklaması, varsayılan yazar adı, her yazının sonundaki kutu ve not |
| **Ürün güvenliği** | İki ürün sayfasında ortak olan lazer güvenliği bölümü |
| **404 sayfası** | Bulunamayan bir adrese gidildiğinde gösterilen sayfanın metinleri ve bağlantıları |

Değişiklikten sonra sağdaki **Güncelle** düğmesine basmayı unutmayın.

## Menüler

Üstteki ana menü ve alttaki footer menüsü **Görünüm > Menüler** ekranından düzenlenir. Menüye sayfa eklemek için soldaki listeden sayfayı işaretleyip **Menüye ekle**'ye basın; öğeleri sürükleyerek sıralayın. Bir öğeyi biraz sağa sürüklerseniz bir üstteki öğenin alt menüsü olur ve sitede açılır menü olarak görünür. Bitince **Menüyü kaydet**'e basın.

## Yardım

Bir şey beklediğiniz gibi görünmüyorsa:

1. Sayfayı kaydettiğinizden emin olun ve sitede sayfayı yenileyin.
2. Yine olmuyorsa **Sürümler**'den önceki hâline dönün.
3. Sorun devam ederse hangi sayfada ve hangi bölümde olduğunu, mümkünse bir ekran görüntüsüyle birlikte site yöneticinize iletin.
