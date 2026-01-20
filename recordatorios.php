<?php
// Suprimir notices de PHP
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario esté registrado
if (!isset($_SESSION['usuario_registrado']) || $_SESSION['usuario_registrado'] !== true) {
    header("Location: Formulario.html");
    exit;
}

$correo_usuario = $_SESSION['usuario_correo'] ?? '';

// Crear archivos si no existen
$archivo_citas = "recordatorios.json";
if (!file_exists($archivo_citas)) {
    file_put_contents($archivo_citas, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

// Leer citas de recordatorios.json
$json_content = file_get_contents($archivo_citas);
$todas_las_citas = json_decode($json_content, true);
if (!is_array($todas_las_citas)) {
    $todas_las_citas = [];
}

// También leer de citas_agendadas.json para mayor compatibilidad
$archivo_citas_agendadas = "citas_agendadas.json";
if (file_exists($archivo_citas_agendadas)) {
    $json_citas_agendadas = file_get_contents($archivo_citas_agendadas);
    $citas_agendadas = json_decode($json_citas_agendadas, true);
    
    if (is_array($citas_agendadas)) {
        // Convertir citas_agendadas al formato de recordatorios
        foreach ($citas_agendadas as $cita) {
            if ($cita['correo'] === $correo_usuario) {
                // Verificar si ya existe este recordatorio
                $existe = false;
                foreach ($todas_las_citas as $existente) {
                    if ($existente['correo_paciente'] === $cita['correo'] && 
                        $existente['fecha'] === $cita['fecha_cita']) {
                        $existe = true;
                        break;
                    }
                }
                
                // Si no existe, agregarlo
                if (!$existe) {
                    $recordatorio = [
                        "id" => uniqid(),
                        "correo_paciente" => $cita['correo'],
                        "nombre_paciente" => $cita['nombre'],
                        "fecha" => $cita['fecha_cita'],
                        "hora" => "09:00",
                        "especialidad" => "Consulta General",
                        "doctor" => "Por Asignar",
                        "observaciones" => $cita['observaciones'] ?? '',
                        "estado" => $cita['estado'] ?? 'pendiente',
                        "fecha_registro" => $cita['fecha_registro'] ?? date("Y-m-d H:i:s")
                    ];
                    $todas_las_citas[] = $recordatorio;
                }
            }
        }
    }
}

// Filtrar citas del usuario actual
$citas_usuario = [];
$hoy = new DateTime();

if (is_array($todas_las_citas)) {
    foreach ($todas_las_citas as $cita) {
        if ($cita['correo_paciente'] === $correo_usuario) {
            $fecha_cita = new DateTime($cita['fecha'] . ' ' . $cita['hora']);
            $cita['fecha_obj'] = $fecha_cita;
            $cita['proxima'] = $fecha_cita >= $hoy;
            $citas_usuario[] = $cita;
        }
    }
}

// Separar citas próximas y pasadas
$citas_proximas = array_filter($citas_usuario, function($c) { return $c['proxima']; });
$citas_pasadas = array_filter($citas_usuario, function($c) { return !$c['proxima']; });

// Ordenar por fecha
usort($citas_proximas, function($a, $b) {
    return $a['fecha_obj']->getTimestamp() - $b['fecha_obj']->getTimestamp();
});

usort($citas_pasadas, function($a, $b) {
    return $b['fecha_obj']->getTimestamp() - $a['fecha_obj']->getTimestamp();
});

// Función para calcular días hasta la cita
function diasHastaLaCita($fecha_cita) {
    $hoy = new DateTime();
    $fecha = new DateTime($fecha_cita);
    $intervalo = $hoy->diff($fecha);
    return $intervalo->days;
}

// Función para obtener estado de la cita
function obtenerEstadoCita($dias) {
    if ($dias < 0) return 'Completada';
    if ($dias === 0) return '¡Hoy!';
    if ($dias === 1) return 'Mañana';
    if ($dias <= 7) return 'Esta Semana';
    return 'Próximas';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recordatorios de Citas - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 50%, #1976d2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .recordatorios-container {
            max-width: 900px;
            margin: 30px auto;
        }

        .header {
            background: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #0d47a1;
            margin: 0 0 10px 0;
            font-size: 32px;
            font-weight: 700;
        }

        .header p {
            color: #666;
            margin: 0;
            font-size: 16px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
        }

        .stat-numero {
            font-size: 32px;
            font-weight: 700;
        }

        .stat-label {
            font-size: 13px;
            opacity: 0.9;
        }

        .seccion {
            background: white;
            padding: 30px;
            border-radius: 16px;
            margin-bottom: 30px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
        }

        .seccion h2 {
            color: #0d47a1;
            margin: 0 0 25px 0;
            font-size: 24px;
            font-weight: 700;
            padding-bottom: 15px;
            border-bottom: 2px solid #e3f2fd;
        }

        .cita-item {
            background: #f8f9fa;
            border-left: 4px solid #1976d2;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .cita-item:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateX(5px);
        }

        .cita-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .cita-titulo {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .cita-titulo h3 {
            color: #0d47a1;
            margin: 0;
            font-size: 18px;
            font-weight: 700;
        }

        .badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .badge-hoy {
            background: #fff3cd;
            color: #856404;
        }

        .badge-manana {
            background: #d1ecf1;
            color: #0c5460;
        }

        .badge-semana {
            background: #d4edda;
            color: #155724;
        }

        .badge-proxima {
            background: #e7e8ea;
            color: #383d41;
        }

        .badge-completada {
            background: #f8d7da;
            color: #721c24;
        }

        .cita-detalles {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .detalle {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .detalle-icono {
            font-size: 20px;
            width: 30px;
            text-align: center;
        }

        .detalle-info {
            flex: 1;
        }

        .detalle-label {
            color: #999;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
        }

        .detalle-valor {
            color: #333;
            font-size: 15px;
            font-weight: 600;
            margin-top: 3px;
        }

        .cita-acciones {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e3f2fd;
        }

        .btn {
            flex: 1;
            padding: 10px 16px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn-recordatorio {
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(21, 101, 192, 0.25);
        }

        .btn-recordatorio:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(21, 101, 192, 0.4);
        }

        .btn-secundario {
            background: #f3f8fc;
            color: #1565c0;
            border: 2px solid #1976d2;
        }

        .btn-secundario:hover {
            background: #e3f2fd;
        }

        .sin-citas {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .sin-citas p {
            font-size: 16px;
            margin-bottom: 20px;
        }

        .sin-citas a {
            display: inline-block;
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
        }

        .countdown {
            font-size: 24px;
            font-weight: 700;
            color: #1565c0;
        }

        .volver-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 12px 24px;
            background: white;
            color: #1565c0;
            border: 2px solid #1976d2;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .volver-btn:hover {
            background: #e3f2fd;
        }

        @media (max-width: 768px) {
            .recordatorios-container {
                margin: 20px auto;
            }

            .header h1 {
                font-size: 24px;
            }

            .cita-detalles {
                grid-template-columns: 1fr;
            }

            .cita-acciones {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header class="top-bar">
        <nav class="nav">
            <div class="logo">
                <img src="Img/imagen1.png" alt="Logo">
                <span>Sistema de Citas</span>
            </div>

            <ul class="menu">
                <li><a href="dashboard.php">← Dashboard</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>

    <div class="recordatorios-container">
        <a href="dashboard.php" class="volver-btn">← Volver al Dashboard</a>

        <!-- HEADER CON ESTADÍSTICAS -->
        <div class="header">
            <h1>🔔 Recordatorios de Citas</h1>
            <p>Mantente al tanto de tus próximas consultas médicas</p>

            <div class="stats">
                <div class="stat-card">
                    <div class="stat-numero"><?php echo count($citas_proximas); ?></div>
                    <div class="stat-label">Citas Próximas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-numero"><?php echo count($citas_pasadas); ?></div>
                    <div class="stat-label">Citas Completadas</div>
                </div>
                <div class="stat-card">
                    <div class="stat-numero"><?php echo count($citas_usuario); ?></div>
                    <div class="stat-label">Citas Totales</div>
                </div>
            </div>
        </div>

        <!-- CITAS PRÓXIMAS -->
        <div class="seccion">
            <h2>📅 Próximas Citas</h2>

            <?php if (count($citas_proximas) > 0): ?>
                <?php foreach ($citas_proximas as $cita): ?>
                    <?php 
                        $dias = diasHastaLaCita($cita['fecha']);
                        $estado = obtenerEstadoCita($dias);
                        $fecha_formateada = date('d \d\e M \d\e Y', strtotime($cita['fecha']));
                    ?>
                    <div class="cita-item">
                        <div class="cita-header">
                            <div class="cita-titulo">
                                <h3>👨‍⚕️ Dr(a). <?php echo htmlspecialchars($cita['doctor']); ?></h3>
                                <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $estado)); ?>">
                                    <?php echo $estado; ?>
                                </span>
                            </div>
                            <div class="countdown"><?php echo $dias; ?> días</div>
                        </div>

                        <div class="cita-detalles">
                            <div class="detalle">
                                <div class="detalle-icono">📍</div>
                                <div class="detalle-info">
                                    <div class="detalle-label">Especialidad</div>
                                    <div class="detalle-valor"><?php echo htmlspecialchars($cita['especialidad']); ?></div>
                                </div>
                            </div>

                            <div class="detalle">
                                <div class="detalle-icono">📅</div>
                                <div class="detalle-info">
                                    <div class="detalle-label">Fecha</div>
                                    <div class="detalle-valor"><?php echo $fecha_formateada; ?></div>
                                </div>
                            </div>

                            <div class="detalle">
                                <div class="detalle-icono">🕐</div>
                                <div class="detalle-info">
                                    <div class="detalle-label">Hora</div>
                                    <div class="detalle-valor"><?php echo htmlspecialchars($cita['hora']); ?></div>
                                </div>
                            </div>

                            <div class="detalle">
                                <div class="detalle-icono">📍</div>
                                <div class="detalle-info">
                                    <div class="detalle-label">Consultorio</div>
                                    <div class="detalle-valor">Consultorio <?php echo htmlspecialchars($cita['consultorio'] ?? '101'); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="cita-acciones">
                            <button class="btn btn-recordatorio" onclick="setRecordatorio('<?php echo htmlspecialchars($cita['fecha']); ?>', '<?php echo htmlspecialchars($cita['hora']); ?>')">
                                🔔 Activar Recordatorio
                            </button>
                            <a href="historial-citas.php" class="btn btn-secundario">Ver Detalles</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="sin-citas">
                    <p>📭 No tienes citas próximas agendadas</p>
                    <a href="index.php">Agendar una Cita</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- CITAS PASADAS -->
        <?php if (count($citas_pasadas) > 0): ?>
            <div class="seccion">
                <h2>✓ Citas Completadas</h2>

                <?php foreach ($citas_pasadas as $cita): ?>
                    <?php $fecha_formateada = date('d \d\e M \d\e Y', strtotime($cita['fecha'])); ?>
                    <div class="cita-item">
                        <div class="cita-header">
                            <div class="cita-titulo">
                                <h3>👨‍⚕️ Dr(a). <?php echo htmlspecialchars($cita['doctor']); ?></h3>
                                <span class="badge badge-completada">COMPLETADA</span>
                            </div>
                        </div>

                        <div class="cita-detalles">
                            <div class="detalle">
                                <div class="detalle-icono">📍</div>
                                <div class="detalle-info">
                                    <div class="detalle-label">Especialidad</div>
                                    <div class="detalle-valor"><?php echo htmlspecialchars($cita['especialidad']); ?></div>
                                </div>
                            </div>

                            <div class="detalle">
                                <div class="detalle-icono">📅</div>
                                <div class="detalle-info">
                                    <div class="detalle-label">Fecha</div>
                                    <div class="detalle-valor"><?php echo $fecha_formateada; ?></div>
                                </div>
                            </div>

                            <div class="detalle">
                                <div class="detalle-icono">🕐</div>
                                <div class="detalle-info">
                                    <div class="detalle-label">Hora</div>
                                    <div class="detalle-valor"><?php echo htmlspecialchars($cita['hora']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function setRecordatorio(fecha, hora) {
            const fechaCompleta = new Date(fecha + ' ' + hora);
            const ahora = new Date();
            const diferencia = fechaCompleta - ahora;

            if (diferencia > 0) {
                // Programar recordatorio para 24 horas antes
                const tiempoRecordatorio = diferencia - (24 * 60 * 60 * 1000);
                
                if (tiempoRecordatorio > 0) {
                    setTimeout(() => {
                        alert(`🔔 Recordatorio: Tu cita con el doctor es mañana a las ${hora}`);
                    }, tiempoRecordatorio);
                    
                    alert('✓ Recordatorio activado para 24 horas antes de tu cita');
                } else {
                    alert('⏰ Tu cita es en menos de 24 horas');
                }
            } else {
                alert('La cita ya pasó');
            }
        }
    </script>
</body>
</html>
