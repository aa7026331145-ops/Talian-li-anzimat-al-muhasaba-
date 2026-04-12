name=backend/routes/customers.js
const express = require('express');
const router = express.Router();
const { Customer } = require('../models');
const auth = require('../middleware/auth');
const tenant = require('../middleware/tenant');

// قائمة العملاء للـtenant
router.get('/', auth, tenant, async (req, res) => {
  const customers = await Customer.findAll({ where: { tenant_id: req.tenantId } });
  res.json(customers);
});

router.post('/', auth, tenant, async (req, res) => {
  const { name, email, phone, address } = req.body;
  const c = await Customer.create({ tenant_id: req.tenantId, name, email, phone, address });
  res.status(201).json(c);
});

module.exports = router;