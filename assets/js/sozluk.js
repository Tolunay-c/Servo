/* Kaynak: _design/sozluk.html <script> (içerik değiştirilmeden taşındı) */
(function(){"use strict";
 var q=document.getElementById('q'), items=[].slice.call(document.querySelectorAll('.term')),
     cnt=document.getElementById('cnt'), empty=document.getElementById('empty');
 function norm(s){return s.toLocaleLowerCase('tr').replace(/\u0307/g,'')
   .replace(/ı/g,'i').replace(/ğ/g,'g').replace(/ü/g,'u').replace(/ş/g,'s').replace(/ö/g,'o').replace(/ç/g,'c');}
 items.forEach(function(el){ el.setAttribute('data-n', norm(el.getAttribute('data-t'))); });
 q.addEventListener('input',function(){
   var v=norm(q.value.trim()), n=0;
   items.forEach(function(el){
     var ok = !v || el.getAttribute('data-n').indexOf(v)>-1;
     if(ok){ el.removeAttribute('hidden'); n++; } else { el.setAttribute('hidden',''); }
   });
   cnt.textContent = n + ' terim';
   empty.hidden = n>0;
 });
})();
