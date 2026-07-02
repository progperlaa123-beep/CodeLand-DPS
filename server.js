const express = require('express');
const mysql = require('mysql2'); 
const cors = require('cors');
const validateEmail = require('deep-email-validator'); // 🔥 IMPORTACIÓN DE LA LIBRERÍA DE VALIDACIÓN

let alumnosRoutes;
try {
    alumnosRoutes = require('./routes/alumnos.routes');
} catch (e) {
    console.warn("⚠️ Aviso: No se encontró el archivo './routes/alumnos'. Las rutas de diagnóstico se configurarán localmente.");
}

const app = express();

// ==========================================================================
// CONFIGURACIÓN DE MIDDLEWARES & CORS (Adaptable a Local y Producción)
// ==========================================================================
app.use(cors({
    origin: [
        "http://localhost", 
        "http://127.0.0.1",
        /\.infinityfreeapp\.com$/ 
    ],
    credentials: true
})); 

app.use(express.json()); 

// ==========================================================================
// CONEXIÓN A LA BASE DE DATOS MYSQL (Detecta si está en la nube o local)
// ==========================================================================
const isLocal = process.env.NODE_ENV !== 'production' && !process.env.RENDER;

const dbConfig = isLocal ? {
    host: 'localhost',
    user: 'root',
    password: '',
    database: 'codeland_dps' 
} : {
    host: 'unaux_XXXXXX', // Tus credenciales de producción reales
    user: 'unaux_XXXXXX',
    password: 'your_password',
    database: 'unaux_XXXXXX_codeland_dps'
};

const connection = mysql.createConnection(dbConfig);

connection.connect((err) => {
    if (err) {
        console.error("❌ Error conectando a la base de datos MySQL:", err);
        return;
    }
    console.log(`🌌 Conexión exitosa a MySQL (Entorno: ${isLocal ? 'Local XAMPP' : 'Producción InfinityFree'})`);
});

// ==========================================================================
// 🔥 MICROSERVICIO: VALIDACIÓN ESTRICTA DE CORREO INSTITUCIONAL REAL
// ==========================================================================
app.post('/api/alumnos/validar-correo-institucional', async (req, res) => {
    const { correo } = req.body;

    if (!correo) {
        return res.status(400).json({ valido: false, error: "El campo correo espacial es obligatorio." });
    }

    // 1. Filtro estricto del dominio institucional con Expresión Regular
    const regexInstitucional = /^[a-zA-Z0-9._%+-]+@cbtis171\.edu\.mx$/;
    if (!regexInstitucional.test(correo)) {
        return res.status(400).json({ 
            valido: false, 
            error: "Acceso denegado. Debes utilizar estrictamente tu correo institucional @cbtis171.edu.mx" 
        });
    }

    try {
        // 2. Ping profundo al buzón de correos (Handshake SMTP directo al servidor escolar)
        const resultado = await validateEmail.validate({
            email: correo,
            validateRegex: true,
            validateMx: true,     // Verifica que cbtis171.edu.mx tenga servidores de correo activos
            validateTypo: false,  // Desactivado para evitar falsos positivos en entornos educativos
            validateSMTP: true    // ¡HACE EL PING AL BUZÓN INDIVIDUAL REAL!
        });

        if (!resultado.valid) {
            console.log(`❌ Correo INVENTADO bloqueado: ${correo}. Razón: ${resultado.reason}`);
            return res.status(400).json({ 
                valido: false, 
                error: "El correo tiene el formato correcto, pero el buzón NO existe o está inactivo en el servidor escolar." 
            });
        }

        // 3. El correo es real. Ahora verificamos que no esté duplicado en MySQL para ahorrarle trabajo a PHP
        const sqlVerificar = "SELECT id_usuario FROM usuarios WHERE correo = ?";
        connection.query(sqlVerificar, [correo], (err, results) => {
            if (err) {
                return res.status(500).json({ valido: false, error: "Error de comunicación con la base de datos." });
            }
            if (results.length > 0) {
                return res.status(400).json({ valido: false, error: "Este correo espacial ya se encuentra registrado en la Flota." });
            }

            console.log(`🚀 Correo REAL verificado con éxito: ${correo}`);
            return res.json({ valido: true });
        });

    } catch (error) {
        console.error("❌ Error en la validación SMTP:", error);
        // Si el DNS local falla o hay timeout en el puerto 25, devolvemos error para bloquear registros dudosos
        return res.status(500).json({ valido: false, error: "Controlador SMTP ocupado. Intenta registrarte de nuevo." });
    }
});

