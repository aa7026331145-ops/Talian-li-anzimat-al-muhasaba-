name=backend/models/journal_line.js
module.exports = (sequelize, DataTypes) => {
  return sequelize.define('JournalLine', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    journal_entry_id: DataTypes.INTEGER,
    account_id: DataTypes.INTEGER,
    debit: { type: DataTypes.DECIMAL, defaultValue: 0 },
    credit: { type: DataTypes.DECIMAL, defaultValue: 0 }
  }, { tableName: 'journal_lines', timestamps: false });
};