name=backend/routes/invoices.js
const express = require('express');
const router = express.Router();
const { Invoice, InvoiceItem, Product, Payment } = require('../models');
const auth = require('../middleware/auth');
const tenant = require('../middleware/tenant');
const { v4: uuidv4 } = require('uuid');

// قائمة الفواتير
router.get('/', auth, tenant, async (req, res) => {
  const invoices = await Invoice.findAll({ where: { tenant_id: req.tenantId } });
  res.json(invoices);
});

// إنشاء فاتورة مع بنودها
router.post('/', auth, tenant, async (req, res) => {
  try {
    const { customer_id, items = [], due_at } = req.body;
    const invoice_number = 'INV-' + uuidv4().slice(0,8).toUpperCase();
    // حسابات مبسطة
    let subtotal = 0, tax = 0;
    for(const it of items){
      subtotal += parseFloat(it.quantity) * parseFloat(it.unit_price);
      tax += ((parseFloat(it.quantity) * parseFloat(it.unit_price)) * (parseFloat(it.tax||0)/100));
    }
    const total = subtotal + tax;
    const invoice = await Invoice.create({
      tenant_id: req.tenantId,
      invoice_number,
      customer_id,
      status: 'issued',
      issued_at: new Date(),
      due_at,
      subtotal,
      tax,
      total
    });

    for(const it of items){
      await InvoiceItem.create({
        invoice_id: invoice.id,
        product_id: it.product_id || null,
        description: it.description || '',
        quantity: it.quantity,
        unit_price: it.unit_price,
        tax: it.tax || 0,
        line_total: (it.quantity * it.unit_price) + ((it.quantity * it.unit_price) * ((it.tax||0)/100))
      });
      // ملاحظة: لم نحدث المخزون هنا تلقائيًا (يمكن إضافته لاحقًا)
    }

    res.status(201).json(invoice);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'server error' });
  }
});

// تسجيل دفعة لفاتورة
router.post('/:id/payments', auth, tenant, async (req, res) => {
  try {
    const invoice = await Invoice.findOne({ where: { id: req.params.id, tenant_id: req.tenantId } });
    if(!invoice) return res.status(404).json({ error: 'invoice not found' });
    const { amount, method } = req.body;
    const payment = await Payment.create({ invoice_id: invoice.id, amount, method, paid_at: new Date() });
    // تحديث حالة مبسطة
    // يمكن حساب الرصيد المدفوع من جدول المدفوعات عند الحاجة
    res.status(201).json(payment);
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'server error' });
  }
});

module.exports = router;