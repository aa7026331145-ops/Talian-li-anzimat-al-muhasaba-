name=backend/routes/products.js
const express = require('express');
const router = express.Router();
const { Product } = require('../models');
const auth = require('../middleware/auth');
const tenant = require('../middleware/tenant');

router.get('/', auth, tenant, async (req, res) => {
  const products = await Product.findAll({ where: { tenant_id: req.tenantId } });
  res.json(products);
});

router.post('/', auth, tenant, async (req, res) => {
  const { name, sku, price, tax_rate, stock } = req.body;
  const p = await Product.create({ tenant_id: req.tenantId, name, sku, price, tax_rate, stock });
  res.status(201).json(p);
});

module.exports = router;