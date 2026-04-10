# Talian ERP — نظام المحاسبة

نظام محاسبة متعدد المستأجرين مبني على Laravel 11.

## هيكل المستودع

```
.
├── backend/          ← Laravel 11 API (PHP 8.2)
│   ├── app/
│   ├── config/
│   ├── database/
│   ├── public/
│   ├── routes/
│   ├── storage/
│   ├── artisan
│   └── composer.json
├── docker/
│   ├── nginx/default.conf
│   └── entrypoint.sh
├── docker-compose.yml
├── Dockerfile
└── .env.example
```

---

## التشغيل على VPS أو محليًا عبر Docker (الطريقة الموصى بها)

### المتطلبات
- Docker Engine >= 24
- Docker Compose Plugin >= 2.20

### الخطوات

```bash
# 1. استنساخ المستودع
git clone https://github.com/aa7026331145-ops/Talian-li-anzimat-al-muhasaba-.git
cd Talian-li-anzimat-al-muhasaba-

# 2. إعداد ملف البيئة
cp .env.example .env
# عدّل .env حسب الحاجة (كلمات المرور، APP_URL، إلخ)

# 3. بناء وتشغيل الحاويات
docker compose up -d --build

# 4. تحقق من أن كل الخدمات تعمل
docker compose ps
```

عند التشغيل الأول يقوم entrypoint تلقائيًا بـ:
- تثبيت composer install
- توليد APP_KEY إن لم يكن موجودًا
- تشغيل php artisan migrate --force
- تشغيل php artisan db:seed (في بيئة غير إنتاجية)

افتح المتصفح على: http://SERVER_IP (أو http://localhost)

### بيانات الدخول الافتراضية (بيئة تطوير)

| الدور | البريد الإلكتروني | كلمة المرور |
|-------|-----------------|------------|
| Super Admin | superadmin@talian-erp.local | value of DEFAULT_SUPER_ADMIN_PASSWORD in .env |
| Tenant Admin | admin@demo.talian-erp.local | value of DEFAULT_ADMIN_PASSWORD in .env |

> **تحذير:** غيّر كلمات المرور فور النشر على الإنتاج.

---

## أوامر مفيدة

```bash
# تسجيل دخول إلى حاوية التطبيق
docker compose exec app sh

# تشغيل أمر artisan
docker compose exec app php artisan <command>

# عرض السجلات
docker compose logs -f app

# إيقاف الخدمات
docker compose down

# إيقاف الخدمات وحذف قاعدة البيانات
docker compose down -v
```

---

## التشغيل اليدوي بدون Docker

```bash
cd backend

# تثبيت الحزم
composer install

# إعداد البيئة
cp .env.example .env
# عدّل .env: DB_HOST=127.0.0.1 وبيانات MySQL المحلية

# توليد المفتاح
php artisan key:generate

# تشغيل الترحيلات والبذر
php artisan migrate --seed

# تشغيل الخادم المحلي
php artisan serve --host=0.0.0.0 --port=8000
```

---

## API Endpoints

جميع النقاط تبدأ بـ /api/

| الطريقة | المسار | الوصف |
|--------|--------|-------|
| POST | /api/login | تسجيل الدخول |
| POST | /api/logout | تسجيل الخروج |
| GET | /api/me | بيانات المستخدم الحالي |
| GET/POST | /api/accounts | شجرة الحسابات |
| GET/POST | /api/journal-entries | قيود اليومية |
| POST | /api/journal-entries/{id}/post | ترحيل قيد |
| GET/POST | /api/tenants | إدارة المستأجرين (super-admin) |

---

## الترقية إلى HTTPS

1. احصل على شهادة SSL (Let's Encrypt مثلاً عبر Certbot)
2. أضف volume للشهادات في docker-compose.yml
3. عدّل docker/nginx/default.conf لإضافة listen 443 ssl
