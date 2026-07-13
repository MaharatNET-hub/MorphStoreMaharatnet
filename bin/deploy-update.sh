#!/usr/bin/env bash
#
# deploy-update.sh — يسحب آخر تعديلات الكود من git على سيرفر مُجهَّز مسبقاً
# (بعد تشغيل setup-server.sh مرة واحدة). لا يعيد تنصيب ووردبريس أو الإضافات.
#
# الاستخدام على السيرفر:
#   set -a && source .env && set +a
#   bash bin/deploy-update.sh
#
set -euo pipefail

: "${REPO_DIR:?يجب تعريف REPO_DIR في .env}"
: "${REPO_BRANCH:=main}"
: "${WP_PATH:?يجب تعريف WP_PATH في .env}"

echo "سحب آخر التعديلات من $REPO_BRANCH ..."
git -C "$REPO_DIR" fetch origin "$REPO_BRANCH"
git -C "$REPO_DIR" checkout "$REPO_BRANCH"
git -C "$REPO_DIR" pull origin "$REPO_BRANCH"

WP_BIN="wp"
if ! command -v wp &> /dev/null && [ -x "$HOME/.local/bin/wp" ]; then
	WP_BIN="$HOME/.local/bin/wp"
fi

echo "تفريغ الكاش (إن وُجدت إضافة تخزين مؤقت مُفعّلة) ..."
"$WP_BIN" cache flush --path="$WP_PATH" || true
if "$WP_BIN" plugin is-active litespeed-cache --path="$WP_PATH" &> /dev/null; then
	"$WP_BIN" litespeed-purge all --path="$WP_PATH" || true
fi

echo "تم تحديث الموقع بآخر نسخة من الكود بنجاح."
