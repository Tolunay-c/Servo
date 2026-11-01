/* ServoMotion: bülten formu e-postayı servomotion/v1/bulten uç noktasına kaydeder (mailto açmaz).
   Tasarımın bülten/rehber script'lerine dokunmaz; belge düzeyindeki yakalama dinleyicisi onlardan önce çalışır.
   Adres (nonce ile) ve metinler inc/enqueue.php'den window.SERVO_BULTEN olarak verilir. */
(function(){"use strict";
 var c=window.SERVO_BULTEN;
 if(!c || !c.adres || !window.fetch) return;
 document.addEventListener('submit',function(e){
   var f=e.target;
   if(!f || f.id!=='nf') return;
   e.stopImmediatePropagation(); e.preventDefault();
   var m=document.getElementById('nmail'), note=document.getElementById('nnote'),
       btn=f.querySelector('button[type="submit"]'), web=f.querySelector('input[name="web"]');
   function goster(metin,hata){
     if(!note) return;
     note.setAttribute('role',hata?'alert':'status'); note.textContent=metin;
   }
   if(!m || !m.value.trim() || (m.checkValidity && !m.checkValidity())){
     if(m){ m.setAttribute('aria-invalid','true'); m.focus(); }
     goster(c.gecersiz,true);
     return;
   }
   m.removeAttribute('aria-invalid');
   if(btn) btn.disabled=true;
   fetch(c.adres,{method:'POST',headers:{'Content-Type':'application/json'},
     body:JSON.stringify({eposta:m.value.trim(),lang:c.lang,web:web?web.value:''})})
   .then(function(r){ return r.json().then(function(j){ return {ok:r.ok,j:j}; }); })
   .then(function(s){
     if(s.ok && s.j && s.j.ok){
       goster(s.j.durum==='zaten'?c.zaten:c.tamam,false);
       if(s.j.durum!=='zaten') f.reset();
     } else {
       goster((s.j && s.j.message) || c.hata,true);
     }
   })
   .catch(function(){ goster(c.hata,true); })
   .then(function(){ if(btn) btn.disabled=false; });
 },true);
})();
