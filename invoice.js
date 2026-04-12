name=backend/models/invoice.js
module.exports = (sequelize, DataTypes) => {
  return sequelize.define('Invoice', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    tenant_id: DataTypes.INTEGER,
    invoice_number: { type: DataTypes.STRING, unique: true },
    customer_id: DataTypes.INTEGER,
    status: { type: DataTypes.STRING, defaultValue: 'draft' },
    issued_at: DataTypes.DATE,
    due_at: DataTypes.DATE,
    subtotal: DataTypes.DECIMAL,
    tax: DataTypes.DECIMAL,
    total: DataTypes.DECIMAL
  }, { tableName: 'invoices', timestamps: true });
};