name=backend/server.js
// نقطة بداية backend - Express + Sequelize
require('dotenv').config();
const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const { sequelize } = require('./models');

const authRoutes = require('./routes/auth');
const customerRoutes = require('./routes/customers');
const productRoutes = require('./routes/products');
const invoiceRoutes = require('./routes/invoices');

const app = express();
app.use(cors());
app.use(bodyParser.json());

// Routes
app.use('/api/auth', authRoutes);
app.use('/api/customers', customerRoutes);
app.use('/api/products', productRoutes);
app.use('/api/invoices', invoiceRoutes);

app.get('/api/health', (req, res) => res.json({ ok: true }));

const PORT = process.env.PORT || 4000;

(async () => {
  try {
    await sequelize.authenticate();
    // sync جميع الموديلات (MVP)
    await sequelize.sync({ alter: true });
    console.log('Database connected and synced');
    app.listen(PORT, () => console.log(`Backend running on ${PORT}`));
  } catch (err) {
    console.error('Unable to start server:', err);
  }
})();