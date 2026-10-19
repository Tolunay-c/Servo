/* ServoMotion: application summary submit validation (English version of iletisim-dogrula.js). Leaves the design scripts untouched; a capture listener bound first stops sending before it starts when required fields are empty. */
(function(){"use strict";
 var MSG_LINES='Please enter the number of lines that must be visible at the same time.', MSG_CONTACT='Please enter a contact person and contact details so we can get back to you.', MSG_BOTH='Before sending, please enter the number of lines and a contact person with contact details.';
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
