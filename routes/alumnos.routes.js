const express = require("express");
const router = express.Router();
const controller = require("../controllers/alumnos.controller");

console.log("🟡 ROUTE alumnos cargada");

// Rutas existentes
router.get("/:id/nivel", controller.obtenerNivelAlumno);
router.get("/:id/verificar-diagnostico", controller.verificarDiagnostico);

// 🔥 NUEVA RUTA ASIGNADA
router.post("/validar-correo-institucional", controller.validarCorreoReal);

module.exports = router;