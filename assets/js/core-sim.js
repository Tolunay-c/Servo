/* Kaynak: _design/core-precisor.html <script> (içerik değiştirilmeden taşındı) */
(function(){
"use strict";
var $=function(id){return document.getElementById(id)};
var NS='http://www.w3.org/2000/svg';
var fmt=function(v,d){return v.toLocaleString('tr-TR',{minimumFractionDigits:d||0,maximumFractionDigits:d||0})};

/* ================= konfigürasyon tanımları =================
   mv : hareketli lazerler { ch:'full'|'top'|'bot', dir:'fwd'|'rev'|'symL'|'symR' }
   fx : merkezde sabit referans lazerleri
   plan: büyük simülasyonun adımları — her adım { pos:[mm|null,...], cut:[mm,...] }
=========================================================== */
var W=1200;                     /* örnek hat çalışma genişliği (mm) */
var VMAX=1000;                  /* mm/s */
var TSET=0.25;                  /* adım başı oturma payı (sn) */

var CFG=[
 {code:'K1', kind:'Tek Kanal', name:'Tek Kanal · 1 Hareketli Lazer',
  short:'1 kanalda 1 hareketli lazer', dual:false,
  fx:[], mv:[{ch:'full',dir:'fwd'}],
  chCount:1, fxPos:[], start:[40],
  plan:[{pos:[300],cut:[300]},{pos:[600],cut:[600]},{pos:[900],cut:[900]}],
  ch:'Tek kanal — gövde boyunca kesintisiz tek hareket bölgesi.',
  lz:'1 hareketli lazer (kırmızı).',
  mvTxt:'Lazer soldan sağa, reçetedeki konumlara sırayla gider.',
  use:'Tek kesim çizgisinin sırayla gösterildiği, az dilimli hatlar.',
  desc:'En yalın konfigürasyon. Tek lazer gövde boyunca soldan sağa ilerler ve reçetedeki her kesim konumunda durur. Dört dilim için üç ayrı adım gerekir; operatör her adımda bıçağı gösterilen çizgiye göre hizalar.'},

 {code:'K2', kind:'Tek Kanal', name:'Tek Kanal · 1 Sabit + 1 Hareketli',
  short:'1 kanalda 1 sabit + 1 hareketli lazer', dual:false,
  fx:[{ch:'full',pos:50}], mv:[{ch:'full',dir:'fwd'}],
  chCount:1, fxPos:[600], start:[40],
  plan:[{pos:[300],cut:[300]},{pos:[600],cut:[600]},{pos:[900],cut:[900]}],
  ch:'Tek kanal — sabit referans ayrı brakette, hareketli lazer ray boyunca serbest.',
  lz:'1 sabit referans lazeri + 1 hareketli lazer (kırmızı).',
  mvTxt:'Sabit lazer ayrı brakette merkezi sürekli gösterir; hareketli lazer merkez dahil her kademede durarak soldan sağa ilerler.',
  use:'Ortalama/merkez referansının sürekli görünmesi gereken hatlar.',
  desc:'Ayrı braketteki sabit lazer makine ortasını kalıcı olarak işaretler ve reçete boyunca hiç sönmez. Hareketli lazer merkez dahil her kesim konumuna sırayla uğrar; operatör her ölçüyü sabit referansa göre anında karşılaştırabildiği için simetri kayması daha kurulum sırasında yakalanır.'},

 {code:'K3', kind:'Çift Kanal', name:'Çift Kanal · 2 Hareketli Lazer',
  short:'2 kanalda 2 hareketli lazer', dual:true,
  fx:[], mv:[{ch:'top',dir:'fwd'},{ch:'bot',dir:'rev'}],
  chCount:2, fxPos:[], start:[40,1160],
  plan:[{pos:[200,1000],cut:[]},
        {pos:[400,800],cut:[]},
        {pos:[600,600],cut:[600]},
        {pos:[800,400],cut:[400,800]},
        {pos:[1000,200],cut:[200,1000]}],
  ch:'İki bağımsız kanal — üst ve alt hareket bölgeleri ayrıdır.',
  lz:'Her kanalda 1 hareketli lazer (kırmızı).',
  mvTxt:'Kanal 1 sol uçtan sağ uca, Kanal 2 sağ uçtan sol uca geçer; yollar ortada kesişir.',
  use:'Kesme ve geri sarma taraflarının aynı anda işaretlendiği, çok dilimli hatlar.',
  desc:'İki lazer zıt uçlardan başlar ve karşı uca kadar gider; yolları merkezde kesişir. Kanal 1 kesme tarafındaki bıçağı, Kanal 2 geri sarma tarafındaki bobin kenarını işaretler — bir kesim çizgisi ancak iki taraftan da işaretlendiğinde tamamlanmış sayılır. Merkez dahil altı dilimlik reçete beş adımda kurulur.'},

 {code:'K4', kind:'Çift Kanal', name:'Çift Kanal · 1 Sabit + 2 Hareketli',
  short:'2 kanalda 1 sabit + 2 hareketli lazer', dual:true,
  fx:[{ch:'top',pos:50}], mv:[{ch:'top',dir:'fwd'},{ch:'bot',dir:'rev'}],
  chCount:2, fxPos:[600], start:[40,1160],
  plan:[{pos:[200,1000],cut:[]},
        {pos:[400,800],cut:[]},
        {pos:[600,600],cut:[600]},
        {pos:[800,400],cut:[400,800]},
        {pos:[1000,200],cut:[200,1000]}],
  ch:'İki bağımsız kanal; merkez referansı ayrı brakette, rayları engellemez.',
  lz:'1 sabit referans lazeri + 2 hareketli lazer (kırmızı).',
  mvTxt:'Kanal 1 sol uçtan sağ uca, Kanal 2 sağ uçtan sol uca geçer; ayrı braketteki sabit lazer merkezi sürekli işaretler ve rayları engellemez.',
  use:'Çift taraflı işaretleme ile merkez referansının birlikte istendiği hatlar.',
  desc:'K3 ile aynı hareket, üstüne kalıcı merkez referansı. Hareketli lazerler zıt uçlardan başlayıp merkez dahil her kademede durarak karşı uca geçer; ayrı braketteki sabit lazer ise tüm döngü boyunca simetri eksenini gösterir. Merkez referansı, her ayar adımında karşılaştırma yapılmasını sağlar.'},

 {code:'K5', kind:'Simetrik', name:'Simetrik · 2 Hareketli Lazer',
  short:'1 kanalda simetrik hareketli 2 lazer', dual:false, sym:true,
  fx:[], mv:[{ch:'full',dir:'symL'},{ch:'full',dir:'symR'}],
  chCount:1, fxPos:[], start:[600,600],
  plan:[{pos:[480,720],cut:[480,720]},
        {pos:[240,960],cut:[240,960]}],
  ch:'Tek kanal — iki lazer aynı bölgede, merkeze göre eşlenik.',
  lz:'2 hareketli lazer (kırmızı), merkeze göre simetrik.',
  mvTxt:'Lazerler tam merkezde park hâlindedir; oradan eşit mesafede dışa doğru açılır.',
  use:'Merkeze göre simetrik dilim genişliklerinin ayarlandığı hatlar.',
  desc:'İki lazer merkezde bekler ve reçeteye göre içten dışa doğru, merkeze eşit mesafede açılır. Her adımda simetrik bir kesim çifti işaretlenir; iki kenarın hedef konumu aynı anda gösterilir.'},

 {code:'K6', kind:'Simetrik', name:'Simetrik · 1 Sabit + 2 Hareketli',
  short:'1 kanalda 1 sabit + 2 simetrik hareketli lazer', dual:false, sym:true,
  fx:[{ch:'full',pos:50}], mv:[{ch:'full',dir:'symL'},{ch:'full',dir:'symR'}],
  chCount:1, fxPos:[600], start:[600,600],
  plan:[{pos:[400,800],cut:[400,800]},
        {pos:[200,1000],cut:[200,1000]}],
  ch:'Tek kanal — merkezde sabit referans, iki yanda simetrik hareket.',
  lz:'1 sabit referans lazeri + 2 simetrik hareketli lazer (kırmızı).',
  mvTxt:'Sabit lazer merkezi işaretler; hareketli çift tam merkezden başlayıp dışa doğru eşlenik açılır.',
  use:'Merkez referansının sürekli görünmesi gereken simetrik dilimleme uygulamaları.',
  desc:'Merkezde sabit duran referans lazeri simetri eksenini kalıcı gösterir; iki hareketli lazer bu eksenin iki yanından içten dışa açılır. Operatör hem simetri eksenini hem kesim çiftlerini aynı anda görür — örnekteki hedef konumlar iki hareket adımında gösterilir.'}
];

/* ============ küçük kart animasyonu (3×2 matris) ============ */
var MINX=6, MAXX=94, CENTER=50, SYM=40;

function trackHTML(c){
  var h='<div class="grid-bg"></div><div class="gbar"></div><div class="ctr"></div>';
  if(c.dual){ h+='<div class="divider"></div><span class="chlab a">KANAL 1</span><span class="chlab b">KANAL 2</span>'; }
  c.fx.forEach(function(f){ h+='<span class="lz fx '+f.ch+'" style="left:'+f.pos+'%"></span>'; });
  c.mv.forEach(function(m){ h+='<span class="lz mv '+m.ch+'" data-dir="'+m.dir+'"></span>'; });
  return h;
}
function pct(dir,p){
  switch(dir){
    case 'fwd':  return MINX + p*(MAXX-MINX);
    case 'rev':  return MINX + (1-p)*(MAXX-MINX);
    case 'symL': return CENTER - p*SYM;
    case 'symR': return CENTER + p*SYM;
  }
  return CENTER;
}

var wrap=$('cfgwrap'), html='';
CFG.forEach(function(c,i){
  html+='<button class="cfgcard" type="button" data-i="'+i+'" aria-pressed="'+(i===0)+'">'+
        '<div class="hd"><span class="code">'+c.code+'</span><div><div class="tt">'+c.kind+'</div>'+
        '<div class="dd">'+c.short+'</div></div></div>'+
        '<div class="bd"><div class="trk">'+trackHTML(c)+'</div></div></button>';
});
wrap.innerHTML=html;

var t=0, cardPlay=true, speed=1, lastCard=0;
function cardFrame(ts){
  if(!lastCard) lastCard=ts;
  var dt=(ts-lastCard)/1000; lastCard=ts;
  if(cardPlay) t += dt*speed*0.9;
  var p=Math.abs(Math.sin(t));
  var els=document.querySelectorAll('.cfgcard .lz.mv[data-dir]');
  for(var i=0;i<els.length;i++){ els[i].style.left = pct(els[i].getAttribute('data-dir'),p).toFixed(2)+'%'; }
  requestAnimationFrame(cardFrame);
}
requestAnimationFrame(cardFrame);

$('play').addEventListener('click',function(){
  cardPlay=!cardPlay;
  this.textContent = cardPlay ? 'Durdur' : 'Oynat';
});
Array.prototype.forEach.call($('segSpeed').children,function(b){
  b.addEventListener('click',function(){
    speed=parseFloat(b.getAttribute('data-sp'));
    Array.prototype.forEach.call($('segSpeed').children,function(x){ x.setAttribute('aria-pressed', x===b); });
  });
});

/* ================= büyük simülasyon sahnesi ================= */
var svg=$('svg');
var L=58, R=42, SPAN=1000-L-R;
var X=function(mm){ return L + mm/W*SPAN; };

var RAIL1_Y=58, RAIL2_Y=94, RAIL_H=17;     /* dual: iki ray */
var RAIL_Y=66;                              /* tek kanal */
var BRK_H=9;                                /* sabit lazer braketi */
var WEB_Y=194, WEB_H=36;
var RULER_Y=252, TOTAL_Y=308;

function el(tag,attrs,txt){
  var e=document.createElementNS(NS,tag);
  for(var k in attrs) e.setAttribute(k,attrs[k]);
  if(txt!=null) e.textContent=txt;
  return e;
}

var cur=null, carts=[], beams=[], labels=[], blades={}, plan=[], timeline=[], totalT=0;

function buildScene(c){
  while(svg.firstChild) svg.removeChild(svg.firstChild);
  carts=[]; beams=[]; labels=[]; blades={};

  var defs=el('defs');
  var g=el('linearGradient',{id:'railg',x1:'0',y1:'0',x2:'0',y2:'1'});
  g.appendChild(el('stop',{offset:'0','stop-color':'#FFE873'}));
  g.appendChild(el('stop',{offset:'0.55','stop-color':'#FFD400'}));
  g.appendChild(el('stop',{offset:'1','stop-color':'#C9A700'}));
  defs.appendChild(g); svg.appendChild(defs);

  /* simetri ekseni */
  if(c.sym || c.fxPos.length){
    svg.appendChild(el('line',{x1:X(W/2),y1:16,x2:X(W/2),y2:WEB_Y+WEB_H,
      stroke:'#5B6C7E','stroke-width':1,'stroke-dasharray':'3 4'}));
    svg.appendChild(el('text',{x:X(W/2),y:10,'text-anchor':'middle',
      'font-family':'IBM Plex Mono, monospace','font-size':10,'letter-spacing':'1.4',
      fill:'#C9A700'}, c.sym?'SIMETRI EKSENI':'MERKEZ REFERANSI'));
  }

  /* sabit lazerler — ayrı braket, rayın arkasındaki düzlemde (raylardan önce çizilir) */
  var BRK_Y = ((c.chCount===2)? RAIL1_Y : RAIL_Y) - 30;
  c.fxPos.forEach(function(mm){
    svg.appendChild(el('rect',{x:X(0)+SPAN*0.02,y:BRK_Y,width:SPAN*0.96,height:BRK_H,rx:2,
      fill:'#16221B',stroke:'#2C4436','stroke-width':1}));
    svg.appendChild(el('text',{x:X(0)+SPAN*0.02,y:BRK_Y-6,'font-family':'IBM Plex Mono, monospace',
      'font-size':9,'letter-spacing':'1.2',fill:'#4E7A5C'},'SABIT LAZER BRAKETI'));
    svg.appendChild(el('line',{x1:X(mm),y1:BRK_Y+BRK_H,x2:X(mm),y2:WEB_Y,stroke:'#2FE05C','stroke-width':11,opacity:.14,'stroke-linecap':'round'}));
    svg.appendChild(el('line',{x1:X(mm),y1:BRK_Y+BRK_H,x2:X(mm),y2:WEB_Y,stroke:'#2FE05C','stroke-width':3.2}));
    svg.appendChild(el('rect',{x:X(mm)-10,y:BRK_Y-4,width:20,height:BRK_H+8,rx:3,fill:'#0B2415',stroke:'#2FE05C','stroke-width':1.5}));
    svg.appendChild(el('circle',{cx:X(mm),cy:BRK_Y+BRK_H/2,r:3,fill:'#2FE05C'}));
    svg.appendChild(el('text',{x:X(mm)+14,y:BRK_Y+BRK_H-1,'font-family':'IBM Plex Mono, monospace',
      'font-size':10,fill:'#2FE05C'}, fmt(mm)));
  });
  /* raylar */
  var rails=[];
  if(c.chCount===2){
    rails=[{y:RAIL1_Y,label:'KANAL 1'},{y:RAIL2_Y,label:'KANAL 2'}];
  } else {
    rails=[{y:RAIL_Y,label:'KANAL 1'}];
  }
  rails.forEach(function(r){
    svg.appendChild(el('rect',{x:X(0),y:r.y,width:SPAN,height:RAIL_H,rx:3,fill:'url(#railg)'}));
    svg.appendChild(el('rect',{x:X(0)+6,y:r.y+RAIL_H/2-2.5,width:SPAN-12,height:5,rx:2,fill:'#FFFCE8',opacity:.5}));
    svg.appendChild(el('text',{x:X(0),y:r.y-6,'font-family':'IBM Plex Mono, monospace','font-size':9,
      'letter-spacing':'1.2',fill:'#6C7E92'}, r.label));
  });

  /* malzeme (web) */
  svg.appendChild(el('rect',{x:X(0),y:WEB_Y,width:SPAN,height:WEB_H,fill:'#232E3A',stroke:'#31404F','stroke-width':1}));
  svg.appendChild(el('text',{x:X(0),y:WEB_Y+WEB_H+14,'text-anchor':'start',
    'font-family':'IBM Plex Mono, monospace','font-size':9,'letter-spacing':'1',fill:'#5B6C7E'},'ÇALIŞMA YÜZEYİ'));

  /* hareketli lazerler: ışın + araba + etiket */
  var n=c.mv.length;
  for(var i=0;i<n;i++){
    var ry = (c.chCount===2) ? (i===0?RAIL1_Y:RAIL2_Y) : RAIL_Y;
    var halo=el('line',{x1:0,y1:ry+RAIL_H,x2:0,y2:WEB_Y,stroke:'#FF2D20','stroke-width':10,opacity:.18,'stroke-linecap':'round'});
    var beam=el('line',{x1:0,y1:ry+RAIL_H,x2:0,y2:WEB_Y,stroke:'#FF4133','stroke-width':3.2});
    var cart=el('rect',{x:0,y:ry-4,width:22,height:RAIL_H+8,rx:3,fill:'#2B0F0C',stroke:'#FF2D20','stroke-width':1.5});
    var dot =el('circle',{cx:0,cy:ry+RAIL_H/2,r:3.5,fill:'#FF2D20'});
    var low = (c.chCount===2 && i===1);
    var lab =el('text',{x:0,y: low ? ry+RAIL_H+14 : ry-12,
      'text-anchor': low ? 'start' : 'middle',
      'font-family':'IBM Plex Mono, monospace','font-size':10,fill:'#FF6B5F'},'');
    lab.setAttribute('data-low', low?'1':'0');
    svg.appendChild(halo); svg.appendChild(beam); svg.appendChild(cart); svg.appendChild(dot); svg.appendChild(lab);
    beams.push([halo,beam]); carts.push({cart:cart,dot:dot,ry:ry}); labels.push(lab);
  }

  /* bıçak üçgenleri (tüm kesim konumları, başta soluk) */
  var allCuts=[];
  c.fxPos.forEach(function(m){ allCuts.push(m); });
  c.plan.forEach(function(s){ s.cut.forEach(function(m){ allCuts.push(m); }); });
  allCuts.forEach(function(mm){
    var x=X(mm), y=WEB_Y-3;
    var tri=el('path',{d:'M '+(x-7)+' '+(y-12)+' L '+(x+7)+' '+(y-12)+' L '+x+' '+y+' Z',
      fill:'none',stroke:'#4A5B70','stroke-width':1.5,'stroke-dasharray':'2 2'});
    svg.appendChild(tri);
    blades[mm]=tri;
  });

  /* ölçü cetveli */
  var edges=[0].concat(allCuts.slice().sort(function(a,b){return a-b;})).concat([W]);
  var uniq=[]; edges.forEach(function(v){ if(uniq.indexOf(v)<0) uniq.push(v); });
  uniq.sort(function(a,b){return a-b;});
  svg.appendChild(el('line',{x1:X(0),y1:RULER_Y,x2:X(W),y2:RULER_Y,stroke:'#33465A','stroke-width':1}));
  uniq.forEach(function(mm){
    svg.appendChild(el('line',{x1:X(mm),y1:RULER_Y-5,x2:X(mm),y2:RULER_Y+5,stroke:'#5B6C7E','stroke-width':1}));
  });
  for(var k=0;k<uniq.length-1;k++){
    var mid=(uniq[k]+uniq[k+1])/2, wdt=uniq[k+1]-uniq[k];
    svg.appendChild(el('text',{x:X(mid),y:RULER_Y+17,'text-anchor':'middle',
      'font-family':'IBM Plex Mono, monospace','font-size':10,fill:'#8494A6'}, fmt(wdt)+' mm'));
  }

  /* toplam genişlik */
  svg.appendChild(el('line',{x1:X(0),y1:TOTAL_Y,x2:X(W),y2:TOTAL_Y,stroke:'#33465A','stroke-width':1}));
  svg.appendChild(el('line',{x1:X(0),y1:TOTAL_Y-5,x2:X(0),y2:TOTAL_Y+5,stroke:'#33465A','stroke-width':1}));
  svg.appendChild(el('line',{x1:X(W),y1:TOTAL_Y-5,x2:X(W),y2:TOTAL_Y+5,stroke:'#33465A','stroke-width':1}));
  var tw=el('text',{x:X(W/2),y:TOTAL_Y-8,'text-anchor':'middle','font-family':'IBM Plex Mono, monospace',
    'font-size':10,'letter-spacing':'1.2',fill:'#6C7E92'},'ÇALIŞMA GENİŞLİĞİ '+fmt(W)+' MM');
  tw.setAttribute('style','paint-order:stroke');
  svg.appendChild(tw);
}

/* zaman çizelgesi kurulumu */
function buildTimeline(c){
  timeline=[]; totalT=0;
  var pos=c.start.slice();
  c.plan.forEach(function(step){
    var dist=0;
    step.pos.forEach(function(target,i){
      if(target==null) return;
      dist=Math.max(dist, Math.abs(target-pos[i]));
    });
    var moveT=dist/VMAX;
    timeline.push({type:'move', from:pos.slice(), to:step.pos.slice(), dur:moveT, cut:step.cut});
    timeline.push({type:'settle', dur:TSET, cut:step.cut});
    totalT += moveT + TSET;
    step.pos.forEach(function(target,i){ if(target!=null) pos[i]=target; });
  });
}

/* durum */
var simT=0, running=false, lastSim=0, done=false;

function setBlade(mm,on){
  var tri=blades[mm]; if(!tri) return;
  if(on){ tri.setAttribute('fill','#C9D2DC'); tri.setAttribute('stroke','#C9D2DC');
          tri.setAttribute('stroke-dasharray','none'); }
  else  { tri.setAttribute('fill','none'); tri.setAttribute('stroke','#4A5B70');
          tri.setAttribute('stroke-dasharray','2 2'); }
}

function applyState(tt){
  var c=cur, pos=c.start.slice(), acc=0, i;
  /* sabit lazer bıçakları hep yerinde */
  c.fxPos.forEach(function(mm){ setBlade(mm,true); });
  /* tüm hareketli bıçakları sıfırla */
  c.plan.forEach(function(s){ s.cut.forEach(function(mm){ setBlade(mm,false); }); });

  for(var k=0;k<timeline.length;k++){
    var seg=timeline[k];
    if(tt >= acc+seg.dur){
      if(seg.type==='move'){ seg.to.forEach(function(v,i){ if(v!=null) pos[i]=v; }); }
      if(seg.type==='settle'){ seg.cut.forEach(function(mm){ setBlade(mm,true); }); }
      acc += seg.dur;
      continue;
    }
    if(seg.type==='move'){
      var f=seg.dur>0 ? (tt-acc)/seg.dur : 1;
      f = f<0?0 : f>1?1 : f;
      var e = f<0.5 ? 2*f*f : 1-Math.pow(-2*f+2,2)/2;   /* easeInOutQuad */
      seg.to.forEach(function(v,i){ if(v!=null) pos[i]=seg.from[i]+(v-seg.from[i])*e; });
    }
    break;
  }

  for(i=0;i<carts.length;i++){
    var x=X(pos[i]);
    carts[i].cart.setAttribute('x', x-11);
    carts[i].dot.setAttribute('cx', x);
    beams[i][0].setAttribute('x1', x); beams[i][0].setAttribute('x2', x);
    beams[i][1].setAttribute('x1', x); beams[i][1].setAttribute('x2', x);
    labels[i].setAttribute('x', labels[i].getAttribute('data-low')==='1' ? x+15 : x);
    labels[i].textContent = fmt(Math.round(pos[i]));
  }
}

function updateStatus(){
  var st=$('stat'), T=$('statT'), D=$('statD');
  st.className='simstat'+(running?' run':(done?'':' idle'));
  if(running){ T.textContent='Lazer hareket ediyor'; D.textContent='Lazerler reçete konumlarına gidiyor'; }
  else if(done){ T.textContent='Döngü tamamlandı'; D.textContent='Örnek lazer hareketi tamamlandı; bıçak ayarı operatöre aittir'; }
  else { T.textContent='Hazır'; D.textContent="Kurulumu başlatmak için Tekrar Oynat'a basın"; }
}

function simFrame(ts){
  if(!lastSim) lastSim=ts;
  var dt=(ts-lastSim)/1000; lastSim=ts;
  if(running){
    simT += dt*speed;
    if(simT>=totalT){ simT=totalT; running=false; done=true; updateStatus(); }
    applyState(simT);
    $('pi').style.width = (totalT? simT/totalT*100 : 100).toFixed(1)+'%';
  }
  requestAnimationFrame(simFrame);
}
requestAnimationFrame(simFrame);

function select(i){
  cur=CFG[i];
  Array.prototype.forEach.call(wrap.children,function(b,k){ b.setAttribute('aria-pressed', k===i); });
  $('dCode').textContent=cur.code;
  $('dTitle').textContent=cur.name;
  $('dW').textContent=fmt(W)+' mm çalışma genişliği';
  $('dDesc').textContent=cur.desc;
  $('dCh').textContent=cur.ch;
  $('dLz').textContent=cur.lz;
  $('dMv').textContent=cur.mvTxt;
  $('dUse').textContent=cur.use;

  buildScene(cur);
  buildTimeline(cur);

  $('oN').innerHTML=(cur.mv.length+cur.fxPos.length)+'<small>adet</small>';
  $('oCh').innerHTML=cur.chCount+'<small>hat</small>';
  var cuts=[]; cur.fxPos.forEach(function(m){ if(cuts.indexOf(m)<0) cuts.push(m); });
  cur.plan.forEach(function(st){ st.cut.forEach(function(m){ if(cuts.indexOf(m)<0) cuts.push(m); }); });
  $('oSl').innerHTML=(cuts.length+1)+'<small>dilim</small>';
  $('oStep').innerHTML=cur.plan.length+'<small>adım</small>';
  $('oT').innerHTML=fmt(totalT,1)+'<small>sn</small>';

  reset(); play();
}
function reset(){ simT=0; running=false; done=false; applyState(0); $('pi').style.width='0%'; updateStatus(); }
function play(){ if(simT>=totalT) simT=0; running=true; done=false; updateStatus(); }

wrap.addEventListener('click',function(e){
  var b=e.target.closest('.cfgcard'); if(b) select(+b.getAttribute('data-i'));
});
$('rep').addEventListener('click',function(){ simT=0; done=false; play(); });
$('rst').addEventListener('click',reset);

select(0);
})();
