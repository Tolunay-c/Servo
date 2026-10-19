# Teknik ifade önerileri (denetim MAJ-04)

**Durum:** Müşteri onayına gönderilecek taslak. Sitede **hiçbir metin değiştirilmedi**; aşağıdaki öneriler onaylanınca içerik güncellemesi yapılır.

**Neden:** Denetim raporu (V1.0, 15 Ekim 2026), Shuttle sayfasındaki "sınırsız çizgi" ve "sistem maliyeti büyümez" ile Core sayfasındaki "toz/elyaf/nem ulaşmaz" gibi ifadelerin koşulsuz vaat gibi okunduğunu belirtiyor. Master (veri sayfası) daha sınırlı bir dil kullanıyor. Önerilen metinler ölçülebilir ve koşullu; ürün, ray, strok ve koruma sınıfı gibi bilgilere bağlanıyor.

**Nasıl okunur:**
- *Konum:* Sayfa ve panel alanı (SCF alan adı). Alan adları her iki dilde aynıdır; TR sayfa ID 6/7, EN sayfa ID 341/342 (yerel kurulum).
- *Referans paketteki karşılığı:* `servomotion-site-tum131026/` içindeki dosya. TR referansta aynı cümle bulunuyorsa "TR referansta aynen var" yazıldı. İngilizce referans (`en/*.html`) Core ve Shuttle için Türkçeden kısa ve daha ölçülüdür; İngilizce sitedeki bazı cümleler **İngilizce referansta hiç yok** (Türkçeden çevrilerek eklenmiş).
- **[TEKNİK ONAY]** işareti: Rakamı veya koşulu teknik ekibin doğrulaması gerekir. Rakam uydurulmadı; köşeli parantezli yerler doldurulmalı.

---

## 1. Shuttle: "Tek servo, sınırsız çizgi"

| | |
|---|---|
| **Konum** | Shuttle Precisor, hero madde listesi (`hero_maddeler`, 1. madde), TR ve EN |
| **Mevcut TR** | **Tek servo, sınırsız çizgi:** Lazer başına motor, sürücü ve kablo gerekmez; çizgi sayısı arttıkça sistem maliyeti ve kablo karmaşası büyümez. |
| **Mevcut EN** | **One servo, unlimited lines:** No motor, drive or cable per laser; system cost and cable complexity do not grow as the number of lines increases. |
| **Referans (TR)** | `shuttle-precisor.html`: aynen var. |
| **Referans (EN)** | `en/shuttle-precisor.html`: bu madde yok. Karşılığı mimari bölümünde: "The number of servo axes remains unchanged as laser carriages are added, helping limit system complexity as the application scales." |
| **Sorun** | "Sınırsız" kapasite vaadi; ray boyu, strok, minimum çizgi aralığı ve güç sınırı yok sayılıyor. "Maliyet büyümez" yanlış: araba ve ray maliyeti çizgi sayısıyla artar, yalnızca servo eksen sayısı sabit kalır. Tablodaki "5 – 40+ araba · projeye göre" ile de çelişiyor. |
| **Önerilen TR** | **Tek servo eksen, çok sayıda çizgi:** Çizgi sayısı arttıkça servo eksen sayısı artmaz; lazer başına motor, sürücü ve kablo gerekmez. Araba ve ray maliyeti projeye göre değişir. Ulaşılabilir çizgi sayısı ray boyu, strok, minimum çizgi aralığı, çevrim süresi ve güç sınırlarıyla projede belirlenir (5 – 40+ araba). |
| **Önerilen EN** | **One servo axis, many lines:** The number of servo axes does not increase as lines are added, and no motor, drive or cable is needed per laser. Carriage and rail cost vary by project. The achievable line count is defined per project by rail length, travel, minimum line spacing, cycle time and power limits (5 – 40+ carriages). |
| **Not** | Dekorasyon amaçlı "sınırsız" sözcüğü başlıkta da kullanılmayacak. **[TEKNİK ONAY]** "5 – 40+" aralığı ve sınırlayıcı parametrelerin listesi. |

## 2. Shuttle: hero şeridi "bakım yükü yok"

