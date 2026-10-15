/* Kaynak: servomotion-site-tum131026/en/guide.html <script> (içerik değiştirilmeden taşındı) */
(function(){
 "use strict";
 var cards = Array.prototype.slice.call(document.querySelectorAll('.story,.featured'));
 var note  = document.getElementById('emptyNote');
 var count = document.getElementById('gcount');
 var q     = document.getElementById('q');
 var state = {cat:'all', area:'all', text:''};

 function apply(){
   var shown = 0;
   cards.forEach(function(c){
     var okCat  = state.cat  === 'all' || c.getAttribute('data-category') === state.cat;
     var okArea = state.area === 'all' || (' '+c.getAttribute('data-areas')+' ').indexOf(' '+state.area+' ') > -1;
     var okText = !state.text || c.getAttribute('data-text').indexOf(state.text) > -1;
     var ok = okCat && okArea && okText;
     if (ok) { c.removeAttribute('hidden'); shown++; } else { c.setAttribute('hidden',''); }
   });
   if (note)  note.hidden = shown > 0;
   if (count) count.innerHTML = '<b>' + shown + '</b> articles listed';
 }

 function bind(attr, key){
   var group = Array.prototype.slice.call(document.querySelectorAll('[data-' + attr + ']'));
   group.forEach(function(b){
     b.addEventListener('click', function(){
       group.forEach(function(x){ x.setAttribute('aria-pressed', String(x === b)); });
       state[key] = b.getAttribute('data-' + attr);
       apply();
     });
   });
 }
 bind('cat','cat'); bind('area','area');
 if (q) q.addEventListener('input', function(){
   state.text = q.value.trim().toLowerCase().replace(/̇/g,'');
   apply();
 });
 apply();
})();

(function(){"use strict";
 var f=document.getElementById('nf'); if(!f) return;
 f.addEventListener('submit',function(e){e.preventDefault();
  var m=document.getElementById('nmail').value;
  document.getElementById('nnote').innerHTML='The form is not connected to a list yet. For now you can '+
   '<a href="mailto:info@servomotion.com.tr?subject=Guide%20subscription&body='+encodeURIComponent(m)+
   '">write to us by email</a>.';});
})();
