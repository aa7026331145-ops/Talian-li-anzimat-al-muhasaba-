name=backend/models/index.js
// تهيئة Sequelize + استيراد الموديلات
const { Sequelize, DataTypes } = require('sequelize');
const DATABASE_URL = process.env.DATABASE_URL || 'postgres://postgres:postgres@localhost:5432/accounting';
const sequelize = new Sequelize(DATABASE_URL, { logging: false });

const User = require('./user')(sequelize, DataTypes);
const Tenant = require('./tenant')(sequelize, DataTypes);
const Customer = require('./customer')(sequelize, DataTypes);
const Product = require('./product')(sequelize, DataTypes);
const Invoice = require('./invoice')(sequelize, DataTypes);
const InvoiceItem = require('./invoice_item')(sequelize, DataTypes);
const Payment = require('./payment')(sequelize, DataTypes);
const Account = require('./account')(sequelize, DataTypes);
const JournalEntry = require('./journal_entry')(sequelize, DataTypes);
const JournalLine = require('./journal_line')(sequelize, DataTypes);

// العلاقات
Tenant.hasMany(User, { foreignKey: 'tenant_id' });
User.belongsTo(Tenant, { foreignKey: 'tenant_id' });

Tenant.hasMany(Customer, { foreignKey: 'tenant_id' });
Customer.belongsTo(Tenant, { foreignKey: 'tenant_id' });

Tenant.hasMany(Product, { foreignKey: 'tenant_id' });
Product.belongsTo(Tenant, { foreignKey: 'tenant_id' });

Tenant.hasMany(Invoice, { foreignKey: 'tenant_id' });
Invoice.belongsTo(Tenant, { foreignKey: 'tenant_id' });
Invoice.hasMany(InvoiceItem, { foreignKey: 'invoice_id' });
InvoiceItem.belongsTo(Invoice, { foreignKey: 'invoice_id' });
Invoice.hasMany(Payment, { foreignKey: 'invoice_id' });

Tenant.hasMany(Account, { foreignKey: 'tenant_id' });
Account.belongsTo(Tenant, { foreignKey: 'tenant_id' });
JournalEntry.hasMany(JournalLine, { foreignKey: 'journal_entry_id' });

module.exports = {
  sequelize,
  Sequelize,
  User,
  Tenant,
  Customer,
  Product,
  Invoice,
  InvoiceItem,
  Payment,
  Account,
  JournalEntry,
  JournalLine
};