| | |
|---|---|
| **Konum** | Shuttle Precisor, hero şeridi (`hero_serit`, 4. kutu), TR ve EN |
| **Mevcut TR** | Lazer başına motor tahrik maliyeti, kablo karmaşası ve bakım yükü yok. |
| **Mevcut EN** | No per-laser motor drive cost, cable complexity or maintenance load. |
| **Referans (TR)** | `shuttle-precisor.html`: aynen var ("Tek Eksen Tahrik Mimarisi"). |
| **Referans (EN)** | `en/shuttle-precisor.html`: "One servo axis performs all movement instead of a motor, drive and cable per laser: fewer components and a simpler maintenance routine." |
| **Sorun** | "Bakım yükü yok" mutlak ifade; tek servo eksen, ray ve arabalar da bakım gerektirir. |
| **Önerilen TR** | Lazer başına ayrı motor, sürücü ve kablo yok; bakım tek servo eksende toplanır. |
| **Önerilen EN** | No separate motor, drive or cable per laser; maintenance is concentrated on the single servo axis. |

## 3. Shuttle: "Ölçeklenebilir Yatırım" ve "Düşük İşletme Maliyeti"

| | |
|---|---|
| **Konum** | Shuttle Precisor, mimari bölümü, "Faydalar" satırı (`mimari_satirlar`, 2. satır, 4. ve 5. adım), TR ve EN |
| **Mevcut TR** | *Ölçeklenebilir Yatırım:* Kesim çizgisi sayısı arttığında tahrik ve kontrol altyapısı aynı kalır, yalnızca araba eklenir. / *Düşük İşletme Maliyeti:* Az sayıda hareketli bileşen, az yedek parça ve kısa duruş süresi anlamına gelir. |
| **Mevcut EN** | *Scalable Investment:* When the number of cutting lines grows, the drive and control infrastructure stays the same; only carriages are added. / *Low Operating Cost:* Few moving components mean few spare parts and short downtime. |
| **Referans (TR)** | `shuttle-precisor.html`: aynen var. |
| **Referans (EN)** | `en/shuttle-precisor.html`: karşılığı yok (yukarıdaki "servo eksen sayısı değişmez" cümlesi dışında). |
| **Sorun** | "Aynı kalır", "kısa duruş süresi" ölçülmüş veriye bağlı değil; kontrol altyapısı (PLC kapasitesi, besleme) çizgi sayısıyla sınırlara dayanabilir. |
| **Önerilen TR** | *Ölçeklenebilir Yatırım:* Çizgi sayısı arttığında servo tahrik ekseni sabit kalır; yalnızca araba ve ray uzunluğu eklenir. Kontrol ve besleme kapasitesi projede doğrulanır. / *Düşük Bakım Yükü:* Az sayıda hareketli bileşen bakım kalemlerini azaltır. Yedek parça ve duruş süresi [TEKNİK ONAY: saha verisi veya hedef değer] ile belirtilir. |
| **Önerilen EN** | *Scalable Investment:* As the number of lines grows, the servo drive axis stays the same; only carriages and rail length are added. Control and power capacity are verified in the project. / *Lower Maintenance Load:* Few moving components reduce the number of maintenance items. Spare parts and downtime are stated as [TECHNICAL APPROVAL: field data or target value]. |
| **Not** | "Düşük İşletme Maliyeti" başlığı da maliyet vaadi içerir; "Düşük Bakım Yükü" olarak değiştirilmesi önerilir. |

## 4. Shuttle: "kablo… ortadan kalkar — en sık arıza veren kalem"

| | |
|---|---|
| **Konum** | Shuttle Precisor, mimari bölümü, "Kesintisiz Besleme" kartı (`mimari_kartlar`, 1. kart), **yalnızca TR** |
| **Mevcut TR** | Lazerler bulundukları konumdan bağımsız olarak sürekli beslenir. Hareketli kablo, enerji zinciri ve kablo sarkması ortadan kalkar — hattın en sık arıza veren kalemi baştan devre dışı kalır. |
| **Mevcut EN** | EN sayfada bu kart metni boş; karşılığı "Upper rail" kartında: "Lasers are powered continuously at any position along the rail, so moving cables and energy chains are not required." |
| **Referans (TR)** | `shuttle-precisor.html`: aynen var. |
| **Referans (EN)** | `en/shuttle-precisor.html`: "Lasers are powered continuously at any position along the rail, so moving cables and energy chains are not required." |
| **Sorun** | "Hattın en sık arıza veren kalemi" kanıtsız karşılaştırma. |
| **Önerilen TR** | Lazerler rayın her noktasında sürekli beslenir; hareketli kablo ve enerji zinciri gerekmez. (İngilizce referanstaki cümlenin karşılığı.) |
| **Önerilen EN** | Lasers are powered continuously at any position along the rail, so moving cables and energy chains are not required. (Mevcut, değişmez.) |

