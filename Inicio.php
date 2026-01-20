<?php
// Suprimir notices de PHP
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$usuario_registrado = isset($_SESSION['usuario_registrado']) && $_SESSION['usuario_registrado'] === true;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
</head>
<body>

    <header class="top-bar">
        <nav class="nav">
            <div class="logo">
                <img src="Img/imagen1.png" alt="Logo">
                <span>Sistema de Citas</span>
            </div>

            <ul class="menu">
                <li><a href="Inicio.php" class="active">Inicio</a></li>
                <?php if ($usuario_registrado) { ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="historial-citas.php">Mis Citas</a></li>
                    <li><a href="recordatorios.php">Recordatorios</a></li>
                <?php } ?>
                <li><a href="doctores.php">Doctores</a></li>
                <li><a href="sobre-nosotros.html">Sobre Nosotros</a></li>
                <li><a href="contacto.html">Contacto</a></li>
                <?php if ($usuario_registrado) { ?>
                    <li><a href="logout.php" class="logout">Cerrar Sesión</a></li>
                <?php } else { ?>
                    <li><a href="Formulario.html">Registro</a></li>
                <?php } ?>
            </ul>
        </nav>
    </header>

    <main class="home fade-in">

        <section class="hero">
            <h1>Bienvenido al Sistema de Citas Médicas</h1>
            <p>Gestione sus citas médicas de forma rápida, segura y sencilla.</p>
        </section>

        <section class="features">
            <div class="card">
                <h3>Registro de Pacientes</h3>
                <p>Inscriba pacientes de manera rápida y segura.</p>
            </div>

            <div class="card">
                <h3>Gestión de Citas</h3>
                <p>Organice y controle las citas médicas fácilmente.</p>
            </div>

            <div class="card">
                <h3>Historial Médico</h3>
                <p>Acceda al historial médico de cada paciente.</p>
            </div>
        </section>

    </main>

    <footer class="footer">
        <p>© 2026 Sistema de Citas Médicas | AppoinMed</p>
    </footer>

</body>
</html>
