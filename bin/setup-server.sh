#!/usr/bin/env bash
#
# setup-server.sh — يجهّز تنصيب ووردبريس كامل من الصفر على سيرفر جديد
# (LAMP/LEMP جاهز مسبقاً: PHP + MySQL/MariaDB + Nginx/Apache) ويربطه بهذا المستودع.
#
# يُشغَّل مرة واحدة عند تجهيز السيرفر لأول مرة. للتحديثات اللاحقة استخدم deploy-update.sh.
#
# الاستخدام:
#   1) انسخ .env.example إلى .env واملأ القيم الحقيقية على السيرفر.
#   2) set -a && source .env && set +a
#   3) bash bin/setup-server.sh
#
set -euo pipefail

# ---------- التحقق من المتغيرات المطلوبة ----------
required_vars=(WP_PATH DB_NAME DB_USER DB_PASS DB_HOST SITE_URL SITE_TITLE ADMIN_USER ADMIN_PASS ADMIN_EMAIL REPO_DIR)
for var in "${required_vars[@]}"; do
	if [ -z "${!var:-}" ]; then
		echo "خطأ: المتغير $var غير معرّف. عرّف .env وشغّل: set -a && source .env && set +a" >&2
		exit 1
	fi
done

DB_TABLE_PREFIX="${DB_TABLE_PREFIX:-wp_}"
THEME_SLUG="astra-child-maharatnet"

echo "== 1/9: التحقق من صلاحية الكتابة على WP_PATH =="
mkdir -p "$WP_PATH"
if ! touch "$WP_PATH/.ms-write-test" 2>/dev/null; then
	cat >&2 <<EOF
خطأ: المستخدم الحالي ($(whoami)) لا يملك صلاحية الكتابة على WP_PATH="$WP_PATH".

هذا شائع على استضافات cPanel: مسارات مثل /var/www/... عادة مملوكة لـ root
أو Apache وليست قابلة للكتابة من حساب المستخدم العادي. المسار الصحيح على
cPanel يكون داخل مجلدك الشخصي، مثل:
  /home/$(whoami)/morphstore.maharatnet.com
  أو /home/$(whoami)/public_html  (إن كان هذا هو الدومين الأساسي)

تأكّد من المسار الفعلي عبر لوحة cPanel (Domains > Document Root)، أو بتشغيل:
  pwd   (بعد الدخول لمجلد الدومين من File Manager/SSH)

ثم صحّح WP_PATH في ملف .env وأعد تحميله:
  set -a && source .env && set +a
  bash bin/setup-server.sh
EOF
	exit 1
fi
rm -f "$WP_PATH/.ms-write-test"

echo "== 2/9: التحقق من WP-CLI =="
WP_BIN=""
if command -v wp &> /dev/null; then
	WP_BIN="wp"
else
	echo "WP-CLI غير موجود — تركيبه..."
	LOCAL_WP_BIN="$HOME/.local/bin/wp"
	if curl -sSL -o /usr/local/bin/wp https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar 2>/dev/null \
		&& chmod +x /usr/local/bin/wp 2>/dev/null; then
		WP_BIN="/usr/local/bin/wp"
	else
		echo "تعذّرت الكتابة على /usr/local/bin (شائع على استضافة بدون صلاحيات root) — التركيب في $LOCAL_WP_BIN بدلاً من ذلك."
		mkdir -p "$HOME/.local/bin"
		curl -sSL -o "$LOCAL_WP_BIN" https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar
		chmod +x "$LOCAL_WP_BIN"
		WP_BIN="$LOCAL_WP_BIN"
	fi
fi
"$WP_BIN" --info

echo "== 3/9: تحميل نواة ووردبريس =="
if [ ! -f "$WP_PATH/wp-load.php" ]; then
	"$WP_BIN" core download --path="$WP_PATH" --locale=ar
else
	echo "ووردبريس موجود مسبقاً في $WP_PATH — تخطّي التحميل."
fi

echo "== 4/9: إعداد wp-config.php =="
if [ ! -f "$WP_PATH/wp-config.php" ]; then
	"$WP_BIN" config create \
		--path="$WP_PATH" \
		--dbname="$DB_NAME" \
		--dbuser="$DB_USER" \
		--dbpass="$DB_PASS" \
		--dbhost="$DB_HOST" \
		--dbprefix="$DB_TABLE_PREFIX" \
		--locale=ar \
		--extra-php <<'PHP'
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'DISALLOW_FILE_EDIT', true );
PHP
else
	echo "wp-config.php موجود مسبقاً — تخطّي."
