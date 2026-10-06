/* Kaynak: _design/rehber-hassasiyet.html <script> (içerik değiştirilmeden taşındı) */
(function(){"use strict";
 var drops=[].slice.call(document.querySelectorAll('.navdrop'));
 drops.forEach(function(d){
   var btn=d.querySelector('.navtop');
   if(!btn) return;
   btn.addEventListener('click',function(e){
     e.preventDefault();
     var open=d.classList.contains('open');
     drops.forEach(function(x){x.classList.remove('open');x.querySelector('.navtop').setAttribute('aria-expanded','false');});
     if(!open){ d.classList.add('open'); btn.setAttribute('aria-expanded','true'); }
   });
 });
 document.addEventListener('click',function(e){
   if(e.target.closest('.navdrop')) return;
   drops.forEach(function(x){x.classList.remove('open');x.querySelector('.navtop').setAttribute('aria-expanded','false');});
 });
 document.addEventListener('keydown',function(e){ if(e.key==='Escape')
   drops.forEach(function(x){x.classList.remove('open');x.querySelector('.navtop').setAttribute('aria-expanded','false');}); });
})();
