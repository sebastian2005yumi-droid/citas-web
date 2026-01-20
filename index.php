<?php
// Suprimir notices de PHP
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Verificar si el usuario está registrado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_registrado']) || $_SESSION['usuario_registrado'] !== true) {
    // Redirigir a la página de verificación de sesión
    include 'verificar_sesion.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agendar Cita - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
</head>
<body>

    <!-- ENCABEZADO -->
    <header class="top-bar">
        <nav class="nav">
            <div class="logo">
                <img src="Img/imagen1.png" alt="Logo">
                <span>Sistema de Citas</span>
            </div>
            <ul class="menu">
                <li><a href="Inicio.html">Inicio</a></li>
                <li><a href="Formulario.html">Registro</a></li>
                <li><a href="index.php" class="active">Agendar</a></li>
                <li><a href="Lista.html">Servicios</a></li>
                <li><a href="Tablas.html">Procesos</a></li>
            </ul>
        </nav>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="contenedor-principal fade-in">

        <section class="formulario-seccion">
            <h2>📋 Agendar una Cita</h2>
            <p style="color: #666; font-size: 14px; text-align: center; margin-bottom: 20px;">
                ✓ Registrado como: <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong>
            </p>

            <form class="formulario" action="registrar.php" method="POST">
                <input type="hidden" name="fuente" value="agendamiento_rapido">
                
                <div>
                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>" readonly>
                </div>

                <div>
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($_SESSION['usuario_correo']); ?>" readonly>
                </div>

                <div>
                    <label for="fecha">Fecha de la cita</label>
                    <input type="date" id="fecha" name="fecha" required>
                </div>

                <div>
                    <label for="mensaje">Observaciones</label>
                    <textarea id="mensaje" name="mensaje" placeholder="Describa brevemente sus síntomas o necesidad médica..."></textarea>
                </div>

                <button type="submit">✓ Enviar Cita</button>
            </form>
        </section>

    </main>

    <footer class="footer">
        <p>© 2026 Sistema de Citas Médicas | AppoinMed</p>
    </footer>

</body>
</html>
