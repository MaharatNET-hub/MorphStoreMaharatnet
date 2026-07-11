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

## 🚀 طريقة الرفع على السيرفر وتشغيله

في حال توفّر **SSH** على السيرفر (VPS أو استضافة سحابية) استخدم **المسار أ** — آلي بالكامل
وموصى به لأنه يربط الموقع مباشرة بـ git (أي تحديث لاحق = `git push` + أمر واحد على السيرفر).
إن كانت الاستضافة **مشتركة بدون SSH** (لوحة تحكم فقط مثل cPanel) استخدم **المسار ب**.

### المسار أ — سيرفر فيه SSH (آلي عبر WP-CLI) — موصى به

1. **تجهيز قاعدة بيانات فارغة** على السيرفر (اسم DB + مستخدم + كلمة مرور)، عبر لوحة
   الاستضافة أو `mysql -u root -p`.

2. **نسخ المستودع إلى السيرفر** (أول مرة فقط):
   ```bash
   git clone https://github.com/MaharatNET-hub/MorphStoreMaharatnet.git /opt/morphstore-maharatnet
   cd /opt/morphstore-maharatnet
   ```

3. **تجهيز متغيرات البيئة**:
   ```bash
   cp .env.example .env
   nano .env   # عبّئ WP_PATH, DB_*, SITE_URL, ADMIN_*, REPO_URL, REPO_DIR=/opt/morphstore-maharatnet
   set -a && source .env && set +a
   ```

4. **تشغيل سكربت التجهيز الكامل** — هذا الأمر الوحيد الذي "يبني ويشغّل" كل شيء:
   ```bash
   bash bin/setup-server.sh
   ```
   يقوم تلقائياً بـ:
   - تركيب WP-CLI إن لم يكن موجوداً.
   - تحميل نواة ووردبريس وإعداد `wp-config.php` وربطه بقاعدة البيانات.
   - تنصيب ووردبريس فعلياً (إنشاء حساب الأدمن، عنوان الموقع...).
   - تركيب الثيم الأساسي **Astra**.
   - تركيب وتفعيل كل الإضافات المجانية المدرجة في `bin/plugins-wp-org.txt`.
   - ربط `theme/astra-child-maharatnet` داخل `wp-content/themes/` عبر **symlink**
     (بحيث أي `git pull` لاحقاً يحدّث الموقع فوراً بدون نسخ ملفات يدوياً).
   - **تفعيل الثيم الابن** وضبط روابط الأرشفة (Permalinks) — هذه خطوة "التشغيل" الفعلية.

5. عند انتهاء السكربت سيطبع رابط الموقع ورابط لوحة التحكم — افتحهما للتأكد أن الموقع يعمل.

6. **تركيب الإضافات اليدوية** (لا تُثبَّت تلقائياً لأنها ليست على WordPress.org) المدرجة في
   `bin/plugins-manual.txt` مثل JetWooBuilder من Crocoblock: حمّلها كملف zip من مصدرها ثم
   ارفعها من لوحة التحكم عبر `Plugins > Add New > Upload Plugin > Activate`.

### المسار ب — استضافة مشتركة بدون SSH (رفع يدوي عبر FTP / File Manager)

1. نصّب ووردبريس عادي من لوحة الاستضافة (Softaculous أو مماثل)، مع WooCommerce وElementor
   وAdvanced Custom Fields وAstra مفعّلة من `Plugins/Themes > Add New` داخل لوحة التحكم.

2. من جهازك، حمّل نسخة الكود من GitHub (زر **Code > Download ZIP** على المستودع، أو
   `git clone` ثم ضغط المجلد)، وحوّل مجلد `theme/astra-child-maharatnet/` وحده إلى ملف
   `astra-child-maharatnet.zip` (المجلد نفسه هو جذر الـ zip، وليس مجلد `theme/`).

3. ادخل على السيرفر عبر **FTP** (FileZilla مثلاً) أو **File Manager** في لوحة الاستضافة،
   وارفع مجلد `astra-child-maharatnet` كاملاً إلى `wp-content/themes/`.
   (أو من لوحة ووردبريس مباشرة: `Appearance > Themes > Add New > Upload Theme` واختر
   ملف الـ zip.)

4. من لوحة التحكم: **Appearance > Themes** فعّل **Astra Child - Maharatnet Store** — هذه
   خطوة "التشغيل" (بمجرد التفعيل تعمل كل حقول ACF وصفحة "أشكال المتجر" تلقائياً لأنها
   مسجّلة بالكود داخل الثيم نفسه).

5. ركّب الإضافات اليدوية من `bin/plugins-manual.txt` بنفس طريقة رفع الثيم (zip → Upload).

> ⚠️ في هذا المسار لا يوجد `git pull` تلقائي — أي تعديل لاحق على الكود (مثل تعبئة
> `template-map.php`) يتطلّب إعادة ضغط مجلد الثيم ورفعه من جديد. إن توفّر لاحقاً وصول
> SSH يُفضَّل الانتقال للمسار (أ) لتفعيل التحديث المباشر عبر git.

### خطوات مشتركة بعد التشغيل (بالمسارين)

6. **تصميم الأشكال في Elementor**: لكل عنصر (هيدر، هيرو، كارت منتج...) صمّم كل Variant
   واحفظه كـ `Template → Section/Container`، بنظام تسمية موحّد مثل `header-01`, `hero-02`.

7. **ربط القوالب بالكود**: افتح `theme/astra-child-maharatnet/inc/template-map.php`
   واستبدل الأصفار بـ ID كل قالب (يظهر الـ ID في رابط تحرير القالب `?post=1234`)، ثم:
   - **المسار أ**: `git add` + `git commit` + `git push` من جهازك، ثم على السيرفر:
     ```bash
     bash bin/deploy-update.sh
     ```
   - **المسار ب**: عدّل الملف محلياً، أعد ضغط مجلد الثيم، وارفعه من جديد فوق النسخة
     القديمة (Upload Theme يستبدل الملفات تلقائياً).

8. **مراجعة صفحة الإعدادات**: من لوحة التحكم افتح **أشكال المتجر** (في القائمة الجانبية)
   واختر الشكل الافتراضي المناسب لكل عنصر — هذه هي الخطوة التي يقوم بها Shop Manager
   يومياً بدون أي حاجة للعودة للكود.

### التحقق من أن كل شيء يعمل

- [ ] الموقع يفتح على `SITE_URL` بدون أخطاء بيضاء (White Screen).
- [ ] **Appearance > Themes** يُظهر الثيم الابن مفعّلاً (وAstra مرئي كـ Parent Theme).
- [ ] قائمة جانبية باسم **أشكال المتجر** ظاهرة في لوحة التحكم (تعني أن ACF يعمل والحقول
      مسجّلة بنجاح).
- [ ] WooCommerce مفعّل ويظهر صفحاته الأساسية (متجر، سلة، شيك أوت).
- [ ] بعد ربط أول قالب في `template-map.php`، يظهر فعلياً في مكانه على الموقع (الهيدر
      مثلاً) بدل رسالة التنبيه الحمراء التي تظهر للأدمن فقط عند عدم وجود قالب مربوط.

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
