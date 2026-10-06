/* Kaynak: _design/rehber-hassasiyet.html <script> (içerik değiştirilmeden taşındı) */
(function(){"use strict";
 var links=Array.prototype.slice.call(document.querySelectorAll('.toc a'));
 var heads=links.map(function(l){return document.getElementById(l.getAttribute('href').slice(1));});
 function onScroll(){
   var y=window.scrollY+120, cur=0;
   heads.forEach(function(h,i){ if(h && h.offsetTop<=y) cur=i; });
   links.forEach(function(l,i){ l.classList.toggle('on', i===cur); });
 }
 window.addEventListener('scroll',onScroll,{passive:true}); onScroll();
})();

(function(){"use strict";
 var f=document.getElementById('nf'); if(!f) return;
 f.addEventListener('submit',function(e){e.preventDefault();
  var m=document.getElementById('nmail').value;
  document.getElementById('nnote').innerHTML='Form henüz bir listeye bağlı değil. Şimdilik '+
   '<a href="mailto:info@servomotion.com.tr?subject=Rehber%20aboneli%C4%9Fi&body='+encodeURIComponent(m)+
   '">e-posta ile</a> yazabilirsiniz.';});
})();
