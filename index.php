<?php
session_start();
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'alumno') {
    header("Location: mundoI.html");
    exit();
}
if (isset($_SESSION['rol']) && $_SESSION['rol'] === 'docente') {
    header("Location: panel_docente.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeLand DPS</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="Style_CL.css">
</head>

<body>

    <div class="contenedor">

        <header class="navbar">

            <div class="logo">
                <span class="azul">CodeLand</span>
                <span class="verde">DPS</span>
            </div>

            <nav class="menu">
                <?php if (isset($_SESSION['rol'])): ?>
                    <?php if ($_SESSION['rol'] === 'alumno'): ?>
                        <a href="mundoI.html">Inicio (Mapa)</a>
                        <a href="perfil.php">Mi Perfil</a>
                    <?php endif; ?>

                    <?php if ($_SESSION['rol'] === 'docente'): ?>
                        <a href="panel_docente.php" style="color: #a855f7; font-weight: 600;">⚙️ Panel Docente</a>
                    <?php endif; ?>
                    
                    <a href="logout.php" class="btn-logout-nav">Cerrar Sesión</a>

                <?php else: ?>
                    <a href="#" onclick="abrirNosotros(); return false;">Nosotros</a>
                    <a href="#" onclick="abrirCursos(); return false;">Cursos</a>
                    <a href="#" onclick="abrirContacto(); return false;">Contacto</a>
                <?php endif; ?>
            </nav>          
        </header>
        <div class="linea"></div>

        <section class="hero">

            <div class="contenido-texto">

                <h1>
                    Aprende Programación <br>
                    Explorando Nuevos <br>
                    Mundos
                </h1>

                <p>
                    Desarrolla tu potencial en la tierra del código y
                    aprende habilidades de programación
                </p>

                <?php if (isset($_SESSION['rol'])): ?>
                    <?php if ($_SESSION['rol'] === 'alumno'): ?>
                        <a href="mundo1.php" class="btn-aventura">Regresar al Mapa</a>
                    <?php else: ?>
                        <a href="panel_docente.php" class="btn-aventura">Ir al Panel</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="InicioSesion.html" class="btn-aventura">Comenzar aventura</a>
                <?php endif; ?>

            </div>

            <div class="imagen-planeta">
                <img src="CodeLandDPS_logo (1).png" alt="Planeta">
            </div>

        </section>

        <section class="cards">

            <div class="card">
                <p>
                    Desarrolla software de aplicación con
                    programación estructurada
                </p>
            </div>

            <div class="card">
                <p>
                    Contenido en micro lecciones
                    interactivas de 5 a 10 minutos
                </p>
            </div>

            <div class="card">
                <p>
                    Actividades gamificadas para la
                    práctica de habilidades
                </p>
            </div>

        </section>

    </div>

    <div id="modalContacto" class="modal-emergente" style="display:none;">
        <div class="modal-content">
            <span class="close-btn" onclick="cerrarContacto()">&times;</span>
            <h2>Contacto Soporte CodeLand DPS</h2>
            <p>¿Tienes dudas o problemas con la plataforma? ¡Contáctanos!</p>
            <hr style="border-color: #334155; margin: 15px 0;">
            <p>📍 <strong>Ubicación:</strong> Abasolo, Guanajuato, México</p>
            <p>📧 <strong>Email:</strong> codelanddps@gmail.com</p>
            <p>📞 <strong>Teléfono:</strong> +52 (429) 119-5835</p>
        </div>
    </div>
    <div id="modalNosotros" class="modal-emergente" style="display:none;">
        <div class="modal-content">
            <span class="close-btn" onclick="cerrarNosotros()">&times;</span>
            <h2>Sobre CodeLand DPS</h2>
            <p>Conoce el propósito pedagógico detrás de nuestra plataforma de aprendizaje gamificado.</p>
            <hr style="border-color: #334155; margin: 15px 0;">
            
            <p style="margin-bottom: 15px;">
                🚀 <strong>Misión:</strong> <br>
                Transformar la enseñanza de la lógica computacional en la Educación Media Superior Tecnológica mediante entornos interactivos y microlecciones gamificadas. Buscamos reducir los niveles de frustración de los estudiantes de programación de segundo semestre, permitiéndoles asimilar conceptos de programación estructurada de manera ágil, lúdica y autónoma.
            </p>
            
            <p style="margin-bottom: 10px;">
                ✨ <strong>Visión:</strong> <br>
                Consolidarse como el prototipo didáctico de referencia dentro del bachillerato tecnológico (DGETI), impulsando el desarrollo del pensamiento lógico-computacional previo a la codificación y asegurando que ningún estudiante abandone su trayectoria técnica por la complejidad abstracta del software.
            </p>
        </div>
    </div>

    <div id="modalCursos" class="modal-emergente" style="display:none;">
        <div class="modal-content" style="max-width: 600px;"> <span class="close-btn" onclick="cerrarCursos()">&times;</span>
            <h2>Trayectoria Académica: Módulo I</h2>
            <p><strong>Carrera Técnica:</strong> Programación (Plan de Estudios DGETI)</p>
            <hr style="border-color: #334155; margin: 15px 0;">
            
            <p style="margin-bottom: 15px; color: #a7f3d0;">
                🎯 <strong>Módulo I: Desarrolla software de aplicación con programación estructurada (272 horas)</strong>
            </p>
            
            <div style="text-align: left; font-size: 0.95em; line-height: 1.5em;">
                <p style="margin-bottom: 10px;">
                    🛠️ <strong>Submódulo 1: Construye algoritmos para la solución de problemas (80 hrs).</strong><br>
                    <span style="color: #94a3b8;">Foco de Mundo I: Diseño de metodologías lógicas, finitas y unívocas mediante pseudocódigo y diagramas de flujo.</span>
                </p>
                <p style="margin-bottom: 10px;">
                    💻 <strong>Submódulo 2: Codifica programas en un lenguaje estructurado (112 hrs).</strong><br>
                    <span style="color: #94a3b8;">Traducción de algoritmos conceptuales a sintaxis de control real, variables y estructuras algorítmicas secuenciales y condicionales.</span>
                </p>
                <p style="margin-bottom: 5px;">
                    📊 <strong>Submódulo 3: Aplica estructuras de datos con un lenguaje de programación (80 hrs).</strong><br>
                    <span style="color: #94a3b8;">Organización y manipulación eficiente de información en arreglos unidimensionales, multidimensionales y registros.</span>
                </p>
            </div>
        </div>
    </div>

    <script>
        function abrirContacto() {
            document.getElementById("modalContacto").style.display = "flex";
        }

        function cerrarContacto() {
            document.getElementById("modalContacto").style.display = "none";
        }

        function abrirNosotros() {
            document.getElementById("modalNosotros").style.display = "flex";
        }

        function cerrarNosotros() {
            document.getElementById("modalNosotros").style.display = "none";
        }

        function abrirCursos() {
            document.getElementById("modalCursos").style.display = "flex";
        }

        function cerrarCursos() {
            document.getElementById("modalCursos").style.display = "none";
        }

        window.onclick = function(event) {
            var modalContacto = document.getElementById("modalContacto");
            var modalNosotros = document.getElementById("modalNosotros");
            var modalCursos = document.getElementById("modalCursos");

            if (event.target == modalContacto) { modalContacto.style.display = "none"; }
            if (event.target == modalNosotros) { modalNosotros.style.display = "none"; }
            if (event.target == modalCursos) { modalCursos.style.display = "none"; }
        }
    </script>

</body>
</html>
