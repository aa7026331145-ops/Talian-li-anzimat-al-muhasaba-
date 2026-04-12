name=backend/models/payment.js
module.exports = (sequelize, DataTypes) => {
  return sequelize.define('Payment', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    invoice_id: DataTypes.INTEGER,
    amount: DataTypes.DECIMAL,
    method: DataTypes.STRING,
    paid_at: DataTypes.DATE
  }, { tableName: 'payments', timestamps: true });
};