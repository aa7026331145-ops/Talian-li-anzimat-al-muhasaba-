name=backend/models/invoice_item.js
module.exports = (sequelize, DataTypes) => {
  return sequelize.define('InvoiceItem', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    invoice_id: DataTypes.INTEGER,
    product_id: DataTypes.INTEGER,
    description: DataTypes.TEXT,
    quantity: DataTypes.DECIMAL,
    unit_price: DataTypes.DECIMAL,
    tax: DataTypes.DECIMAL,
    line_total: DataTypes.DECIMAL
  }, { tableName: 'invoice_items', timestamps: false });
};