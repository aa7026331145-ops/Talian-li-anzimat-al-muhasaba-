name=backend/models/tenant.js
module.exports = (sequelize, DataTypes) => {
  return sequelize.define('Tenant', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    name: { type: DataTypes.STRING, allowNull: false },
    subdomain: { type: DataTypes.STRING, allowNull: true }
  }, {
    tableName: 'tenants',
    timestamps: true
  });
};