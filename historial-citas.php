<?php
// Suprimir notices de PHP
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Verificar si el usuario está registrado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no está registrado, redirigir a registro
if (!isset($_SESSION['usuario_registrado']) || $_SESSION['usuario_registrado'] !== true) {
    header("Location: Formulario.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Citas - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
    <style>
        .historial-container {
            max-width: 1200px;
            margin: 140px auto 80px;
            padding: 0 30px;
        }

        .historial-header {
            margin-bottom: 50px;
        }

        .historial-header h1 {
            color: #0d47a1;
            font-size: 36px;
            margin: 0 0 10px 0;
            font-weight: 700;
        }

        .historial-header p {
            color: #666;
            font-size: 16px;
            margin: 0;
        }

        .tabs {
            display: flex;
            gap: 0;
            margin-bottom: 40px;
            border-bottom: 2px solid #e3f2fd;
        }

        .tab {
            padding: 15px 30px;
            background: none;
            border: none;
            color: #666;
            font-weight: 600;
            cursor: pointer;
            font-size: 15px;
            transition: all 0.3s ease;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
        }

        .tab:hover {
            color: #1976d2;
        }

        .tab.active {
            color: #0d47a1;
            border-bottom-color: #1976d2;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .cita-lista {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .cita-item {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border-left: 5px solid #1976d2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .cita-item:hover {
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.12);
            transform: translateY(-3px);
        }

        .cita-item.completada {
            border-left-color: #4caf50;
        }

        .cita-item.cancelada {
            border-left-color: #f44336;
            opacity: 0.7;
        }

        .cita-detalles {
            flex: 1;
        }

        .cita-doctor {
            color: #0d47a1;
            font-size: 20px;
            font-weight: 700;
            margin: 0 0 10px 0;
        }

        .cita-info {
            color: #666;
            font-size: 14px;
            line-height: 1.8;
            margin: 0;
        }

        .cita-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 13px;
            margin-top: 10px;
        }

        .status-pendiente {
            background: #fff3e0;
            color: #e65100;
        }

        .status-completada {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-cancelada {
            background: #ffebee;
            color: #c62828;
        }

        .cita-acciones {
            display: flex;
            gap: 10px;
            flex-direction: column;
            min-width: 150px;
        }

        .cita-acciones a, .cita-acciones button {
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
        }

        .btn-editar {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #1976d2;
        }

        .btn-editar:hover {
            background: #bbdefb;
        }

        .btn-cancelar {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #f44336;
        }

        .btn-cancelar:hover {
            background: #ffcdd2;
        }

        .vacio {
            text-align: center;
            padding: 80px 40px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        .vacio-icono {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .vacio h2 {
            color: #0d47a1;
            margin: 20px 0 10px 0;
            font-size: 24px;
        }

        .vacio p {
            color: #999;
            margin: 0 0 30px 0;
        }

        .vacio a {
            display: inline-block;
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
            color: white;
            padding: 12px 28px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .vacio a:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(21, 101, 192, 0.4);
        }

        @media (max-width: 900px) {
            .historial-container {
                margin: 110px auto 60px;
                padding: 0 20px;
            }

            .cita-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .cita-acciones {
                width: 100%;
                margin-top: 15px;
                flex-direction: row;
            }

            .cita-acciones a, .cita-acciones button {
                flex: 1;
            }
        }

        @media (max-width: 600px) {
            .historial-container {
                margin: 100px auto 40px;
                padding: 0 15px;
            }

            .historial-header h1 {
                font-size: 28px;
            }

            .tabs {
                flex-direction: column;
                border-bottom: none;
            }

            .tab {
                padding: 12px 0;
                border-bottom: none;
                border-bottom: 2px solid #e3f2fd;
                margin-bottom: 0;
            }

            .tab.active {
                border-bottom: 2px solid #1976d2;
            }

            .cita-item {
                padding: 20px;
            }

            .cita-doctor {
                font-size: 18px;
            }
        }
    </style>
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
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="historial-citas.php" class="active">Mis Citas</a></li>
                <li><a href="doctores.php">Doctores</a></li>
                <li><a href="contacto.html">Contacto</a></li>
                <li><a href="logout.php" class="logout">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="historial-container fade-in">
        <!-- HEADER -->
        <div class="historial-header">
            <h1>📅 Mis Citas</h1>
            <p>Gestiona y consulta el historial de todas tus citas médicas.</p>
        </div>

        <!-- TABS -->
        <div class="tabs">
            <button class="tab active" onclick="mostrarTab(event, 'pendientes')">Próximas</button>
            <button class="tab" onclick="mostrarTab(event, 'completadas')">Completadas</button>
            <button class="tab" onclick="mostrarTab(event, 'canceladas')">Canceladas</button>
        </div>

        <!-- TAB: PENDIENTES -->
        <div id="pendientes" class="tab-content active">
            <div class="vacio">
                <div class="vacio-icono">📋</div>
                <h2>No hay citas próximas</h2>
                <p>No tienes citas agendadas en este momento.</p>
                <a href="index.php">+ Agendar Nueva Cita</a>
            </div>
        </div>

        <!-- TAB: COMPLETADAS -->
        <div id="completadas" class="tab-content">
            <div class="vacio">
                <div class="vacio-icono">✓</div>
                <h2>Sin citas completadas</h2>
                <p>No tienes citas completadas aún.</p>
                <a href="index.php">+ Agendar Cita</a>
            </div>
        </div>

        <!-- TAB: CANCELADAS -->
        <div id="canceladas" class="tab-content">
            <div class="vacio">
                <div class="vacio-icono">✕</div>
                <h2>Sin citas canceladas</h2>
                <p>No has cancelado ninguna cita.</p>
                <a href="index.php">+ Agendar Cita</a>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <p>© 2026 Sistema de Citas Médicas | AppoinMed</p>
    </footer>

    <script>
        function mostrarTab(event, tabName) {
            // Ocultar todos los tabs
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.remove('active'));

            // Remover clase active de todos los botones
            const botones = document.querySelectorAll('.tab');
            botones.forEach(btn => btn.classList.remove('active'));

            // Mostrar el tab seleccionado
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>

</body>
</html>
