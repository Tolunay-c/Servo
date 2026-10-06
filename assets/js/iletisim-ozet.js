/* Kaynak: _design/iletisim.html <script> (içerik değiştirilmeden taşındı) */
(function(){
 "use strict";
 var ids=['f-hat','f-cizgi','f-strok','f-mesafe','f-sik','f-montaj','f-firma','f-kisi'];
 var el={}; ids.forEach(function(i){el[i]=document.getElementById(i);});
 var vT=document.getElementById('v-title'), vD=document.getElementById('v-desc'), vS=document.getElementById('v-sum');
 var boxes=Array.prototype.slice.call(document.querySelectorAll('.chk input'));

 function verdict(n){
   if(!n) return ['ÇİZGİ SAYISINI GİRİN',
     'Aynı anda kaç kesim çizgisinin görünmesi gerektiği, iki sistem arasındaki en belirleyici ayrımdır. Bu alanı doldurduğunuzda yönü burada göreceksiniz.'];
   if(n<=4) return ['CORE PRECISOR',
     'Aynı anda '+n+' çizgi, kapalı gövdeli Core Precisor\'ın kapsamında kalır. Altı hareket konfigürasyonundan hangisinin uygun olduğu, çizgilerin bağımsız mı simetrik mi hareket etmesi gerektiğine göre belirlenir.'];
   if(n<=6) return ['CORE SINIRINDA',
     n+' çizgi Core Precisor\'ın üst sınırına yakın. Reçete çeşitliliğiniz ileride artacaksa Shuttle Precisor da değerlendirilmeli; karar keşifte netleşir.'];
   return ['SHUTTLE PRECISOR',
     'Aynı anda '+n+' çizginin görünmesi gerekiyorsa tek servo mekikle çok sayıda lazer arabasını konumlandıran Shuttle Precisor mimarisi uygundur. Gövde boyu, rezerve alanı dahil edilerek hesaplanır.'];
 }

 function build(){
   var n=parseInt(el['f-cizgi'].value,10)||0;
   var v=verdict(n); vT.textContent=v[0]; vD.textContent=v[1];

   var L=[];
   L.push('SERVOMOTION — UYGULAMA ÖZETİ');
   L.push('----------------------------------------');
   if(el['f-firma'].value) L.push('Firma            : '+el['f-firma'].value);
   if(el['f-kisi'].value)  L.push('İlgili kişi      : '+el['f-kisi'].value);
   if(el['f-hat'].value)   L.push('Hat / uygulama   : '+el['f-hat'].value);
   if(n)                   L.push('Eşzamanlı çizgi  : '+n+' adet');
   if(el['f-strok'].value) L.push('Strok            : '+el['f-strok'].value+' mm');
   if(el['f-mesafe'].value)L.push('Çalışma mesafesi : '+el['f-mesafe'].value+' mm');
   if(el['f-sik'].value)   L.push('Reçete değişimi  : '+el['f-sik'].value);
   if(el['f-montaj'].value)L.push('Montaj yapısı    : '+el['f-montaj'].value);
   var haz=boxes.filter(function(b){return b.checked;}).map(function(b){return b.getAttribute('data-h');});
   if(haz.length) L.push('Hazır bilgiler   : '+haz.join(', '));
   var strok=parseInt(el['f-strok'].value,10)||0;
   if(n){ L.push(''); L.push('Ön değerlendirme : '+v[0]); }
   if(strok>4000) L.push('Not              : 4000 mm üzeri gövde talebe göre tasarlanır.');
   L.push('');
   L.push('Uygulamayı birlikte değerlendirmek için dönüş yapmanızı rica ederiz.');
   var txt=L.join('\n');
   vS.textContent = (L.length>4) ? txt : 'Özet, alanları doldurdukça burada oluşur.';
   return txt;
 }

 ids.forEach(function(i){ el[i].addEventListener('input',build); el[i].addEventListener('change',build); });
 boxes.forEach(function(b){ b.addEventListener('change',build); });

 var note=document.getElementById('b-note'), noteHTML=note.innerHTML;
 function flash(msg){ note.textContent=msg; setTimeout(function(){ note.innerHTML=noteHTML; },4000); }

 document.getElementById('b-mail').addEventListener('click',function(){
   var t=build();
   window.location.href='mailto:info@servomotion.com.tr?subject='+
     encodeURIComponent('Uygulama özeti — '+(el['f-firma'].value||'ServoMotion web sitesi'))+
     '&body='+encodeURIComponent(t);
 });
 document.getElementById('b-wa').addEventListener('click',function(){
   window.open('https://wa.me/905446361200?text='+encodeURIComponent(build()),'_blank','noopener');
 });
 document.getElementById('b-copy').addEventListener('click',function(){
   var t=build();
   function fallback(){
     var ta=document.createElement('textarea'); ta.value=t; ta.style.position='fixed'; ta.style.opacity='0';
     document.body.appendChild(ta); ta.select();
     try{ document.execCommand('copy'); flash('Özet panoya kopyalandı.'); }
     catch(e){ flash('Kopyalanamadı — özeti seçip elle kopyalayabilirsiniz.'); }
     document.body.removeChild(ta);
   }
   if(navigator.clipboard && navigator.clipboard.writeText){
     navigator.clipboard.writeText(t).then(function(){ flash('Özet panoya kopyalandı.'); },fallback);
   } else { fallback(); }
 });

 var KONU={lastik:'Lastik ve kompozit — takviye katmanı serimi',
           nonwoven:'Hijyenik ürünler ve nonwoven',
           ambalaj:'Ambalaj ve kağıt',
           film:'Film ve folyo', tekstil:'Tekstil ve teknik kumaşlar', etiket:'Etiket ve yapışkanlı bant'};
 try{
   var k=(location.search.match(/[?&]konu=([^&]+)/)||[])[1];
   if(k && KONU[decodeURIComponent(k)]){ el['f-hat'].value=KONU[decodeURIComponent(k)]; }
 }catch(e){}
 build();
})();
