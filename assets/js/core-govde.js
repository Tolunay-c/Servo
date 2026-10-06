/* Kaynak: _design/core-precisor.html <script> (içerik değiştirilmeden taşındı) */
(function(){
"use strict";
var $=function(id){return document.getElementById(id)};
var fmt=function(v){return Math.round(v).toLocaleString('tr-TR')};

var SIZES=[600,1200,1800,2600,3300,4000];
var LMAX=SIZES[SIZES.length-1];
var idx=3, auto=null;

/* merdiven */
var lad=$('ladder'), h='';
SIZES.forEach(function(L,i){
  h+='<button class="lrow" type="button" data-i="'+i+'" aria-pressed="'+(i===idx)+'">'+
     '<span class="ll">'+fmt(L)+' mm</span>'+
     '<span class="ltrack"><span class="lbar" style="width:'+(L/LMAX*100).toFixed(2)+'%">'+
     '<span class="lin" data-i="'+i+'"></span></span></span></button>';
});
h+='<div class="lscale"><span>'+fmt(SIZES[0])+' mm</span><span>'+fmt(LMAX/2)+' mm</span><span>'+fmt(LMAX)+' mm</span></div>';
lad.innerHTML=h;

var EM=80;   /* uç emniyet payı, mm (her iki uçta) */
var AP=45;   /* montaj aparatı genişliği, mm */
function em(){ return EM; }
function ap(){ return AP; }

function paint(){
  var L=SIZES[idx], E=em();
  var stroke=Math.max(0,L-2*E);

  /* merdiven */
  Array.prototype.forEach.call(lad.querySelectorAll('.lrow'),function(b,i){
    b.setAttribute('aria-pressed', i===idx);
    var Li=SIZES[i], si=Math.max(0,Li-2*E);
    var inb=b.querySelector('.lin');
    inb.style.left=(E/Li*100).toFixed(2)+'%';
    inb.style.width=(si/Li*100).toFixed(2)+'%';
  });

  /* profil */
  var wPct=L/LMAX*100;
  $('pbody').style.width=wPct.toFixed(2)+'%';
  $('pzone').style.left=(E/L*100).toFixed(2)+'%';
  $('pzone').style.width=(stroke/L*100).toFixed(2)+'%';
  $('pcart').style.left=(E/L*100+ (stroke/L*100)*0.5).toFixed(2)+'%';

  /* ölçü çizgileri (yüzde, sahne genişliğine göre) */
  $('dTot').style.left='0%'; $('dTot').style.width=wPct.toFixed(2)+'%';
  $('lTot').style.left=(wPct/2).toFixed(2)+'%';
  $('lTot').textContent='GÖVDE '+fmt(L)+' mm';

  var sL=wPct*(E/L), sW=wPct*(stroke/L);
  $('dStr').style.left=sL.toFixed(2)+'%'; $('dStr').style.width=sW.toFixed(2)+'%';
  $('lStr').style.left=(sL+sW/2).toFixed(2)+'%';
  var tight = wPct < 34;
  $('lMl').style.left=(sL/2).toFixed(2)+'%'; $('lMl').textContent=fmt(E);
  $('lMr').style.left=(sL+sW+sL/2).toFixed(2)+'%'; $('lMr').textContent=fmt(E);
  $('lMl').style.opacity = tight ? 0 : 1;
  $('lMr').style.opacity = tight ? 0 : 1;
  $('lStr').textContent = tight ? ('STROK '+fmt(stroke)+' mm') : ('FAYDALI STROK '+fmt(stroke)+' mm · UÇ PAYI '+fmt(E)+' mm');

  /* başlık */
  $('hCode').textContent='L'+(idx+1);
  $('hW').textContent=fmt(L)+' mm';
  $('hStep').textContent=(idx+1)+' / '+SIZES.length;
}

function go(i){ idx=(i+SIZES.length)%SIZES.length; paint(); }
lad.addEventListener('click',function(e){
  var b=e.target.closest('.lrow'); if(b){ stopAuto(); go(+b.getAttribute('data-i')); }
});
$('hPrev').addEventListener('click',function(){ stopAuto(); go(idx-1); });
$('hNext').addEventListener('click',function(){ stopAuto(); go(idx+1); });

function stopAuto(){ if(auto){ clearInterval(auto); auto=null; $('hAuto').textContent='Adım Adım Göster'; } }
$('hAuto').addEventListener('click',function(){
  if(auto){ stopAuto(); return; }
  go(0); this.textContent='Durdur';
  auto=setInterval(function(){
    if(idx>=SIZES.length-1){ go(0); } else { go(idx+1); }
  },1500);
});

paint();
})();
