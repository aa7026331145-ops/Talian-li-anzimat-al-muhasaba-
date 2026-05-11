name=backend/routes/auth.js
const express = require('express');
const router = express.Router();
const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const { User, Tenant } = require('../models');
const SECRET = process.env.JWT_SECRET || 'supersecretkey';
const SALT_ROUNDS = 10;

// تسجيلTenant + مستخدم Admin أولي (endpoint بسيط للـMVP - يمكن تحسينه)
router.post('/register', async (req, res) => {
  try {
    const { tenantName, name, email, password } = req.body;
    if(!tenantName || !name || !email || !password) return res.status(400).json({ error: 'missing fields' });
    const tenant = await Tenant.create({ name: tenantName });
    const hash = await bcrypt.hash(password, SALT_ROUNDS);
    const user = await User.create({ name, email, password_hash: hash, role: 'admin', tenant_id: tenant.id });
    const token = jwt.sign({ id: user.id }, SECRET, { expiresIn: '7d' });
    res.json({ token, tenant_id: tenant.id });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'server error' });
  }
});

// تسجيل دخول
router.post('/login', async (req, res) => {
  try {
    const { email, password } = req.body;
    const user = await User.findOne({ where: { email } });
    if(!user) return res.status(400).json({ error: 'invalid credentials' });
    const ok = await bcrypt.compare(password, user.password_hash);
    if(!ok) return res.status(400).json({ error: 'invalid credentials' });
    const token = jwt.sign({ id: user.id }, SECRET, { expiresIn: '7d' });
    res.json({ token, tenant_id: user.tenant_id, role: user.role });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'server error' });
  }
});

module.exports = router;