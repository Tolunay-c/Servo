/* Kaynak: servomotion-site-tum131026/en/contact.html <script> (içerik değiştirilmeden taşındı) */
(function(){"use strict";
 var ids=['f-hat','f-cizgi','f-strok','f-mesafe','f-sik','f-montaj','f-firma','f-kisi'];
 var el={}; ids.forEach(function(i){el[i]=document.getElementById(i);});
 var vT=document.getElementById('v-title'), vD=document.getElementById('v-desc'), vS=document.getElementById('v-sum');
 var boxes=Array.prototype.slice.call(document.querySelectorAll('.chk input'));
 function verdict(n){
   if(!n) return ['ENTER THE NUMBER OF LASER LINES','How many laser reference lines must be visible at the same time is the most decisive difference between the two systems. Fill in that field and the direction appears here.'];
   if(n<=4) return ['CORE PRECISOR','With '+n+' simultaneous lines the enclosed Core Precisor is in scope. Which of the six movement configurations applies depends on whether the lines move independently or symmetrically.'];
   if(n<=6) return ['CLOSE TO THE CORE LIMIT',n+' lines is near the upper limit of Core Precisor. If your recipe variety is likely to grow, Shuttle Precisor should also be considered; the decision is made during the site survey.'];
   return ['SHUTTLE PRECISOR','If '+n+' lines must be visible at the same time, the shuttle architecture — one servo axis positioning many laser carriages — is the fit. Rail assembly length is calculated including the reserve area.'];
 }
 function build(){
   var n=parseInt(el['f-cizgi'].value,10)||0;
   var v=verdict(n); vT.textContent=v[0]; vD.textContent=v[1];
   var L=[];
   L.push('SERVOMOTION — APPLICATION SUMMARY');
   L.push('----------------------------------------');
   if(el['f-firma'].value) L.push('Company          : '+el['f-firma'].value);
   if(el['f-kisi'].value)  L.push('Contact          : '+el['f-kisi'].value);
   if(el['f-hat'].value)   L.push('Line / process   : '+el['f-hat'].value);
   if(n)                   L.push('Simultaneous     : '+n+' lines');
   if(el['f-strok'].value) L.push('Travel range     : '+el['f-strok'].value+' mm');
   if(el['f-mesafe'].value)L.push('Working distance : '+el['f-mesafe'].value+' mm');
   if(el['f-sik'].value)   L.push('Recipe changes   : '+el['f-sik'].value);
   if(el['f-montaj'].value)L.push('Mounting         : '+el['f-montaj'].value);
   var haz=boxes.filter(function(b){return b.checked;}).map(function(b){return b.getAttribute('data-h');});
   if(haz.length) L.push('Available info   : '+haz.join(', '));
   var strok=parseInt(el['f-strok'].value,10)||0;
   if(n){ L.push(''); L.push('First assessment : '+v[0]); }
   if(strok>4000) L.push('Note             : bodies longer than 4,000 mm are designed on request.');
   L.push('');
   L.push('Please get back to us to evaluate the application together.');
   var txt=L.join('\n');
   vS.textContent = (L.length>4) ? txt : 'The summary is built here as you fill in the fields.';
   return txt;
 }
 ids.forEach(function(i){ el[i].addEventListener('input',build); el[i].addEventListener('change',build); });
 boxes.forEach(function(b){ b.addEventListener('change',build); });
 var note=document.getElementById('b-note'), noteHTML=note.innerHTML;
 function flash(m){ note.textContent=m; setTimeout(function(){ note.innerHTML=noteHTML; },4000); }
 document.getElementById('b-mail').addEventListener('click',function(){
   var t=build();
   window.location.href='mailto:info@servomotion.com.tr?subject='+
     encodeURIComponent('Application summary — '+(el['f-firma'].value||'ServoMotion website'))+'&body='+encodeURIComponent(t);
 });
 document.getElementById('b-wa').addEventListener('click',function(){
   window.open('https://wa.me/905446361200?text='+encodeURIComponent(build()),'_blank','noopener');
 });
 document.getElementById('b-copy').addEventListener('click',function(){
   var t=build();
   function fb(){ var ta=document.createElement('textarea'); ta.value=t; ta.style.position='fixed'; ta.style.opacity='0';
     document.body.appendChild(ta); ta.select();
     try{ document.execCommand('copy'); flash('Summary copied to clipboard.'); }
     catch(e){ flash('Could not copy — please select the text manually.'); }
     document.body.removeChild(ta); }
   if(navigator.clipboard && navigator.clipboard.writeText){ navigator.clipboard.writeText(t).then(function(){flash('Summary copied to clipboard.');},fb); }
   else fb();
 });
 var MAP={rubber:'Rubber and composite — reinforcement layer laying',nonwoven:'Hygiene products and nonwoven',packaging:'Packaging and paper'};
 try{ var k=(location.search.match(/[?&]topic=([^&]+)/)||[])[1];
   if(k && MAP[decodeURIComponent(k)]) el['f-hat'].value=MAP[decodeURIComponent(k)];
 }catch(e){}
 build();
})();
