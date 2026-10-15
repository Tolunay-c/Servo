/* Kaynak: servomotion-site-tum131026/en/guide-accuracy.html <script> (içerik değiştirilmeden taşındı) */
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
  document.getElementById('nnote').innerHTML='The form is not connected to a list yet. For now you can '+
   '<a href="mailto:info@servomotion.com.tr?subject=Guide%20subscription&body='+encodeURIComponent(m)+
   '">write to us by email</a>.';});
})();
