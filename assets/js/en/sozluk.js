/* Kaynak: servomotion-site-tum131026/en/glossary.html <script> (içerik değiştirilmeden taşındı) */
(function(){"use strict";
 var q=document.getElementById('q'),
     items=[].slice.call(document.querySelectorAll('.term')),
     cnt=document.getElementById('cnt'),
     empty=document.getElementById('empty');
 function norm(s){return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'');}
 items.forEach(function(el){ el.setAttribute('data-n', norm(el.getAttribute('data-t')||'')); });
 q.addEventListener('input',function(){
  var v=norm(q.value.trim()), n=0;
  items.forEach(function(el){
   var ok = !v || el.getAttribute('data-n').indexOf(v)>-1;
   if(ok){ el.removeAttribute('hidden'); n++; } else { el.setAttribute('hidden',''); }
  });
  cnt.textContent = n + (n===1 ? ' term' : ' terms');
  empty.hidden = n>0;
 });
})();
