/* Talep formları (doküman, destek, iade, kılavuz): servomotion/v1/talep uç noktasına gönderir.
   Form data-adres özniteliği yoksa (alıcı adresi tanımlı değilse) hiçbir şey yapmaz ve
   formun mailto: yedeği çalışır. */
(function () {
  'use strict';

  var formlar = document.querySelectorAll('form[data-talep][data-adres]');
  if (!formlar.length || !window.fetch) return;

  Array.prototype.forEach.call(formlar, function (f) {
    var sonuc = f.querySelector('.talep-sonuc');
    var dugme = f.querySelector('button[type="submit"]');

    function goster(metin, hata) {
      if (!sonuc) return;
      sonuc.textContent = metin;
      sonuc.className = 'talep-sonuc ' + (hata ? 'hata' : 'tamam');
      sonuc.hidden = false;
    }

    f.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!f.reportValidity()) return;

      var alanlar = {};
      var dokumanlar = [];
      var digerDil = false;

      Array.prototype.forEach.call(f.elements, function (el) {
        if (!el.name || el.name === 'web') return;
        if (el.type === 'checkbox') {
          if (!el.checked) return;
          if (el.dataset.dil) digerDil = true;
          else if (el.dataset.kod) dokumanlar.push(el.dataset.kod);
          return;
        }
        alanlar[el.name] = el.value;
      });

      var web = f.querySelector('input[name="web"]');
      var yuk = {
        tur: f.getAttribute('data-talep'),
        alanlar: alanlar,
        dokumanlar: dokumanlar,
        diger_dil: digerDil,
        lang: (document.documentElement.lang || 'tr').slice(0, 2),
        web: web ? web.value : '',
        kaynak: window.location.href.split('#')[0]
      };

      if (sonuc) sonuc.hidden = true;
      if (dugme) dugme.disabled = true;

      fetch(f.getAttribute('data-adres'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(yuk)
      })
        .then(function (r) {
          return r.json().then(function (j) { return { ok: r.ok, j: j }; });
        })
        .then(function (s) {
          if (s.ok && s.j && s.j.ok) {
            goster(s.j.mesaj || f.getAttribute('data-tamam') || 'Talebiniz alındı.', false);
            f.reset();
          } else {
            goster((s.j && s.j.message) || f.getAttribute('data-hata') || 'Talep gönderilemedi. Lütfen tekrar deneyin.', true);
          }
        })
        .catch(function () {
          goster(f.getAttribute('data-hata') || 'Talep gönderilemedi. Lütfen tekrar deneyin.', true);
        })
        .then(function () {
          if (dugme) dugme.disabled = false;
        });
    });
  });
})();
