/* Kaynak: _design/shuttle-precisor.html <script> (içerik değiştirilmeden taşındı) */
(function(){
"use strict";
var $=function(id){return document.getElementById(id)};
var num=function(v,d){var x=parseFloat(v);return isFinite(x)?x:d};
var fmt=function(v,d){d=(d===undefined)?1:d;return v.toLocaleString('tr-TR',{minimumFractionDigits:d,maximumFractionDigits:d})};
var fmt0=function(v){return v.toLocaleString('tr-TR',{maximumFractionDigits:0})};

/* mekik zamanlama sabitleri */
var VAP=800, VMV=500, T_PIN=0.75, T_SET=0.5, T_RET=0.7;

var mode='A', cur=[], plan=null, simT=0, playing=false, raf=null, lastT=0, speed=4;

/* sabit tasarım değerleri (mm) — arayüzde gösterilmez */
var KC=60;   /* araba genişliği */
var GAP=10;  /* servis payı */
var EM=80;   /* uç emniyet payı */
var PG=40;   /* rezerve alanı girişi */
var PC=5;    /* park adım payı */

function S(){
  var kc=KC, gap=GAP, em=EM, pg=PG, pc=PC;
  var L=Math.max(kc+1,num($('L').value,2400));
  /* N alanı gövdedeki toplam lazer adedidir */
  var n=Math.max(2,Math.round(num($('N').value,16)));
  var p=Math.max(1,num($('P').value,156));
  return {L:L,kc:kc,gap:gap,em:em,pg:pg,n:n,p:p,NT:n,pc:pc};
}
function calc(){
  var s=S();
  var p=(mode==='A')?(s.L-s.kc)/(s.n-1):s.p;
  var nRaw=(s.L-s.kc)/p+1;
  var nFit=Math.max(1,Math.floor(nRaw+1e-9));
  var want=(mode==='A')?s.n:nFit;
  var aktif=Math.max(1,Math.min(s.NT,want));
  var rezerve=Math.max(0,s.NT-aktif);
  var eksik=(mode==='A')?Math.max(0,s.n-s.NT):0;
  var n=aktif;
  var span=(aktif-1)*p+s.kc;
  var x=p-s.kc;
  var step=s.kc+s.pc;
  var mechEnd=rezerve>0?(s.L+s.pg+s.kc+(rezerve-1)*step):Math.max(s.L,span);
  var rail=Math.ceil((mechEnd+s.em)/50)*50;
  var nHard=Math.floor((s.L-s.kc)/s.kc)+1;
  var st,lb,dt;
  if(eksik>0){ st='bad'; lb='Yetersiz lazer';
    dt='Girilen adet gövdedeki toplam lazer sayısını '+eksik+' adet aşıyor. Toplam lazeri artırın.'; }
  else if(p<s.kc){ st='bad'; lb='Araba çarpışması';
    dt='Arabalar '+fmt(s.kc-p)+' mm örtüşüyor. Lazer adedini azaltın veya stroku büyütün.'; }
  else if(x<s.gap){ st='warn'; lb='Sınırda';
    dt='Arabalar arasında yalnızca '+fmt(x)+' mm kalıyor (servis payı '+fmt0(s.gap)+' mm).'; }
  else if(rezerve>0){ st='ok'; lb='Uygun · rezerveli';
    dt=aktif+' lazer aktif, '+rezerve+' lazer rezerve alanında bekliyor. Gereken ray boyu '+fmt0(rail)+' mm.'; }
  else if(mode==='B'&&Math.abs(nRaw-Math.round(nRaw))>0.02){ st='ok'; lb='Uygun · artık mesafe';
    dt=aktif+' lazer sığıyor; strokun '+fmt0(s.L-span)+' mm\'si kaplanmıyor. Aralığı '+fmt((s.L-s.kc)/(aktif-1))+' mm yaparsanız tam oturur.'; }
  else { st='ok'; lb='Uygun';
    dt='Arabalar arasında '+fmt(x)+' mm boşluk var. Bu stroka fiziksel olarak en fazla '+nHard+' lazer sığar.'; }
  return {p:p,n:n,aktif:aktif,rezerve:rezerve,eksik:eksik,step:step,mechEnd:mechEnd,
          x:x,span:span,rail:rail,nRaw:nRaw,nHard:nHard,state:st,label:lb,detail:dt,s:s};
}
function layout(r){
  var o=[],i,s=r.s;
  for(i=0;i<r.aktif;i++) o.push(s.kc/2+i*r.p);
  for(i=0;i<r.rezerve;i++) o.push(s.L+s.pg+s.kc/2+i*r.step);
  return o;
}
/* park: tüm arabalar sol uçta bitişik bekler */
function park(r){
  var o=[],i,step=r.s.kc+4;
  for(i=0;i<r.s.NT;i++) o.push(r.s.kc/2+i*step);
  return o;
}

/* mekik çizelgesi: her araba sırayla taşınır */
function schedule(from,to){
  var n=from.length, i, t=0, sx=0, ev=[], sp=[];
  var exp = to[n-1]>=from[n-1];
  var ord=[]; for(i=0;i<n;i++) ord.push(i);
  if(exp) ord.reverse();
  for(var q=0;q<ord.length;q++){
    i=ord[q];
    if(Math.abs(to[i]-from[i])<0.5) continue;
    var ta=Math.abs(from[i]-sx)/VAP+0.25;
    sp.push({t0:t,t1:t+ta,a:sx,b:from[i]}); t+=ta; sx=from[i];
    var p0=t; t+=T_PIN;
    var tm=Math.abs(to[i]-from[i])/VMV+T_SET, m0=t; t+=tm;
    sp.push({t0:m0,t1:m0+tm,a:sx,b:to[i]}); sx=to[i];
    var r0=t; t+=T_RET;
    ev.push({i:i,p0:p0,p1:p0+T_PIN,m0:m0,m1:m0+tm,r0:r0,r1:r0+T_RET,from:from[i],to:to[i]});
  }
  var th=Math.abs(sx)/VAP+0.25;
  sp.push({t0:t,t1:t+th,a:sx,b:0}); t+=th;
  return {total:ev.length?t:0,ev:ev,sp:sp,moved:ev.length};
}
function evAt(t){
  if(!plan) return null;
  for(var i=0;i<plan.ev.length;i++){ var e=plan.ev[i]; if(t>=e.p0-0.5&&t<e.r1) return e; }
  return null;
}
function carAt(i,t){
  if(!plan) return cur[i];
  var e=null,k;
  for(k=0;k<plan.ev.length;k++) if(plan.ev[k].i===i){ e=plan.ev[k]; break; }
  if(!e) return cur[i];
  if(t<=e.m0) return e.from;
  if(t>=e.m1) return e.to;
  return e.from+(e.to-e.from)*((t-e.m0)/(e.m1-e.m0));
}
function pinAt(t){
  var e=evAt(t); if(!e) return 0;
  if(t<e.p0) return 0;
  if(t<e.p1) return (t-e.p0)/T_PIN;
  if(t<e.r0) return 1;
  if(t<e.r1) return 1-(t-e.r0)/T_RET;
  return 0;
}
function shuttleAt(t){
  if(!plan) return 0;
  var last=0,i;
  for(i=0;i<plan.sp.length;i++){
    var s=plan.sp[i];
    if(t>=s.t1) last=s.b;
    else if(t>=s.t0) return s.a+(s.b-s.a)*((t-s.t0)/Math.max(0.001,s.t1-s.t0));
  }
  var e=evAt(t);
  if(e&&t>=e.p0&&t<e.m0) return e.from;
  return last;
}

/* ---------- ÇİZİM ---------- */
function draw(t){
  var r=calc(), s=r.s;
  var VW=1000, PAD=34;
  var W=Math.max(s.L,r.span,r.rail,r.mechEnd+s.kc);
  var SW=VW-PAD*2, mm=function(x){return PAD+x/W*SW};
  var VH=300;
  var busY=24,busH=17, midY=64,midH=11, carT=52,carB=104, appT=104,appB=124, shT=176,shB=196, matY=232,matH=22;
  var g='', i;
  var sx=shuttleAt(t), pe=pinAt(t), e=evAt(t);

  g+='<rect x="0" y="0" width="'+VW+'" height="'+VH+'" fill="#0E151D"/>';
  /* üst ray */
  g+='<rect x="'+(PAD-18)+'" y="'+busY+'" width="'+(SW+36)+'" height="'+busH+'" fill="#0F2432" stroke="#4FA3E3"/>';
  g+='<text x="'+(PAD-18)+'" y="'+(busY-6)+'" fill="#4FA3E3" font-size="11" font-family="IBM Plex Mono,monospace">ÜST RAY · +24 VDC BARA</text>';
  /* orta ray */
  g+='<rect x="'+(PAD-18)+'" y="'+midY+'" width="'+(SW+36)+'" height="'+midH+'" fill="#212C38" stroke="#31404F"/>';
  /* alt ray */
  g+='<rect x="'+(PAD-18)+'" y="'+shT+'" width="'+(SW+36)+'" height="'+(shB-shT)+'" fill="#1C2530" stroke="#3A4A5C"/>';
  g+='<text x="'+(PAD-18)+'" y="'+(shB+16)+'" fill="#F5A623" font-size="11" font-family="IBM Plex Mono,monospace">ALT RAY · SERVO MEKİK</text>';
  /* malzeme */
  g+='<rect x="'+PAD+'" y="'+matY+'" width="'+(mm(s.L)-PAD)+'" height="'+matH+'" fill="#232E3B" stroke="#33465A"/>';
  /* strok sonu */
  if(W>s.L+1){
    g+='<line x1="'+mm(s.L)+'" y1="'+(busY-4)+'" x2="'+mm(s.L)+'" y2="'+(matY+matH+4)+'" stroke="#F5A623" stroke-dasharray="4 5" stroke-width="1" opacity=".8"/>';
    g+='<text x="'+(mm(s.L)+6)+'" y="'+(busY-6)+'" fill="#F5A623" font-size="10.5" font-family="IBM Plex Mono,monospace">STROK SONU</text>';
  }
  /* arabalar */
  /* rezerve alanı */
  var rezNow=0;
  for(i=0;i<cur.length;i++) if(carAt(i,t)>s.L+1) rezNow++;
  if(W>s.L+s.pg*0.6){
    var rz0=mm(s.L+s.pg-s.kc*0.6), rz1=mm(W);
    g+='<rect x="'+rz0+'" y="'+(carT-10)+'" width="'+(rz1-rz0+16)+'" height="'+(shB-carT+20)+'" fill="#141A22" stroke="#F5A623" stroke-dasharray="4 4" stroke-width="1" opacity=".85"/>';
    g+='<text x="'+((rz0+rz1)/2)+'" y="'+(shB+24)+'" fill="#F5A623" font-size="10.5" font-family="IBM Plex Mono,monospace" text-anchor="middle">REZERVE ALANI · '+rezNow+' LAZER</text>';
  }
  var cw=Math.max(6,s.kc/W*SW);
  for(i=0;i<cur.length;i++){
    var cx=mm(carAt(i,t));
    var isRez=(carAt(i,t)>s.L+1);
    var act=(e&&e.i===i), open=act&&pe>0.75;
    var fill=open?'#5C4A20':(isRez?'#2E3A48':'#3D4C5E'), edge=open?'#F5A623':(isRez?'#414F60':'#66798F');
    g+='<rect x="'+(cx-4)+'" y="'+(busY+3)+'" width="8" height="'+(busH-6)+'" fill="#24505C" stroke="#5FD3D0" stroke-width="1"/>';
    g+='<rect x="'+(cx-2.5)+'" y="'+(busY+busH)+'" width="5" height="'+(carT-busY-busH)+'" fill="#1B3A44" stroke="#5FD3D0" stroke-width=".8"/>';
    g+='<rect x="'+(cx-cw/2)+'" y="'+carT+'" width="'+cw+'" height="'+(carB-carT)+'" rx="2" fill="'+fill+'" stroke="'+edge+'" stroke-width="1.3"/>';
    g+='<rect x="'+(cx-cw/2+2)+'" y="'+(midY-2)+'" width="'+Math.max(2,cw-4)+'" height="5" fill="'+(open?'#F5A623':'#33D183')+'"/>';
    g+='<rect x="'+(cx-cw*0.30)+'" y="'+appT+'" width="'+(cw*0.60)+'" height="'+(appB-appT)+'" rx="2" fill="'+(isRez?'#333F4D':'#55637A')+'" stroke="#8496AC" stroke-width="1"/>';
    if(isRez){
      g+='<text x="'+cx+'" y="'+(appB+14)+'" fill="#5E718A" font-size="9" font-family="IBM Plex Mono,monospace" text-anchor="middle">REZ</text>';
    } else {
      /* lazer çizgisi: mekik rayının önünden geçer — ray hizasında kesilir */
      g+='<rect x="'+(cx-1.6)+'" y="'+appB+'" width="3.2" height="'+(shT-appB-3)+'" fill="#FF2D20"/>';
      g+='<rect x="'+(cx-6)+'" y="'+appB+'" width="12" height="'+(shT-appB-3)+'" fill="#FF2D20" opacity=".12"/>';
      g+='<rect x="'+(cx-1.6)+'" y="'+(shB+3)+'" width="3.2" height="'+(matY+matH-shB-3)+'" fill="#FF2D20"/>';
      g+='<rect x="'+(cx-6)+'" y="'+(shB+3)+'" width="12" height="'+(matY+matH-shB-3)+'" fill="#FF2D20" opacity=".12"/>';
    }
  }
  /* mekik */
  var shw=Math.max(26,cw*1.5);
  var msx=mm(sx);
  g+='<rect x="'+(msx-shw/2)+'" y="'+shT+'" width="'+shw+'" height="'+(shB-shT)+'" rx="2" fill="#4A3A12" stroke="#F5A623" stroke-width="1.4"/>';
  var pl=(shT-appB-4)*pe;
  if(pl>0.5) g+='<rect x="'+(msx-3)+'" y="'+(shT-pl)+'" width="6" height="'+pl+'" fill="'+(pe>0.75?'#F5A623':'#B08430')+'" stroke="#E0B060" stroke-width=".8"/>';
  g+='<text x="'+msx+'" y="'+(shB-6)+'" fill="#F5A623" font-size="10.5" font-family="IBM Plex Mono,monospace" text-anchor="middle">MEKİK</text>';
  /* ölçü: p */
  if(cur.length>=2 && Math.abs(mm(carAt(1,t))-mm(carAt(0,t)))>64){
    var a=mm(carAt(0,t)), b=mm(carAt(1,t)), dy=carT-12;
    g+='<line x1="'+a+'" y1="'+dy+'" x2="'+b+'" y2="'+dy+'" stroke="#FFD400" stroke-width="1"/>';
    g+='<line x1="'+a+'" y1="'+(dy-4)+'" x2="'+a+'" y2="'+(dy+4)+'" stroke="#FFD400" stroke-width="1"/>';
    g+='<line x1="'+b+'" y1="'+(dy-4)+'" x2="'+b+'" y2="'+(dy+4)+'" stroke="#FFD400" stroke-width="1"/>';
    g+='<text x="'+((a+b)/2)+'" y="'+(dy-5)+'" fill="#FFD400" font-size="12" font-family="Barlow Condensed,sans-serif" font-weight="600" text-anchor="middle">p = '+fmt(r.p)+' mm</text>';
  }
  g+='<text x="'+(PAD+SW/2)+'" y="'+(VH-8)+'" fill="#93A4B7" font-size="11" font-family="IBM Plex Mono,monospace" text-anchor="middle">STROK '+fmt0(s.L)+' mm · '+r.aktif+' AKTİF'+(r.rezerve>0?' · '+r.rezerve+' REZERVE':'')+' · GEREKEN RAY '+fmt0(r.rail)+' mm</text>';

  $('stage').innerHTML='<svg viewBox="0 0 '+VW+' '+VH+'" role="img" aria-label="Mekik sistemi konfigürasyon şeması">'+g+'</svg>';
}

function stepText(t){
  if(!plan||plan.total<=0) return ['Yerleşim hazır','Arabalar hedef konumlarında'];
  if(t<=0) return ['Hazır', plan.moved+' araba taşınacak'];
  if(t>=plan.total) return ['Kurulum tamamlandı','Mekik çekildi — tüm çizgiler yerinde kilitli'];
  var e=evAt(t);
  if(!e) return ['Mekik yaklaşıyor','Alt rayda bir sonraki arabaya gidiyor'];
  var no=e.i+1;
  if(t<e.p1) return [no+'. araba · Pim yükseliyor','Kavrama diline giriyor, fren açılıyor'];
  if(t<e.m1) return [no+'. araba · Servo taşıyor','Hedef '+fmt(e.to,1)+' mm · yol '+fmt0(Math.abs(e.to-e.from))+' mm'];
  return [no+'. araba · Pim iniyor','Yay freni kapandı, araba kilitlendi'];
}

function render(t){
  var r=calc();
  $('oP').innerHTML=fmt(r.p)+'<small>mm</small>';
  $('oN').innerHTML=r.aktif+'<small>/ '+r.rezerve+'</small>';
  $('oX').innerHTML=fmt(r.x)+'<small>mm</small>';
  $('oR').innerHTML=fmt0(r.rail)+'<small>mm</small>';
  $('stat').className='status s-'+r.state;
  $('stat').innerHTML='<span class="dot"></span><span>'+r.label+'</span><span class="detail">'+r.detail+'</span>';
  $('pi').style.width=(plan&&plan.total?Math.min(100,t/plan.total*100):0)+'%';
  var s2=stepText(t);
  $('nowT').textContent=s2[0]; $('nowD').textContent=s2[1];
  var fin=(plan&&plan.total>0&&t>=plan.total);
  $('now').style.color=fin?'#33D183':'#F5A623';
  $('now').querySelector('.dot').style.background=fin?'#33D183':'#F5A623';
}
function setN(v){ v=Math.max(2,Math.round(v)); $('N').value=v; $('Nr').max=Math.max(60,v); $('Nr').value=Math.min(v,+$('Nr').max); }
function setP(v){ $('P').value=Math.round(v*10)/10; $('Pr').max=Math.max(600,Math.ceil(v)); $('Pr').value=Math.min(v,+$('Pr').max); }
function setL(v){ $('L').value=v; $('Lr').value=Math.min(v,+$('Lr').max); }
function setMode(m){
  mode=m;
  var a=(m==='A');
  $('mA').setAttribute('aria-pressed',a?'true':'false');
  $('mB').setAttribute('aria-pressed',a?'false':'true');
  $('fN').className='fld'+(a?'':' calc'); $('fP').className='fld'+(a?' calc':'');
  $('bN').dataset.role=a?'in':''; $('bP').dataset.role=a?'':'in';
  $('bN').textContent=a?'girdi · adet':'toplam · adet';
  $('bP').textContent=a?'hesaplanan · mm':'girdi · mm';
  $('mnote').innerHTML=a
    ? 'Gövdedeki lazer adedini siz girersiniz, çizgiler arası mesafe buna göre hesaplanır.'
    : 'Çizgiler arası mesafeyi siz girersiniz; stroka sığmayan lazerler rezerve alanında bekler.';
}
function rebuild(reset){
  stop();
  var r=calc();
  if(mode==='A') setP(r.p);
  var tgt=layout(r);
  if(reset||cur.length!==tgt.length) cur=park(r);
  plan=schedule(cur.slice(),tgt);
  simT=0;
  $('go').textContent='Kurulumu Oynat';
  draw(0); render(0);
}
function stop(){ playing=false; if(raf){cancelAnimationFrame(raf);raf=null;} }
function loop(now){
  var dt=(now-lastT)/1000*speed; lastT=now;
  simT=Math.min(plan.total,simT+dt);
  draw(simT); render(simT);
  if(simT<plan.total&&playing) raf=requestAnimationFrame(loop);
  else{
    playing=false; raf=null;
    if(simT>=plan.total){ cur=layout(calc()); plan=schedule(cur.slice(),cur.slice()); draw(0); }
    $('go').textContent='Tekrar Oynat';
  }
}

/* ---------- olaylar ---------- */
$('mA').addEventListener('click',function(){ if(mode==='A')return; setMode('A'); rebuild(true); });
$('mB').addEventListener('click',function(){ if(mode==='B')return; var r=calc(); setP(r.p); setMode('B'); rebuild(true); });
var link=function(rg,ip,after){
  rg.addEventListener('input',function(){ ip.value=rg.value; if(after)after(); rebuild(true); });
  ip.addEventListener('input',function(){ rg.value=ip.value; if(after)after(); rebuild(true); });
};
link($('Lr'),$('L'));
link($('Nr'),$('N'),function(){ if(mode!=='A') setMode('A'); });
link($('Pr'),$('P'),function(){ if(mode!=='B') setMode('B'); });
$('go').addEventListener('click',function(){
  if(playing){ stop(); $('go').textContent='Devam'; return; }
  if(!plan||plan.total<=0){ rebuild(true); if(!plan||plan.total<=0) return; }
  if(simT>=plan.total){ var r=calc(); cur=park(r); plan=schedule(cur.slice(),layout(r)); simT=0; }
  playing=true; lastT=performance.now(); $('go').textContent='Duraklat';
  raf=requestAnimationFrame(loop);
});
$('rst').addEventListener('click',function(){ rebuild(true); });
Array.prototype.forEach.call($('segSpeed').querySelectorAll('button'),function(b){
  b.addEventListener('click',function(){
    speed=+b.dataset.s;
    Array.prototype.forEach.call($('segSpeed').querySelectorAll('button'),function(x){
      x.setAttribute('aria-pressed', x===b?'true':'false');});
  });
});
window.addEventListener('resize',function(){ draw(simT); });

setMode('A');
setL(2400); setN(16);
rebuild(true);
})();
