/* ServoMotion: uygulama özeti gönderim doğrulaması. Tasarımın iletisim-ozet.js / iletisim-gonder.js dosyalarına dokunmaz;
   aynı düğmelere önce bağlanan yakalama (capture) dinleyicisiyle, eksik alan varsa gönderimi başlamadan durdurur. */
(function(){"use strict";
 var MSG_LINES='Lütfen aynı anda görünmesi gereken çizgi sayısını girin.', MSG_CONTACT='Lütfen size dönüş yapabilmemiz için ilgili kişi ve iletişim bilgisini girin.', MSG_BOTH='Göndermeden önce çizgi sayısını ve ilgili kişi ile iletişim bilgisini girin.';
 var mail=document.getElementById('b-mail'), wa=document.getElementById('b-wa'), note=document.getElementById('b-note');
 var cizgi=document.getElementById('f-cizgi'), kisi=document.getElementById('f-kisi');
 if(!mail || !note || !cizgi || !kisi) return;

 var err=document.createElement('p');
 err.id='b-err'; err.className='hint'; err.setAttribute('role','alert'); err.hidden=true;
 err.style.cssText='color:#b3261e;font-weight:600;margin:0 0 10px';
 note.parentNode.insertBefore(err,note);

 function clear(){
   err.hidden=true; err.textContent='';
   [cizgi,kisi].forEach(function(f){ f.removeAttribute('aria-invalid'); f.removeAttribute('aria-describedby'); });
 }
 function fail(msg,fields){
   err.textContent=msg; err.hidden=false;
   fields.forEach(function(f){ f.setAttribute('aria-invalid','true'); f.setAttribute('aria-describedby','b-err'); });
   fields[0].focus();
 }
 function check(e){
   clear();
   var n=parseInt(cizgi.value,10)||0, bad=[];
   if(n<1) bad.push(cizgi);
   /* Sunucuya gönderimde (uç nokta varsa e-posta düğmesi) iletişim bilgisi de zorunludur. */
   if(e.currentTarget===mail && window.SERVOMOTION_FORM_ENDPOINT && !kisi.value.trim()) bad.push(kisi);
   if(!bad.length) return;
   e.stopImmediatePropagation(); e.preventDefault();
   fail(bad.length>1 ? MSG_BOTH : (bad[0]===cizgi ? MSG_LINES : MSG_CONTACT),bad);
 }
 mail.addEventListener('click',check,true);
 if(wa) wa.addEventListener('click',check,true);
 [cizgi,kisi].forEach(function(f){ f.addEventListener('input',clear); });
})();