## 5. Shuttle: zaman ve mutlak kararlılık ifadeleri (ek bulgu)

| | |
|---|---|
| **Konum** | Shuttle Precisor, hero madde listesi, 2. ve 3. madde (`hero_maddeler`), TR ve EN; "Kilitli araba" kartı (`mimari_kartlar`) |
| **Mevcut TR** | **Saniyeler içinde reçete değişimi:** … hat dakikalarca beklemez. / **Kurulum sonrası kararlılık:** … çizgiler hizalandıkları yerde kalır. / "…hat titreşimi ya da darbe çizgiyi kaydırmaz." |
| **Mevcut EN** | **Recipe changeover in seconds** … the line does not wait for minutes. / **Stability after setup** … / (kart metni EN'de kısaltılmış) |
| **Referans (TR)** | `shuttle-precisor.html`: aynen var. |
| **Referans (EN)** | `en/shuttle-precisor.html`: "carries the carriages to their recipe positions in sequence"; süre veya "titreşim kaymaz" iddiası yok. |
| **Sorun** | "Saniyeler", "dakikalarca beklemez" ve "titreşim/darbe kaydırmaz" ölçüye bağlı değil. Süre araba sayısına ve strok'a bağlıdır; Core sayfasındaki simülasyon da toplam süreyi göstermediğini söylüyor. |
| **Önerilen TR** | **Hızlı reçete değişimi:** Mekik, rayda kilitli arabaları sırayla yeni ölçüye taşır. Değişim süresi araba sayısına ve stroka bağlıdır; örnek süre [TEKNİK ONAY: örnek konfigürasyon ve ölçülen süre] olarak verilir. / **Kurulum sonrası kararlılık:** Konumlanan her araba rayda mekanik olarak kilitlenir; mekik çekildikten sonra çizgiler hizalandıkları yerde kalır. |
| **Önerilen EN** | **Fast recipe changeover:** The shuttle moves the locked carriages to the new size in sequence. Changeover time depends on carriage count and travel; an example time is given as [TECHNICAL APPROVAL: example configuration and measured time]. / **Stability after setup:** Each carriage locks mechanically to the rail once positioned; the lines stay where they were aligned after the shuttle withdraws. |
| **Not** | "Titreşim ya da darbe kaydırmaz" cümlesi, kilit mekanizmasının test koşuluna bağlanabiliyorsa yazılır; bağlanamıyorsa çıkarılır. |

## 6. Core: "Hattın tozu, elyafı ve nemi hareketli parçalara ulaşmaz"

| | |
|---|---|
| **Konum** | Core Precisor, "Bu Mimarinin Getirisi" bölümü, "Korumalı Gövde" (`calisma_satirlar`, 2. satır, 1. adım), TR ve EN |
| **Mevcut TR** | Lazer modülleri, servo eksenler ve kablolama kapalı gövde içindedir. Hattın tozu, elyafı ve nemi hareketli parçalara ulaşmaz; temizlik dış yüzey ve lazer penceresiyle sınırlı kalır. |
| **Mevcut EN** | The laser modules, servo axes and cabling sit inside a closed housing. Dust, fibre and moisture from the line cannot reach the moving parts; cleaning is limited to the outer surface and the laser window. |
| **Referans (TR)** | `core-precisor.html`: aynen var. |
| **Referans (EN)** | `en/core-precisor.html`: bu bölüm yok. Koruma sınıfı tabloda: "Rail assembly protection class: IP54 · higher protection on request"; laser koruma sınıfı IP54. |
| **Sorun** | Koşulsuz koruma vaadi ("ulaşmaz / cannot reach"). IP54 toza ve neme karşı **sınırlı** korumadır (toz girişi tamamen engellenmez; her yönden su püskürtmesine dayanır, daldırmaya değil). Servis kapağı açıldığında koruma kalkar. |
| **Önerilen TR** | Lazer modülleri, servo eksenler ve kablolama kapalı gövde içindedir. Gövde IP54 koruma sınıfındadır (daha yüksek koruma talebe göre); toz, elyaf ve nemin hareketli parçalara ulaşması bu sınıf ölçüsünde sınırlanır. Temizlik dış yüzey ve lazer penceresiyle sınırlı kalır. |
| **Önerilen EN** | The laser modules, servo axes and cabling sit inside a closed housing. The housing is rated IP54 (higher protection on request), which limits the ingress of dust, fibre and moisture to the moving parts to the extent defined by that class. Cleaning is limited to the outer surface and the laser window. |
| **Not** | **[TEKNİK ONAY]** IP54 değerinin gövde için mi yoksa yalnızca ray montajı için mi geçerli olduğu; test veya sertifika kaydı. Kanıt yoksa cümle "kapalı gövde içindedir; temizlik dış yüzeyle sınırlıdır" kısmında bırakılır. |

## 7. Core: "Maliyet makine genişliğine değil… göre belirlenir"; "ek şase gerekmez"

| | |
|---|---|
| **Konum** | Core Precisor, "Bu Mimarinin Getirisi" bölümü, "Ölçülü Yatırım" ve "Kompakt Montaj" (`calisma_satirlar`, 2. satır, 5. ve 4. adım), TR ve EN |
| **Mevcut TR** | *Kompakt Montaj:* Makine boyunca ray, araba park alanı veya ek şase gerekmez. / *Ölçülü Yatırım:* Az çizgili hatlarda bir veya iki eksen yeterlidir. Maliyet makine genişliğine değil, gerçekten ihtiyaç duyulan çizgi sayısına göre belirlenir. |
| **Mevcut EN** | *Compact mounting:* No rail along the machine, carriage park zone or extra chassis is required. / *Proportionate investment:* On lines with few reference lines, one or two axes are enough. Cost follows the number of lines actually needed, not the width of the machine. |
| **Referans (TR)** | `core-precisor.html`: aynen var. |
| **Referans (EN)** | `en/core-precisor.html`: bu bölüm yok. Tabloda: "Housing / rail assembly length up to 4,000 mm · longer on request", "System weight varies with application and housing length", "Mounting interface: Defined per application · adapter plate to the existing frame or a new support". |
| **Sorun** | Tablo "Montaj arayüzü: uygulamaya göre belirlenir · bağlantı plakası mevcut şaseye veya yeni taşıyıcıya" derken metin "ek şase gerekmez" diyor; gövde boyu (600–4.000 mm) makine genişliğine göre seçildiği için maliyet makine genişliğinden de etkilenir. |
| **Önerilen TR** | *Kompakt Montaj:* Sistem tek gövde hâlinde hattın üzerine monte edilir; ray veya araba park alanı gerektirmez. Bağlantı plakası mevcut şaseye veya yeni bir taşıyıcıya uyarlanır. / *Ölçülü Yatırım:* Az çizgili hatlarda bir veya iki eksen yeterlidir. Maliyet gövde uzunluğuna ve ihtiyaç duyulan çizgi sayısına göre projede belirlenir. |
| **Önerilen EN** | *Compact mounting:* The system is mounted over the line as a single housing and needs no rail or carriage park zone. The connection plate is adapted to the existing chassis or a new carrier. / *Proportionate investment:* On lines with few reference lines, one or two axes are enough. Cost is defined in the project from the housing length and the number of lines needed. |

## 8. Core: merkez lazer "hiç hareket etmez", "her an doğrulanabilir"; "iki kat çizgi"

| | |
|---|---|
| **Konum** | Core Precisor, "Bu Mimarinin Getirisi", "Kalıcı Merkez Referansı" ve "Simetrik Hareket" (`calisma_satirlar`, 2. satır, 2. ve 3. adım), TR ve EN |
| **Mevcut TR** | Merkezdeki sabit lazer hiç hareket etmez. … makine merkezi, ölçüm yapmadan her an doğrulanabilir. / … aynı adım sayısında iki kat çizgi konumu gösterilir. |
| **Mevcut EN** | The fixed laser in the centre never moves. … the machine centre can be verified at any time without measuring. / … the same number of steps shows twice as many line positions. |
| **Referans (TR)** | `core-precisor.html`: aynen var. |
| **Referans (EN)** | `en/core-precisor.html`: "A fixed laser marks the centre axis while the moving lasers mark the target positions on either side." (mutlak ifade yok). |
| **Sorun** | "Hiç hareket etmez / her an doğrulanabilir" mutlak; sabit lazer ayrı bir braketle bağlıdır ve konfigürasyona (K1–K6) göre eklenir ("İhtiyaca göre sabit bir merkez referans lazeri eklenebilir"). "İki kat" yalnızca çift kanallı/simetrik konfigürasyonlarda geçerli. |
| **Önerilen TR** | Sabit merkez referans lazeri (isteğe bağlı) hareket etmez ve ayrı braketle bağlanır. Simetrik reçeteler bu çizgiye göre kurulur; makine merkezi ölçüm yapmadan kontrol edilebilir. / Çift kanallı ve simetrik konfigürasyonlarda (K[…]) iki lazer aynı anda hareket eder; aynı adım sayısında iki çizgi konumu gösterilir. **[TEKNİK ONAY: hangi K modelleri]** |
| **Önerilen EN** | The optional fixed centre reference laser does not move and is mounted on a separate bracket. Symmetrical recipes are set up around this line, so the machine centre can be checked without measuring. / In dual-channel and symmetrical configurations (K[…]) two lasers move at the same time; the same number of steps shows two line positions. **[TECHNICAL APPROVAL: which K models]** |

## 9. Core → Shuttle geçiş metni: "onlarca lazer arabası"

| | |
|---|---|
| **Konum** | Core Precisor, geçiş bandı (`gecis_metin`), TR ve EN |
| **Mevcut TR** | …tek servo mekikle onlarca lazer arabasını konumlandıran Shuttle Precisor bu iş için tasarlandı. |
| **Mevcut EN** | …one servo shuttle positioning dozens of laser carriages — is designed for the job. |
| **Referans (TR)** | `core-precisor.html`: aynen var. |
| **Referans (EN)** | `en/core-precisor.html`: "Where simultaneous reference lines are not required…"; "onlarca" ifadesi yok. Shuttle tablosu: "5 – 40+ · project specific". |
| **Sorun** | "Onlarca" sayısal olarak 5–40+ ile uyumsuz değil ama belirsiz ve sınırsız izlenimi destekliyor. |
| **Önerilen TR** | …tek servo mekikle 5 – 40+ lazer arabasını (projeye göre) konumlandıran Shuttle Precisor bu iş için tasarlandı. |
| **Önerilen EN** | …one servo shuttle positioning 5 – 40+ laser carriages (project specific) — is designed for the job. |

---

## Onay için kontrol listesi

- [ ] Teknik ekip: [TEKNİK ONAY] işaretli rakam ve koşulları doldurdu veya cümleyi çıkarma kararı verdi (madde 1, 3, 5, 6, 8).
- [ ] IP54 değerinin neyi kapsadığı ve test/sertifika kaydı yazılı olarak bildirildi (madde 6).
- [ ] Türkçe ve İngilizce metinler aynı kapsamı anlatıyor (EN sayfada olup EN referansta olmayan cümleler netleşti).
- [ ] Onaylanan metinler panelden (Core ve Shuttle sayfa alanları) girilebilir; kod değişikliği gerekmez. İsterseniz, onaydan sonra tema güncelleme rutinine (`inc/guncelleme.php`) eklenerek canlıda tek seferde uygulanır.

## Kapsam dışı

- Ana sayfadaki karşılaştırma tablosu ("Tek servo tahrik, çok sayıda kilitli araba", "tüm çizgiler yerinde kalır") bu listeye alınmadı; denetim bulgusu Core/Shuttle sayfalarıyla ilgili. Gerekirse aynı yöntemle eklenir.
- Servis sayfalarındaki "Garanti" ifadeleri ("garanti süresi… teklifte yazılı olarak tanımlanır") koşulludur, değiştirilmesi önerilmedi.
