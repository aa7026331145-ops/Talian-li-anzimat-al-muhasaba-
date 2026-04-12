name=README.md
# Accounting SaaS MVP (Scaffold)

محتويات هذا المشروع:
- docker-compose.yml : لتشغيل PostgreSQL + backend + frontend
- backend : Express + Sequelize API (JWT auth, tenant isolation basic)
- frontend : React (بسيط) واجهة RTL

تشغيل محلي سريع:
1. ثبت Docker و Docker Compose.
2. انسخ المشروع لمجلد محلي.
3. شغل:
   docker-compose up --build
4. بعد الإقلاع:
   - Frontend: http://localhost:3000
   - Backend: http://localhost:4000
   - API Health: http://localhost:4000/api/health

خطوات مبدئية للاستعمال:
- سجل Tenant جديد عبر POST /api/auth/register
  body: { "tenantName":"شركة تجريبية", "name":"Admin", "email":"admin@x.com", "password":"password" }
- ��جل دخول POST /api/auth/login ثم استخدم الـtoken للطلبات المحمية.

ملاحظات أمان وإنتاج:
- هذه نسخة MVP للتطوير فقط. قبل الإنتاج: قم بتأمين JWT secret عبر environment variables قوية، فعّل HTTPS، إضافة rate-limiting و input validation، اختبارات أمان، وتهيئة نسخ احتياطية للبيانات.
- قم بتحويل توليد الفواتير إلى ترازع آمن وتحقق من تحديث المخزون وقيود دفتر اليومية عند كل معاملة.

نموذج اتفاقية تسليم ملكية الكود: see OWNERSHIP.md