name=backend/models/product.js
module.exports = (sequelize, DataTypes) => {
  return sequelize.define('Product', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    tenant_id: DataTypes.INTEGER,
    name: DataTypes.STRING,
    sku: DataTypes.STRING,
    price: DataTypes.DECIMAL,
    tax_rate: { type: DataTypes.DECIMAL, defaultValue: 0 },
    stock: { type: DataTypes.DECIMAL, defaultValue: 0 }
  }, { tableName: 'products', timestamps: true });
};