/* Kaynak: _design/iletisim.html <script> (içerik değiştirilmeden taşındı) */
/* Sunucu tarafı form: aşağıdaki adresi kendi form servisinizin adresiyle doldurun
   (örn. https://formspree.io/f/xxxx). Boş bırakılırsa e-posta/WhatsApp akışı çalışmaya devam eder. */
window.SERVOMOTION_FORM_ENDPOINT = window.SERVOMOTION_FORM_ENDPOINT || ""; /* WP: adres wp_add_inline_script ile önceden verilir */
(function(){"use strict";
 var ep=window.SERVOMOTION_FORM_ENDPOINT, btn=document.getElementById('b-mail'), note=document.getElementById('b-note');
 if(!ep || !btn) return;
 var noteHTML=note.innerHTML;
 btn.textContent='GÖNDER';
 btn.addEventListener('click',function(e){
   e.stopImmediatePropagation();
   var sum=document.getElementById('v-sum').textContent;
   var data={ozet:sum, firma:(document.getElementById('f-firma')||{}).value||'',
             kisi:(document.getElementById('f-kisi')||{}).value||'', kaynak:location.href};
   note.textContent='Gönderiliyor…';
   fetch(ep,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(data)})
    .then(function(r){ note.textContent = r.ok ? 'Teşekkürler, özetiniz bize ulaştı. En kısa sürede dönüş yapacağız.'
                                               : 'Gönderilemedi. Lütfen özeti kopyalayıp e-posta ile iletin.'; })
    .catch(function(){ note.textContent='Bağlantı kurulamadı. Lütfen özeti kopyalayıp e-posta ile iletin.'; })
    .then(function(){ setTimeout(function(){ note.innerHTML=noteHTML; },7000); });
 }, true);
})();