fi

echo "== 5/9: تنصيب ووردبريس =="
if ! "$WP_BIN" core is-installed --path="$WP_PATH" 2>/dev/null; then
	"$WP_BIN" core install \
		--path="$WP_PATH" \
		--url="$SITE_URL" \
		--title="$SITE_TITLE" \
		--admin_user="$ADMIN_USER" \
		--admin_password="$ADMIN_PASS" \
		--admin_email="$ADMIN_EMAIL" \
		--skip-email
else
	echo "ووردبريس مُنصَّب مسبقاً — تخطّي."
fi

echo "== 6/9: تركيب الثيم الأساسي Astra =="
"$WP_BIN" theme install astra --path="$WP_PATH" || echo "تنبيه: تعذّر تركيب Astra (قد يكون مُركَّباً مسبقاً)."

echo "== 7/9: تركيب وتفعيل الإضافات المجانية من WordPress.org =="
PLUGINS_FILE="$(dirname "$0")/plugins-wp-org.txt"
failed_plugins=()
while IFS= read -r plugin || [ -n "$plugin" ]; do
	plugin="$(echo "$plugin" | sed 's/#.*//' | xargs)"
	[ -z "$plugin" ] && continue
	echo "  -> $plugin"
	if ! "$WP_BIN" plugin install "$plugin" --activate --path="$WP_PATH"; then
		failed_plugins+=("$plugin")
	fi
done < "$PLUGINS_FILE"

echo "== 8/9: ربط الثيم الابن من هذا المستودع (git) =="
if [ ! -d "$REPO_DIR/.git" ]; then
	echo "استنساخ المستودع إلى $REPO_DIR ..."
	git clone --branch "${REPO_BRANCH:-main}" "$REPO_URL" "$REPO_DIR"
else
	echo "المستودع موجود مسبقاً في $REPO_DIR — تحديث..."
	git -C "$REPO_DIR" fetch origin "${REPO_BRANCH:-main}"
	git -C "$REPO_DIR" checkout "${REPO_BRANCH:-main}"
	git -C "$REPO_DIR" pull origin "${REPO_BRANCH:-main}"
fi

THEME_TARGET="$WP_PATH/wp-content/themes/$THEME_SLUG"
if [ -L "$THEME_TARGET" ] || [ -d "$THEME_TARGET" ]; then
	echo "رابط/مجلد الثيم موجود مسبقاً في $THEME_TARGET — تخطّي الربط."
else
	ln -s "$REPO_DIR/theme/$THEME_SLUG" "$THEME_TARGET"
	echo "تم ربط الثيم: $THEME_TARGET -> $REPO_DIR/theme/$THEME_SLUG"
fi

echo "== 9/9: تفعيل الثيم الابن وضبط الإعدادات =="
"$WP_BIN" theme activate "$THEME_SLUG" --path="$WP_PATH"
"$WP_BIN" rewrite structure '/%postname%/' --path="$WP_PATH"
"$WP_BIN" rewrite flush --path="$WP_PATH"

echo ""
echo "======================================================================"
echo "تم التنصيب بنجاح 🎉"
echo "رابط الموقع: $SITE_URL"
echo "رابط لوحة التحكم: $SITE_URL/wp-admin"
echo ""
if [ "${#failed_plugins[@]}" -gt 0 ]; then
	echo "تنبيه: تعذّر تركيب الإضافات التالية تلقائياً (ركّبها يدوياً أو تحقّق من الاسم):"
	printf '  - %s\n' "${failed_plugins[@]}"
	echo ""
fi
echo "خطوات متبقية يدوياً (راجع README.md):"
echo "  1) تركيب الإضافات المدرجة في bin/plugins-manual.txt (JetWooBuilder وغيرها)."
echo "  2) تصميم قوالب Elementor لكل شكل، ونسخ الـ IDs إلى theme/$THEME_SLUG/inc/template-map.php"
echo "  3) مراجعة صفحة إعدادات (أشكال المتجر) في لوحة التحكم واختيار الأشكال الافتراضية."
echo "======================================================================"
