/* ServoMotion: bülten formu, liste servisi bağlanana kadar hazır konulu e-posta (mailto) akışı açar.
   Tasarımın bülten/rehber script'lerine dokunmaz; belge düzeyindeki yakalama dinleyicisi onlardan önce çalışır.
   Metinler ve adres inc/enqueue.php'den window.SERVO_BULTEN olarak verilir. */
(function(){"use strict";
 var c=window.SERVO_BULTEN;
 if(!c || !c.eposta) return;
 document.addEventListener('submit',function(e){
   var f=e.target;
   if(!f || f.id!=='nf') return;
   e.stopImmediatePropagation(); e.preventDefault();
   var m=document.getElementById('nmail'), note=document.getElementById('nnote');
   if(!m || !m.value.trim() || (m.checkValidity && !m.checkValidity())){
     if(m){ m.setAttribute('aria-invalid','true'); m.focus(); }
     if(note){ note.setAttribute('role','alert'); note.textContent=c.gecersiz; }
     return;
   }
   m.removeAttribute('aria-invalid');
   var href='mailto:'+c.eposta+'?subject='+encodeURIComponent(c.konu)+'&body='+encodeURIComponent(c.govde.replace('%s',m.value.trim()));
   if(note){
     note.setAttribute('role','status'); note.textContent=c.acildi+' ';
     var a=document.createElement('a'); a.href=href; a.textContent=c.baglanti; note.appendChild(a);
   }
   window.location.href=href;
 },true);
})();
