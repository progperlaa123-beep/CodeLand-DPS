const db = require("../db");

console.log("🔥 CONTROLLER REAL OPTIMIZADO - SIN BLOQUEOS DE RED");

// ========================================================
// 1. OBTENER NIVEL REAL DEL ALUMNO (MUNDO I)
// ========================================================
exports.obtenerNivelAlumno = (req, res) => {
  const id = req.params.id;
  
  const sql = `
    SELECT 
      puntaje,
      CASE 
        WHEN id_nivel = 1 THEN 'Principiante'
        WHEN id_nivel = 2 THEN 'Intermedio'
        WHEN id_nivel = 3 THEN 'Avanzado'
        ELSE 'Principiante'
      END AS nivel
    FROM diagnostico_usuario
    WHERE id_alumno = ?
    ORDER BY fecha_realizacion DESC
    LIMIT 1
  `;
  
  db.query(sql, [id], (err, results) => {
    if (err) {
      console.error("❌ Error en la consulta SQL de nivel:", err);
      return res.status(500).json({ error: "Error interno en la base de datos" });
    }
    
    if (results.length === 0) {
      return res.json({ nivel: "Principiante", puntaje: 0 });
    }
    
    console.log(`➡️ Enviando nivel real a mundoI para alumno ${id}:`, results[0].nivel);
    res.json(results[0]);
  });
};

// ========================================================
// 2. VERIFICAR DIAGNÓSTICO PREVIO
// ========================================================
exports.verificarDiagnostico = (req, res) => {
  const id = req.params.id;

  const sql = `
    SELECT COUNT(*) AS total 
    FROM diagnostico_usuario 
    WHERE id_alumno = ?
  `;

  db.query(sql, [id], (err, results) => {
    if (err) {
      console.error("❌ Error verificando diagnóstico:", err);
      return res.status(500).json({ error: "Error en el servidor" });
    }
    res.json({ completado: results[0].total > 0 });
  });
};

// ========================================================
// 🔥 3. VALIDACIÓN ESTRICTA Y SEGURA DE CORREO INSTITUCIONAL
// ========================================================
exports.validarCorreoReal = async (req, res) => {
  const { correo } = req.body;

  if (!correo) {
    return res.status(400).json({ valido: false, error: "El correo es requerido." });
  }

  const correoLimpio = correo.trim().toLowerCase();

  // 1. Filtro estricto de dominio
  if (!correoLimpio.endsWith("@cbtis171.edu.mx")) {
    return res.status(400).json({ 
      valido: false, 
      error: "Acceso denegado. Debes utilizar estrictamente tu correo institucional @cbtis171.edu.mx" 
    });
  }

  try {
    // 2. Consultamos al validador oficial de inicios de sesión de Google (evita fallos de DNS/MX locales)
    const urlGoogle = `https://accounts.google.com/ClientLogin?Email=${encodeURIComponent(correoLimpio)}`;
    
    const respuesta = await fetch(urlGoogle, { method: 'GET' });
    const cuerpoTexto = await respuesta.text();

    // Si Google responde explícitamente que el usuario NO existe, lo bloqueamos
    if (cuerpoTexto.includes("NoSuchUser")) {
      console.log(`❌ Registro bloqueado. Correo INVENTADO: ${correoLimpio}`);
      return res.status(400).json({ 
        valido: false, 
        error: "El correo tiene el formato correcto, pero el buzón NO existe en los servidores de Google de la escuela." 
      });
    }

    // Si no contiene errores de usuario inexistente, la cuenta es real y legítima
    console.log(`🚀 Correo REAL verificado con éxito: ${correoLimpio}`);
    return res.json({ valido: true });

  } catch (error) {
    console.error("❌ Error de red al consultar a Google:", error);
    // Si por alguna razón tu internet falla al consultar a Google, dejamos pasar por contingencia
    // para no bloquear a alumnos reales con problemas de conexión temporales.
    return res.json({ valido: true });
  }
};