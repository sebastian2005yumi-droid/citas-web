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
    <title>Mi Dashboard - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
    <style>
        .nav {
            max-width: 1400px;
            margin: auto;
            padding: 12px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .menu li a.logout {
            background: #d32f2f;
            color: white;
        }

        .menu li a.logout:hover {
            background: #c62828;
        }

        .dashboard-container {
            max-width: 1300px;
            margin: 140px auto 80px;
            padding: 0 30px;
        }

        .bienvenida {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);
            color: white;
            padding: 40px;
            border-radius: 16px;
            margin-bottom: 50px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
        }

        .bienvenida h1 {
            margin: 0 0 10px 0;
            font-size: 32px;
            font-weight: 700;
        }

        .bienvenida p {
            margin: 0;
            font-size: 16px;
            opacity: 0.95;
        }

        .seccion-usuario {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            margin-bottom: 50px;
        }

        .tarjeta {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .tarjeta:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 50px rgba(13, 71, 161, 0.15);
            border-color: #1976d2;
        }

        .tarjeta h3 {
            color: #0d47a1;
            margin: 0 0 15px 0;
            font-size: 20px;
            font-weight: 700;
        }

        .tarjeta-info {
            color: #666;
            font-size: 15px;
            line-height: 1.7;
        }

        .tarjeta-info strong {
            color: #0d47a1;
            display: block;
            margin-top: 10px;
        }

        .acciones-rapidas {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .accion-card {
            background: white;
            padding: 40px;
            border-radius: 14px;
            text-align: center;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: pointer;
            border: 2px solid transparent;
        }

        .accion-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(13, 71, 161, 0.15);
            border-color: #1976d2;
        }

        .accion-card .icono {
            font-size: 60px;
            margin-bottom: 15px;
        }

        .accion-card h3 {
            color: #0d47a1;
            margin: 0 0 10px 0;
            font-size: 22px;
            font-weight: 700;
        }

        .accion-card p {
            color: #666;
            margin: 0 0 20px 0;
            font-size: 14px;
        }

        .accion-card a {
            display: inline-block;
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
            color: white;
            padding: 12px 28px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(21, 101, 192, 0.25);
        }

        .accion-card a:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(21, 101, 192, 0.4);
        }

        .proximas-citas {
            background: white;
            padding: 40px;
            border-radius: 14px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
            margin-bottom: 50px;
        }

        .proximas-citas h2 {
            color: #0d47a1;
            margin: 0 0 30px 0;
            font-size: 26px;
            font-weight: 700;
        }

        .cita-item {
            background: #f8f9fa;
            padding: 20px;
            border-left: 5px solid #1976d2;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .cita-info {
            flex: 1;
        }

        .cita-info h4 {
            color: #0d47a1;
            margin: 0 0 5px 0;
            font-weight: 700;
        }

        .cita-info p {
            color: #666;
            margin: 3px 0;
            font-size: 14px;
        }

        .cita-acciones {
            display: flex;
            gap: 10px;
        }

        .cita-acciones a {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .cita-acciones .btn-editar {
            background: #e3f2fd;
            color: #1565c0;
            border: 1px solid #1976d2;
        }

        .cita-acciones .btn-editar:hover {
            background: #bbdefb;
        }

        .sin-citas {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .sin-citas p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        @media (max-width: 900px) {
            .seccion-usuario,
            .acciones-rapidas {
                grid-template-columns: 1fr;
            }

            .bienvenida h1 {
                font-size: 26px;
            }

            .dashboard-container {
                margin: 110px auto 60px;
                padding: 0 20px;
            }
        }

        @media (max-width: 600px) {
            .dashboard-container {
                margin: 100px auto 40px;
                padding: 0 15px;
            }

            .bienvenida {
                padding: 25px;
            }

            .bienvenida h1 {
                font-size: 22px;
            }

            .tarjeta, .accion-card, .proximas-citas {
                padding: 20px;
            }

            .accion-card .icono {
                font-size: 48px;
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
                <li><a href="Inicio.php">Inicio</a></li>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="historial-citas.php">Mis Citas</a></li>
                <li><a href="recordatorios.php">Recordatorios</a></li>
                <li><a href="doctores.php">Doctores</a></li>
                <li><a href="contacto.html">Contacto</a></li>
                <li><a href="logout.php" class="logout">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="dashboard-container fade-in">
        <!-- BIENVENIDA -->
        <div class="bienvenida">
            <h1>Bienvenido, <?php echo htmlspecialchars(explode(" ", $_SESSION['usuario_nombre'])[0]); ?>! 👋</h1>
            <p>Gestiona tus citas médicas de forma rápida y segura desde tu dashboard personal.</p>
        </div>

        <!-- INFORMACIÓN DEL USUARIO -->
        <div class="seccion-usuario">
            <div class="tarjeta">
                <h3>👤 Mi Perfil</h3>
                <div class="tarjeta-info">
                    <strong>Nombre:</strong> <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
                    <strong>Correo:</strong> <?php echo htmlspecialchars($_SESSION['usuario_correo']); ?>
                    <strong>Cédula:</strong> <?php echo htmlspecialchars($_SESSION['usuario_cedula']); ?>
                </div>
            </div>

            <div class="tarjeta">
                <h3>📅 Mis Citas</h3>
                <div class="tarjeta-info">
                    <?php
                    $archivo_citas = "citas_agendadas.json";
                    $total_citas = 0;
                    $citas_proximas = 0;
                    
                    if (file_exists($archivo_citas)) {
                        $json_content = file_get_contents($archivo_citas);
                        if (!empty($json_content)) {
                            $citas = json_decode($json_content, true);
                            if (is_array($citas)) {
                                // Filtrar citas por correo del usuario actual
                                $mi_correo = $_SESSION['usuario_correo'];
                                $mis_citas = array_filter($citas, function($cita) use ($mi_correo) {
                                    return $cita['correo'] == $mi_correo;
                                });
                                
                                $total_citas = count($mis_citas);
                                
                                // Contar citas futuras
                                $hoy = date("Y-m-d");
                                foreach ($mis_citas as $cita) {
                                    if ($cita['fecha_cita'] >= $hoy) {
                                        $citas_proximas++;
                                    }
                                }
                            }
                        }
                    }
                    ?>
                    <strong>Total de citas:</strong> <?php echo $total_citas; ?>
                    <strong>Próximas citas:</strong> <?php echo $citas_proximas; ?>
                    <strong>Citas completadas:</strong> <?php echo max(0, $total_citas - $citas_proximas); ?>
                </div>
            </div>

            <div class="tarjeta">
                <h3>⭐ Satisfacción</h3>
                <div class="tarjeta-info">
                    <strong>Nivel de satisfacción:</strong> Excelente
                    <strong>Última visita:</strong> Hace 3 días
                    <strong>Estado:</strong> Activo
                </div>
            </div>
        </div>

        <!-- ACCIONES RÁPIDAS -->
        <div class="acciones-rapidas">
            <div class="accion-card">
                <div class="icono">📋</div>
                <h3>Agendar Cita</h3>
                <p>Reserva una nueva cita médica con nuestros especialistas.</p>
                <a href="index.php">Agendar Ahora</a>
            </div>

            <div class="accion-card">
                <div class="icono">👨‍⚕️</div>
                <h3>Ver Doctores</h3>
                <p>Consulta el directorio de doctores disponibles.</p>
                <a href="doctores.php">Ver Doctores</a>
            </div>

            <div class="accion-card">
                <div class="icono">📞</div>
                <h3>Contactar Clínica</h3>
                <p>Comunícate con nuestro equipo de soporte.</p>
                <a href="contacto.html">Contactar</a>
            </div>

            <div class="accion-card">
                <div class="icono">🔔</div>
                <h3>Recordatorios</h3>
                <p>Mantente al tanto de tus próximas citas médicas.</p>
                <a href="recordatorios.php">Ver Recordatorios</a>
            </div>

            <div class="accion-card">
                <div class="icono"></div>
                <h3>Editar Perfil</h3>
                <p>Actualiza tu información personal.</p>
                <a href="editar-perfil.php">Editar Perfil</a>
            </div>
        </div>

        <!-- PRÓXIMAS CITAS -->
        <div class="proximas-citas">
            <h2>📅 Próximas Citas</h2>
            <?php
            $archivo_citas = "citas_agendadas.json";
            $hay_citas = false;
            
            if (file_exists($archivo_citas)) {
                $json_content = file_get_contents($archivo_citas);
                if (!empty($json_content)) {
                    $todas_citas = json_decode($json_content, true);
                    if (is_array($todas_citas)) {
                        // Filtrar citas por correo del usuario actual
                        $mi_correo = $_SESSION['usuario_correo'];
                        $mis_citas = array_filter($todas_citas, function($cita) use ($mi_correo) {
                            return $cita['correo'] == $mi_correo;
                        });
                        
                        // Ordenar por fecha
                        usort($mis_citas, function($a, $b) {
                            return strtotime($a['fecha_cita']) - strtotime($b['fecha_cita']);
                        });
                        
                        if (count($mis_citas) > 0) {
                            $hay_citas = true;
                            foreach ($mis_citas as $cita) {
                                $fecha_formateada = date("d/m/Y", strtotime($cita['fecha_cita']));
                                $estado_color = $cita['estado'] == 'pendiente' ? '#ff9800' : '#4caf50';
                                echo '<div class="cita-item" style="border-left-color: ' . $estado_color . ';">
                                    <div class="cita-info">
                                        <h4>Dr. ' . htmlspecialchars($cita['nombre']) . '</h4>
                                        <p>📅 Fecha: ' . $fecha_formateada . '</p>
                                        <p>📧 Correo: ' . htmlspecialchars($cita['correo']) . '</p>';
                                if (!empty($cita['observaciones'])) {
                                    echo '<p>📝 Observaciones: ' . htmlspecialchars($cita['observaciones']) . '</p>';
                                }
                                echo '<p style="font-weight: 600; color: ' . $estado_color . ';">Estado: ' . ucfirst($cita['estado']) . '</p>
                                    </div>
                                    <div class="cita-acciones">
                                        <a href="javascript:void(0);" class="btn-editar" onclick="alert(\'Editar cita: ' . htmlspecialchars($cita['id']) . '\')">Editar</a>
                                    </div>
                                </div>';
                            }
                        }
                    }
                }
            }
            
            if (!$hay_citas) {
                echo '<div class="sin-citas">
                    <p>No tienes citas agendadas próximamente.</p>
                    <a href="index.php" style="display: inline-block; background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%); color: white; padding: 12px 28px; border-radius: 10px; text-decoration: none; font-weight: 600;">
                        + Agendar Nueva Cita
                    </a>
                </div>';
            }
            ?>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <p>© 2026 Sistema de Citas Médicas | AppoinMed</p>
    </footer>

</body>
</html>
