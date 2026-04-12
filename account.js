name=backend/models/account.js
module.exports = (sequelize, DataTypes) => {
  return sequelize.define('Account', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    tenant_id: DataTypes.INTEGER,
    name: DataTypes.STRING,
    code: DataTypes.STRING,
    type: DataTypes.STRING,
    parent_id: DataTypes.INTEGER
  }, { tableName: 'accounts', timestamps: true });
};