/* ServoMotion: iletisim-gonder.js'in İngilizce sürümü; aynı mantık, yalnızca görünen metinler İngilizce. */
/* Sunucu tarafı form: aşağıdaki adresi kendi form servisinizin adresiyle doldurun
   (örn. https://formspree.io/f/xxxx). Boş bırakılırsa e-posta/WhatsApp akışı çalışmaya devam eder. */
window.SERVOMOTION_FORM_ENDPOINT = window.SERVOMOTION_FORM_ENDPOINT || ""; /* WP: adres wp_add_inline_script ile önceden verilir */
(function(){"use strict";
 var ep=window.SERVOMOTION_FORM_ENDPOINT, btn=document.getElementById('b-mail'), note=document.getElementById('b-note');
 if(!ep || !btn) return;
 var noteHTML=note.innerHTML;
 btn.textContent='SEND';
 btn.addEventListener('click',function(e){
   e.stopImmediatePropagation();
   var sum=document.getElementById('v-sum').textContent;
   var data={ozet:sum, firma:(document.getElementById('f-firma')||{}).value||'',
             kisi:(document.getElementById('f-kisi')||{}).value||'', kaynak:location.href};
   note.textContent='Sending…';
   fetch(ep,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify(data)})
    .then(function(r){ note.textContent = r.ok ? 'Thank you, your summary has reached us. We will get back to you as soon as possible.'
                                               : 'Could not be sent. Please copy the summary and send it by email.'; })
    .catch(function(){ note.textContent='Could not connect. Please copy the summary and send it by email.'; })
    .then(function(){ setTimeout(function(){ note.innerHTML=noteHTML; },7000); });
 }, true);
})();
