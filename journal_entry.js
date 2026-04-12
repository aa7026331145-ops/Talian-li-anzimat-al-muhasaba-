name=backend/models/journal_entry.js
module.exports = (sequelize, DataTypes) => {
  return sequelize.define('JournalEntry', {
    id: { type: DataTypes.INTEGER, primaryKey: true, autoIncrement: true },
    tenant_id: DataTypes.INTEGER,
    entry_date: DataTypes.DATE,
    description: DataTypes.TEXT
  }, { tableName: 'journal_entries', timestamps: true });
};