// ==========================================================================
// RUTA ANTERIOR: GUARDAR PROGRESO GAMIFICADO
// ==========================================================================
app.post('/api/progreso/guardar', (req, res) => {
    const { id_alumno, id_leccion, xp_ganada } = req.body;

    if (!id_alumno || !id_leccion || !xp_ganada) {
        return res.status(400).json({ error: "Faltan parámetros de telemetría" });
    }

    const queryProgreso = `INSERT INTO progreso_lecciones (id_alumno, id_leccion, fecha_completado) 
                           VALUES (?, ?, NOW())`;

    connection.query(queryProgreso, [id_alumno, id_leccion], (errProgreso) => {
        if (errProgreso) {
            console.error("Error en tabla progreso:", errProgreso);
            return res.status(500).json({ error: "Fallo en la base de datos al guardar progreso" });
        }

        const queryXP = `INSERT INTO perfil_gamificado (id_alumno, xp_total, nivel) VALUES (?, ?, 'Alumno de la Flota (Zona 1)')
                         ON DUPLICATE KEY UPDATE xp_total = xp_total + ?`;

        connection.query(queryXP, [id_alumno, xp_ganada, xp_ganada], (errXP, resultXP) => {
            if (errXP) {
                console.error("Error en tabla XP:", errXP);
                return res.status(500).json({ error: "Fallo al acreditar XP real" });
            }

            if (parseInt(id_leccion) === 10) {
                const queryAscenso = `UPDATE perfil_gamificado SET nivel = 'Intermedio' WHERE id_alumno = ?`;
                connection.query(queryAscenso, [id_alumno], (errAscenso) => {
                    if (errAscenso) console.error("Error al ascender nivel del alumno:", errAscenso);
                });
            }

            res.json({ status: "Sincronización Exitosa", mensaje: "Telemetría guardada en MySQL" });
        });
    });
});

// Integración condicional de las rutas adicionales si existen
if (alumnosRoutes) {
    app.use('/api/alumnos', alumnosRoutes);
}
app.post('/api/procesar_leccion_completada.php', (req, res) => {
    // Capturamos la nueva variable desde el body enviado por el HTML
    const { id_leccion, xp_ganada, tiempo_segundos } = req.body;
    const id_alumno = 1; // ID de sesión del alumno

    // Ejemplo de consulta SQL guardando el tiempo acumulado de resolución
    const queryProgreso = `
        INSERT INTO lecciones_completadas (id_alumno, id_leccion, tiempo_empleado) 
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE tiempo_empleado = ?`;

    connection.query(queryProgreso, [id_alumno, id_leccion, tiempo_segundos, tiempo_segundos], (err, result) => {
        if (err) return res.status(500).json({ success: false, error: err.message });
        res.json({ success: true, mensaje: "Telemetría y tiempo guardados con éxito" });
    });
});
app.post('/api/guardar-avance', (req, res) => {
    const { id_alumno, id_leccion, xp_ganada } = req.body;

    if (!id_alumno || !id_leccion) {
        return res.status(400).json({ error: "Parámetros insuficientes para la telemetría." });
    }

    // 1. Insertar el avance de la lección de forma única
    const sqlAvance = `INSERT IGNORE INTO avance_lecciones (id_alumno, id_leccion) VALUES (?, ?)`;
    
    connection.query(sqlAvance, [id_alumno, id_leccion], (errAvance, resultAvance) => {
        if (errAvance) {
            console.error("❌ Error en avance_lecciones:", errAvance);
            return res.status(500).json({ error: "Fallo en registro de avance." });
        }

        // 2. Acreditar XP en el perfil gamificado
        const sqlXP = `
            INSERT INTO perfil_gamificado (id_alumno, xp_total) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE xp_total = xp_total + ?
        `;

        connection.query(sqlXP, [id_alumno, xp_ganada, xp_ganada], (errXP) => {
            if (errXP) console.error("⚠️ Error actualizando XP:", errXP);
            
            res.json({ success: true, mensaje: "Progreso espacial sincronizado correctamente." });
        });
    });
});

// ==========================================================================
// REGISTRAR INCIDENCIAS Y ERRORES LOGICOS (Para Analíticas del Docente)
// ==========================================================================
app.post('/api/registrar-error', (req, res) => {
    const { id_alumno, id_leccion, error_mensaje } = req.body;

    if (!id_alumno || !id_leccion || !error_mensaje) {
        return res.status(400).json({ error: "Faltan componentes en el reporte de error." });
    }

    const sqlError = `INSERT INTO errores_navegacion (id_alumno, id_leccion, error_mensaje) VALUES (?, ?, ?)`;
    
    connection.query(sqlError, [id_alumno, id_leccion, error_mensaje], (err) => {
        if (err) {
            console.error("❌ Error al guardar log en MySQL:", err);
            return res.status(500).json({ error: "No se guardó el log." });
        }
        res.json({ success: true, mensaje: "Error registrado en los logs del sector docente." });
    });
});
// INICIALIZACIÓN DEL SERVIDOR
const PUERTO = process.env.PORT || 3000; 
app.listen(PUERTO, () => {
    console.log(`🚀 Servidor de CodeLand DPS corriendo en el hiperespacio: Puerto ${PUERTO}`);
}); 