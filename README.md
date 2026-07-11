# MorphStoreMaharatnet

نظام تصميم متعدّد الأشكال (Multi-Variant Design System) لمتجر إلكتروني على ووردبريس:
**Astra + Elementor + WooCommerce + ACF**. الأدمن يصمّم عدّة أشكال جاهزة لكل عنصر
(هيدر، هيرو، كارت منتج، فوتر...)، ومدير المتجر (Shop Manager) يختار الشكل المناسب
لكل عميل من صفحة إعدادات واحدة، دون أي صلاحية تعديل على التصميم.

التوصيف الكامل للمشروع: [`docs/project-overview.md`](docs/project-overview.md).

## بنية المستودع

```
theme/astra-child-maharatnet/   ← الثيم الابن (الكود الفعلي الذي يُرفع على السيرفر)
  ├─ style.css                 ← رأس الثيم الابن (Template: astra)
  ├─ functions.php             ← نقطة الدخول، يحمّل كل ملفات inc/
  ├─ header.php / footer.php   ← يعرضان شكل الهيدر/الفوتر المختار
  └─ inc/
      ├─ acf-options-page.php  ← صفحة إعدادات "أشكال المتجر"
      ├─ acf-fields.php        ← 12 حقل Select (بالكود، بدون إعداد يدوي)
      ├─ template-map.php      ← خريطة: اختيار ← ID قالب Elementor (تُعبَّأ بعد الرفع)
      ├─ helpers.php           ← ms_render_store_part() + shortcode [store_part]
      ├─ hooks-woocommerce.php ← ربط كارت المنتج + body classes
      └─ role-permissions.php  ← تقييد صلاحيات Shop Manager
bin/
  ├─ setup-server.sh           ← تجهيز سيرفر جديد بالكامل من الصفر (أول مرة)
  ├─ deploy-update.sh          ← سحب تحديثات لاحقة من git على سيرفر مُجهَّز مسبقاً
  ├─ plugins-wp-org.txt        ← إضافات تُركَّب تلقائياً (WordPress.org)
  └─ plugins-manual.txt        ← إضافات تحتاج تحميل يدوي (Crocoblock وغيرها)
docs/project-overview.md       ← ملخّص فني للمشروع وقرارات التنفيذ
.env.example                   ← نموذج متغيرات البيئة (انسخه إلى .env على السيرفر)
```

## المتطلبات على السيرفر

- سيرفر بـ **PHP 8.0+**، **MySQL/MariaDB**، و **Nginx أو Apache** جاهز (LEMP/LAMP).
- صلاحيات SSH ووصول لقاعدة بيانات فارغة (اسم/مستخدم/كلمة مرور).
- Git مُركَّب على السيرفر.
- (اختياري لكن موصى به) [WP-CLI](https://wp-cli.org) — السكربت يركّبه تلقائياً إن لم يكن موجوداً.

## خطوات النشر (أول مرة على سيرفر جديد)

1. **تجهيز قاعدة بيانات** فارغة على السيرفر (اسم DB + مستخدم + كلمة مرور).

2. **نسخ متغيرات البيئة** وتعبئتها بالقيم الحقيقية:
   ```bash
   cp .env.example .env
   nano .env   # عبّئ WP_PATH, DB_*, SITE_URL, ADMIN_*, REPO_URL...
   set -a && source .env && set +a
   ```

3. **تشغيل سكربت التجهيز الكامل** (من داخل نسخة المستودع على السيرفر، أو حتى قبل
   استنساخه — السكربت نفسه يستنسخ `REPO_DIR` إذا لم يكن موجوداً):
   ```bash
   bash bin/setup-server.sh
   ```
   هذا السكربت يقوم تلقائياً بـ:
   - تركيب WP-CLI إن لم يكن موجوداً.
   - تحميل نواة ووردبريس وإعداد `wp-config.php`.
   - تنصيب ووردبريس (عنوان الموقع + حساب الأدمن).
   - تركيب الثيم الأساسي **Astra**.
   - تركيب وتفعيل كل الإضافات المجانية المدرجة في `bin/plugins-wp-org.txt`.
   - استنساخ هذا المستودع إلى `REPO_DIR` وربط `theme/astra-child-maharatnet` داخل
     `wp-content/themes/` عبر **symlink** (بحيث `git pull` لاحقاً يحدّث الموقع فوراً).
   - تفعيل الثيم الابن وضبط روابط الأرشفة (Permalinks).

4. **تركيب الإضافات اليدوية** المدرجة في `bin/plugins-manual.txt` (مثل JetWooBuilder
   من Crocoblock) عبر لوحة التحكم: `Plugins > Add New > Upload Plugin`.

5. **تصميم الأشكال في Elementor**: لكل عنصر (هيدر، هيرو، كارت منتج...) صمّم كل Variant
   واحفظه كـ `Template → Section/Container`، بنظام تسمية موحّد مثل `header-01`, `hero-02`.

6. **ربط القوالب بالأكواد**: افتح
   `theme/astra-child-maharatnet/inc/template-map.php` واستبدل الأصفار بـ ID كل قالب
   (يظهر الـ ID في رابط تحرير القالب `?post=1234`)، ثم:
   ```bash
   git add theme/astra-child-maharatnet/inc/template-map.php
   git commit -m "ربط قوالب Elementor بخريطة الأشكال"
   git push
   ```

7. **تحديث السيرفر بآخر التعديلات** بعد أي تعديل على الكود (محلياً أو من أي مكان):
   ```bash
   bash bin/deploy-update.sh
   ```

8. **مراجعة صفحة الإعدادات**: من لوحة التحكم افتح **أشكال المتجر** واختر الشكل
   الافتراضي المناسب لكل عنصر.

## آلية عمل النظام (مختصر)

- كل عنصر متجر = **قالب (Template)** مصمَّم في Elementor، مُسمَّى ومحفوظ بـ ID.
- Shop Manager يفتح صفحة **أشكال المتجر** (صلاحية `manage_woocommerce`، بدون أي
  وصول لتحرير القوالب)، ويختار من قوائم منسدلة جاهزة.
- الكود يقرأ الاختيار عبر ACF ويعرض القالب المطابق تلقائياً:
  - **الهيدر/الفوتر**: تلقائياً على كل صفحة.
  - **كارت المنتج**: تلقائياً في كل عرض لقائمة المنتجات.
  - **هيرو/تصنيفات/أقسام منتجات/آراء عملاء/براندات**: عبر Shortcode
    `[store_part field="hero_style"]` يوضع في أي صفحة Elementor.
  - **صفحة المتجر/المنتج المفرد/السلة/الشيك أوت**: قوالب كاملة الصفحة تُربط عبر
    JetWooBuilder + إضافة شروط عرض (Dynamic Conditions) تقارن قيمة الحقل مباشرة،
    أو عبر كلاس `body` المُضاف تلقائياً (`shop-style-shop-02` مثلاً).

تفاصيل أوسع في [`docs/project-overview.md`](docs/project-overview.md).

## ملاحظات أداء

- فعّل كاش (LiteSpeed Cache أو مماثل) بعد التنصيب مباشرة.
- احذف من `template-map.php` أي قالب غير مستخدم فعلياً بعد استقرار المشروع.
- استخدم Flexbox Containers في Elementor بدل الويدجت التقليدية لخفّة أكبر.
