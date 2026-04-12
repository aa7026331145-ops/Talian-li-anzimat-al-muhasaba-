name=backend/models/user.js
const bcrypt = require('bcrypt');
module.exports = (sequelize, DataTypes) => {
  const User = sequelize.define('User', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    name: DataTypes.STRING,
    email: { type: DataTypes.STRING, unique: true },
    password_hash: DataTypes.STRING,
    role: { type: DataTypes.STRING, defaultValue: 'user' },
    tenant_id: { type: DataTypes.INTEGER }
  }, {
    tableName: 'users',
    timestamps: true
  });

  User.prototype.verifyPassword = function(password){
    return bcrypt.compare(password, this.password_hash);
  };

  return User;
};