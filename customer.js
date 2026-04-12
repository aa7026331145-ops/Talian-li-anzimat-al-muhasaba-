name=backend/models/customer.js
module.exports = (sequelize, DataTypes) => {
  return sequelize.define('Customer', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    tenant_id: DataTypes.INTEGER,
    name: DataTypes.STRING,
    email: DataTypes.STRING,
    phone: DataTypes.STRING,
    address: DataTypes.TEXT,
    balance: { type: DataTypes.DECIMAL, defaultValue: 0 }
  }, { tableName: 'customers', timestamps: true });
};