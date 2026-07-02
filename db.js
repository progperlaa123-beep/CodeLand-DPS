const mysql = require("mysql2");

const db = mysql.createConnection({
  host: "localhost",
  user: "root",
  password: "",
  database: "codeland_dps"
});

db.connect((err) => {
  if (err) {
    console.error("❌ Error de conexión:", err);
    return;
  }
  console.log("🟢 Conectado a MySQL - CodeLand DPS");
});

module.exports = db;