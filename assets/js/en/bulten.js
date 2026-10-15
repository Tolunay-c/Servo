/* Kaynak: servomotion-site-tum131026/en/core-precisor.html <script> (içerik değiştirilmeden taşındı) */
(function(){"use strict";
 var f=document.getElementById('nf'); if(!f) return;
 f.addEventListener('submit',function(e){e.preventDefault();
  var m=document.getElementById('nmail').value;
  document.getElementById('nnote').innerHTML='The form is not connected to a list yet. For now, please '+
   '<a href="mailto:info@servomotion.com.tr?subject=Newsletter&body='+encodeURIComponent(m)+
   '">write to us</a>.';});
})();
