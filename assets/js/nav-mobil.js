/* Dar ekran hamburger menüsü: aria-expanded, Esc ile kapanma, açıkken sayfa kaydırması kilidi. */
(function(){"use strict";
 var nav0=document.querySelector('.nav');
 var root=document.documentElement;
 function olc(){ if(nav0) root.style.setProperty('--servo-header-h',nav0.getBoundingClientRect().height+'px'); }
 if(nav0){ olc(); window.addEventListener('resize',olc); if(window.ResizeObserver) new ResizeObserver(olc).observe(nav0); }
 var btn=document.querySelector('.navtoggle');
 if(!btn) return;
 var nav=btn.closest('.nav');
 var panel=document.getElementById(btn.getAttribute('aria-controls'));
 if(!nav||!panel) return;
 var mq=window.matchMedia('(max-width:1180px)');
 function set(open){
   nav.classList.toggle('nav-acik',open);
   root.classList.toggle('nav-acik',open);
   btn.setAttribute('aria-expanded',open?'true':'false');
   btn.setAttribute('aria-label',btn.getAttribute(open?'data-kapat':'data-ac')||btn.getAttribute('aria-label'));
 }
 btn.addEventListener('click',function(){ set(btn.getAttribute('aria-expanded')!=='true'); });
 document.addEventListener('keydown',function(e){
   if(e.key==='Escape'&&btn.getAttribute('aria-expanded')==='true'){ set(false); btn.focus(); }
 });
 panel.addEventListener('click',function(e){ if(e.target.closest('a')) set(false); });
 function genis(){ if(!mq.matches) set(false); }
 if(mq.addEventListener) mq.addEventListener('change',genis); else mq.addListener(genis);
})();
