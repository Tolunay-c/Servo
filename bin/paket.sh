#!/usr/bin/env bash
#
# Canlıya yüklenecek tema paketini (zip) oluşturur.
#
# Kullanım:  bin/paket.sh            -> dist/servo-<sürüm>.zip
#            bin/paket.sh <hedef.zip>
#
# Pakete çalışma ağacındaki dosyalar girer (commit edilmemiş değişiklikler
# dahil, .gitignore'daki dosyalar hariç). Tasarım, dokümanlar, git ve
# geliştirme dosyaları pakete girmez. Zip'in içinde tek klasör (servo/)
# vardır; Görünüm > Temalar > Yeni ekle > Tema yükle ile yüklenebilir.

set -euo pipefail

KOK="$(cd "$(dirname "$0")/.." && pwd)"
TEMA="servo"
cd "$KOK"

SURUM="$(sed -n 's/^Version:[[:space:]]*//p' style.css | head -1 | tr -d '[:space:]')"
HEDEF="${1:-dist/${TEMA}-${SURUM:-0}.zip}"
case "$HEDEF" in /*) ;; *) HEDEF="$KOK/$HEDEF" ;; esac

# Pakete girmeyecek yollar (kökten itibaren).
HARIC='^(_design/|docs/|bin/|dist/|\.playwright-mcp/|\.claude/|\.vscode/|\.idea/|node_modules/|\.git|CLAUDE\.md$|README\.md$|phpcs\.xml|composer\.(json|lock)$|package(-lock)?\.json$)'

GECICI="$(mktemp -d)"
trap 'rm -rf "$GECICI"' EXIT
mkdir -p "$GECICI/$TEMA"

# İzlenen + izlenmeyen (ignore edilmemiş) dosyalar; silinmiş olanlar atlanır.
git ls-files -z --cached --others --exclude-standard \
	| tr '\0' '\n' \
	| grep -Ev "$HARIC" \
	| grep -Ev '(^|/)\.gitkeep$|(^|/)\.DS_Store$|(^|/)Thumbs\.db$|\.(log|swp|bak)$' \
	| while IFS= read -r dosya; do
		[ -f "$dosya" ] || continue
		mkdir -p "$GECICI/$TEMA/$(dirname "$dosya")"
		cp -p "$dosya" "$GECICI/$TEMA/$dosya"
	done

# Zorunlu dosyalar pakette olmalı.
for zorunlu in style.css functions.php index.php screenshot.png; do
	if [ ! -f "$GECICI/$TEMA/$zorunlu" ]; then
		echo "Hata: $zorunlu pakette yok." >&2
		exit 1
	fi
done

mkdir -p "$(dirname "$HEDEF")"
rm -f "$HEDEF"
( cd "$GECICI" && zip -qrX "$HEDEF" "$TEMA" )

ADET="$(find "$GECICI/$TEMA" -type f | wc -l | tr -d ' ')"
BOYUT="$(du -h "$HEDEF" | cut -f1 | tr -d ' ')"
echo "Paket hazır: $HEDEF ($ADET dosya, $BOYUT)"
