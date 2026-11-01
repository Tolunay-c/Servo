/* Uygulama notu kartları: "Talep et" düğmesi kartın içinde e-posta alanını ve Gönder'i açar (aynı anda tek kart açık).
   Klavye: düğme Enter/Boşluk ile açar ve odağı e-posta alanına taşır; Esc kartı kapatıp odağı düğmeye döndürür.
   Gönderim talep.js ile yapılır (servomotion/v1/talep). */
(function () {
  'use strict';

  var kartlar = [].slice.call(document.querySelectorAll('.dlform'));
  if (!kartlar.length) return;

  function ayarla(kart, ac) {
    var dugme = kart.querySelector('.dl-ac');
    var alan = kart.querySelector('.dl-alan');
    if (!dugme || !alan) return;
    alan.hidden = !ac;
    dugme.setAttribute('aria-expanded', ac ? 'true' : 'false');
    kart.classList.toggle('acik', ac);
  }

  kartlar.forEach(function (kart) {
    var dugme = kart.querySelector('.dl-ac');
    if (!dugme) return;

    dugme.addEventListener('click', function () {
      var ac = dugme.getAttribute('aria-expanded') !== 'true';
      kartlar.forEach(function (k) { if (k !== kart) ayarla(k, false); });
      ayarla(kart, ac);
      if (ac) {
        var giris = kart.querySelector('input[type="email"]');
        if (giris) giris.focus();
      }
    });

    kart.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && dugme.getAttribute('aria-expanded') === 'true') {
        ayarla(kart, false);
        dugme.focus();
      }
    });
  });
})();
