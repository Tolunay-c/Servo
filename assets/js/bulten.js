/* Kaynak: _design/sozluk.html <script> (içerik değiştirilmeden taşındı) */
(function(){"use strict";
 var f=document.getElementById('nf'); if(!f) return;
 f.addEventListener('submit',function(e){e.preventDefault();
  var m=document.getElementById('nmail').value;
  document.getElementById('nnote').innerHTML='Form henüz bir listeye bağlı değil. Şimdilik '+
   '<a href="mailto:info@servomotion.com.tr?subject=B%C3%BClten%20%C3%BCyeli%C4%9Fi&body='+encodeURIComponent(m)+
   '">e-posta ile</a> yazabilirsiniz.';});
})();
