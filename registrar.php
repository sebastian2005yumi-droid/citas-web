<?php
// Suprimir notices de PHP
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que la solicitud sea POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Determinar el tipo de formulario enviado
    $tipo_formulario = isset($_POST['fuente']) ? $_POST['fuente'] : 'agendamiento';
    
    // AGENDAMIENTO RÁPIDO (index.html) - REQUIERE USUARIO REGISTRADO
    if ($tipo_formulario === 'agendamiento_rapido') {
        // Verificar si el usuario está registrado (tiene sesión)
        if (!isset($_SESSION['usuario_registrado']) || $_SESSION['usuario_registrado'] !== true) {
            die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#fff3cd; border:2px solid #ff9800; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#e65100;'>⚠️ Debes registrarte primero</h2><p style='font-size:16px; color:#333;'>Para agendar una cita, es necesario completar el formulario de registro.</p><a href='Formulario.html' style='display:inline-block; background:#ff9800; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Ir a Registro</a></div>");
        }
        $nombre = htmlspecialchars(trim($_POST['nombre']));
        $correo = htmlspecialchars(trim($_POST['correo']));
        $fecha = htmlspecialchars(trim($_POST['fecha']));
        $mensaje = htmlspecialchars(trim($_POST['mensaje']));
        
        if (empty($nombre) || empty($correo) || empty($fecha)) {
            die("Error: Por favor completa todos los campos requeridos.");
        }
        
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            die("Error: El correo electrónico no es válido.");
        }
        
        $fecha_registro = date("Y-m-d H:i:s");
        $linea_datos = "TIPO: AGENDAMIENTO RÁPIDO | FECHA REGISTRO: $fecha_registro | NOMBRE: $nombre | CORREO: $correo | FECHA CITA: $fecha | OBSERVACIONES: $mensaje\n";
        $linea_datos .= str_repeat("-", 120) . "\n";
        $archivo = "citas.txt";
        
        // Guardar también en JSON para el dashboard
        $cita_json = [
            "id" => uniqid(),
            "tipo" => "agendamiento_rapido",
            "fecha_registro" => $fecha_registro,
            "nombre" => $nombre,
            "correo" => $correo,
            "fecha_cita" => date("Y-m-d", strtotime($fecha)),
            "observaciones" => $mensaje,
            "estado" => "pendiente"
        ];
        
        $archivo_json = "citas_agendadas.json";
        $citas = [];
        
        // Leer citas existentes si el archivo existe
        if (file_exists($archivo_json)) {
            $json_content = file_get_contents($archivo_json);
            if (!empty($json_content)) {
                $citas = json_decode($json_content, true);
                if (!is_array($citas)) {
                    $citas = [];
                }
            }
        }
        
        // Agregar nueva cita
        $citas[] = $cita_json;
        
        // Guardar en JSON
        $json_resultado = json_encode($citas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        
        // También guardar como recordatorio
        $recordatorio = [
            "id" => uniqid(),
            "correo_paciente" => $correo,
            "nombre_paciente" => $nombre,
            "fecha" => date("Y-m-d", strtotime($fecha)),
            "hora" => "09:00",
            "especialidad" => "Consulta General",
            "doctor" => "Por Asignar",
            "observaciones" => $mensaje,
            "estado" => "pendiente",
            "fecha_registro" => $fecha_registro
        ];
        
        $archivo_recordatorios = "recordatorios.json";
        $recordatorios = [];
        
        if (file_exists($archivo_recordatorios)) {
            $json_recordatorios = file_get_contents($archivo_recordatorios);
            if (!empty($json_recordatorios)) {
                $recordatorios = json_decode($json_recordatorios, true);
                if (!is_array($recordatorios)) {
                    $recordatorios = [];
                }
            }
        }
        
        $recordatorios[] = $recordatorio;
        
        if (file_put_contents($archivo, $linea_datos, FILE_APPEND | LOCK_EX) && 
            file_put_contents($archivo_json, $json_resultado, LOCK_EX) &&
            file_put_contents($archivo_recordatorios, json_encode($recordatorios, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX)) {
            ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cita Registrada - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
        }

        .top-bar {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);
            box-shadow: 0 4px 15px rgba(13, 71, 161, 0.2);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            font-size: 22px;
            font-weight: bold;
        }

        .logo img {
            width: 50px;
            height: 50px;
        }

        .menu {
            display: flex;
            list-style: none;
            gap: 30px;
            margin: 0;
            padding: 0;
        }

        .menu a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 8px 12px;
            border-radius: 6px;
        }

        .menu a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .mensaje-exito {
            max-width: 700px;
            margin: 60px auto;
            padding: 60px 40px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 70px rgba(13, 71, 161, 0.15);
            animation: slideIn 0.6s ease;
        }

        .mensaje-exito h2 {
            font-size: 36px;
            color: #0d47a1;
            margin: 0 0 20px 0;
            font-weight: 700;
        }

        .mensaje-exito > p:first-of-type {
            font-size: 18px;
            color: #555;
            margin: 0 0 30px 0;
            line-height: 1.6;
        }

        .datos-confirmacion {
            background: linear-gradient(135deg, #f0f4ff 0%, #e8eef7 100%);
            padding: 30px;
            border-radius: 15px;
            margin: 30px 0;
            text-align: left;
            border-left: 5px solid #0d47a1;
            box-shadow: 0 5px 20px rgba(13, 71, 161, 0.08);
        }

        .datos-confirmacion strong {
            display: block;
            color: #0d47a1;
            font-weight: 600;
            margin-top: 12px;
            font-size: 16px;
        }

        .datos-confirmacion strong:first-child {
            margin-top: 0;
        }

        .mensaje-exito > p:last-of-type {
            font-size: 16px;
            color: #666;
            margin: 20px 0 30px 0;
            font-weight: 500;
        }

        .btn-volver {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%);
            color: white;
            padding: 14px 40px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(13, 71, 161, 0.2);
        }

        .btn-volver:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 40px rgba(13, 71, 161, 0.3);
        }

        .btn-volver:active {
            transform: translateY(-2px);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .footer {
            background: #0d47a1;
            color: white;
            text-align: center;
            padding: 25px;
            margin-top: 60px;
            box-shadow: 0 -4px 15px rgba(13, 71, 161, 0.1);
        }

        .footer p {
            margin: 0;
            font-size: 14px;
        }

        @media (max-width: 768px) {
            .menu {
                flex-direction: column;
                gap: 10px;
            }

            .nav {
                flex-direction: column;
                gap: 15px;
            }

            .mensaje-exito {
                margin: 30px 15px;
                padding: 40px 20px;
            }

            .mensaje-exito h2 {
                font-size: 28px;
            }

            .datos-confirmacion {
                padding: 20px;
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
                <li><a href="Inicio.php">Inicio</a></li>
                <li><a href="Formulario.html" class="active">Registro</a></li>
                <li><a href="index.php">Agendar</a></li>
                <li><a href="doctores.php">Doctores</a></li>
                <li><a href="contacto.html">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <main class="mensaje-exito">
        <div style="text-align: center;">
            <h2>✓ ¡Cita Registrada Exitosamente!</h2>
            <p>Tu cita ha sido registrada en nuestro sistema. Te contactaremos próximamente.</p>
            
            <div class="datos-confirmacion">
                <strong>👤 Nombre:</strong> <?php echo $nombre; ?><br><br>
                <strong>📧 Correo:</strong> <?php echo $correo; ?><br><br>
                <strong>📅 Fecha de cita:</strong> <?php echo date("d/m/Y", strtotime($fecha)); ?><br><br>
                <strong>⏱️ Fecha de registro:</strong> <?php echo date("d/m/Y H:i:s"); ?>
            </div>
            
            <p>Los datos han sido guardados correctamente.</p>
            <a href="dashboard.php" class="btn-volver">→ Ir a Mi Dashboard</a>
        </div>
    </main>

    <footer class="footer">
        <p>© 2026 Sistema de Citas Médicas | AppoinMed</p>
    </footer>
</body>
</html>
            <?php
        } else {
            die("Error: No se pudo registrar la cita. Intenta más tarde.");
        }
    }
    
    // REGISTRO COMPLETO DE PACIENTE (Formulario.html)
    else if ($tipo_formulario === 'formulario_registro') {
        // Obtener y limpiar datos
        $nom = htmlspecialchars(trim($_POST['nom'] ?? ''));
        $ape = htmlspecialchars(trim($_POST['ape'] ?? ''));
        $ced = htmlspecialchars(trim($_POST['ced'] ?? ''));
        $correo = strtolower(htmlspecialchars(trim($_POST['correo'] ?? '')));
        $clave = $_POST['clave'] ?? '';
        $edad = htmlspecialchars(trim($_POST['edad'] ?? ''));
        $nac = htmlspecialchars(trim($_POST['nac'] ?? ''));
        $gen = htmlspecialchars($_POST['gen'] ?? '');
        $motivo = isset($_POST['motivo']) ? implode(", ", array_map('htmlspecialchars', $_POST['motivo'])) : '';
        $esp = htmlspecialchars($_POST['esp'] ?? '');
        $sat = htmlspecialchars($_POST['sat'] ?? '');
        $col = htmlspecialchars($_POST['col'] ?? '');
        $com = htmlspecialchars(trim($_POST['com'] ?? ''));
        
        // Validar campos requeridos
        if (empty($nom) || empty($ape) || empty($ced) || empty($correo) || empty($clave)) {
            die("Error: Por favor completa todos los campos requeridos (*, Nombres, Apellidos, Cédula, Correo, Contraseña).");
        }
        
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            die("Error: El correo electrónico no es válido.");
        }
        
        // Verificar si el usuario ya existe
        $archivo_json = "registros.json";
        $registros_existentes = [];
        
        if (file_exists($archivo_json)) {
            $json_content = file_get_contents($archivo_json);
            if (!empty($json_content)) {
                $registros_existentes = json_decode($json_content, true);
                if (!is_array($registros_existentes)) {
                    $registros_existentes = [];
                }
            }
        }
        
        // Buscar si el correo ya está registrado
        foreach ($registros_existentes as $reg) {
            if (strtolower($reg['correo'] ?? '') === $correo) {
                die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#fff3cd; border:2px solid #ff9800; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#e65100;'>⚠️ Correo ya registrado</h2><p style='font-size:16px; color:#333;'>El correo <strong>" . htmlspecialchars($correo) . "</strong> ya tiene una cuenta registrada. Si olvidaste tu contraseña, intenta recuperarla.</p><a href='Formulario.html' style='display:inline-block; background:#ff9800; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
            }
        }
        
        // Procesar foto si se subió
        $foto_nombre = "";
        if (!empty($_FILES['foto']['name'])) {
            $foto_tmp = $_FILES['foto']['tmp_name'];
            $foto_error = $_FILES['foto']['error'];
            
            if ($foto_error === 0) {
                $foto_nombre = "usuario_" . time() . "_" . basename($_FILES['foto']['name']);
                $carpeta_fotos = "fotos/";
                
                if (!is_dir($carpeta_fotos)) {
                    mkdir($carpeta_fotos, 0755, true);
                }
                
                move_uploaded_file($foto_tmp, $carpeta_fotos . $foto_nombre);
            }
        }
        
        // Encriptar contraseña
        $clave_encriptada = password_hash($clave, PASSWORD_BCRYPT);
        
        // Crear línea de datos
        $fecha_registro = date("Y-m-d H:i:s");
        $linea_datos = "\n" . str_repeat("=", 120) . "\n";
        $linea_datos .= "TIPO: REGISTRO COMPLETO DE PACIENTE | FECHA: $fecha_registro\n";
        $linea_datos .= str_repeat("-", 120) . "\n";
        $linea_datos .= "DATOS PERSONALES:\n";
        $linea_datos .= "  Nombres: $nom | Apellidos: $ape\n";
        $linea_datos .= "  Cédula: $ced | Edad: $edad\n";
        $linea_datos .= "  Correo: $correo\n";
        $linea_datos .= "  Fecha de Nacimiento: $nac | Género: $gen\n";
        $linea_datos .= "\nDATOS MÉDICOS:\n";
        $linea_datos .= "  Motivo Consulta: $motivo\n";
        $linea_datos .= "  Especialidad Solicitada: $esp\n";
        $linea_datos .= "\nDATA ADICIONAL:\n";
        $linea_datos .= "  Satisfacción (1-10): $sat\n";
        $linea_datos .= "  Color Favorito: $col\n";
        $linea_datos .= "  Comentarios: $com\n";
        $linea_datos .= "  Foto: " . ($foto_nombre ? $foto_nombre : "No subida") . "\n";
        $linea_datos .= str_repeat("=", 120) . "\n";
        
        $archivo = "registros.txt";
        
        if (file_put_contents($archivo, $linea_datos, FILE_APPEND | LOCK_EX)) {
            // CREAR SESIÓN PARA EL USUARIO REGISTRADO
            $_SESSION['usuario_registrado'] = true;
            $_SESSION['usuario_nombre'] = $nom . " " . $ape;
            $_SESSION['usuario_correo'] = $correo;
            $_SESSION['usuario_cedula'] = $ced;
            
            // También guardar en un archivo JSON para mejor consulta
            $datos_json = [
                "id" => time(),
                "fecha_registro" => $fecha_registro,
                "nombres" => $nom,
                "apellidos" => $ape,
                "cedula" => $ced,
                "correo" => $correo,
                "clave_encriptada" => $clave_encriptada,
                "edad" => $edad,
                "fecha_nacimiento" => $nac,
                "genero" => $gen,
                "motivo_consulta" => $motivo,
                "especialidad" => $esp,
                "satisfaccion" => $sat,
                "color_favorito" => $col,
                "comentarios" => $com,
                "foto" => $foto_nombre
            ];
            
            $archivo_json = "registros.json";
            $registros = [];
            
            // Leer registros existentes si el archivo existe
            if (file_exists($archivo_json)) {
                $json_content = file_get_contents($archivo_json);
                if (!empty($json_content)) {
                    $registros = json_decode($json_content, true);
                    if (!is_array($registros)) {
                        $registros = [];
                    }
                }
            }
            
            // Agregar nuevo registro
            $registros[] = $datos_json;
            
            // Guardar en JSON con validación
            $json_resultado = json_encode($registros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if ($json_resultado !== false) {
                $guardado = file_put_contents($archivo_json, $json_resultado, LOCK_EX);
                if ($guardado === false) {
                    error_log("Error al guardar registros.json: Problema de permisos o disco lleno");
                }
            } else {
                error_log("Error al codificar JSON: " . json_last_error_msg());
            }
            
            // Mostrar página de confirmación
            ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Exitoso - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
    <style>
        .mensaje-exito {
            max-width: 700px;
            margin: 80px auto;
            padding: 50px;
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 50%, #1976d2 100%);
            border-radius: 16px;
            color: white;
            text-align: center;
            box-shadow: 0 20px 60px rgba(13, 71, 161, 0.2);
            animation: fadeIn 0.7s ease;
        }
        .mensaje-exito h2 {
            font-size: 32px;
            margin-bottom: 15px;
            color: #ffffff;
        }
        .mensaje-exito p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 10px;
        }
        .datos-confirmacion {
            background: rgba(255, 255, 255, 0.15);
            padding: 25px;
            border-radius: 10px;
            margin: 30px 0;
            text-align: left;
            max-height: 400px;
            overflow-y: auto;
        }
        .datos-confirmacion strong {
            display: block;
            margin-top: 8px;
            font-size: 14px;
        }
        .btn-volver {
            background: white;
            color: #0d47a1;
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        .btn-volver:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
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
                <li><a href="Inicio.php">Inicio</a></li>
                <li><a href="Formulario.html">Registro</a></li>
                <li><a href="index.php">Agendar</a></li>
                <li><a href="doctores.php">Doctores</a></li>
                <li><a href="contacto.html">Contacto</a></li>
            </ul>
        </nav>
    </header>

    <main class="contenedor-principal fade-in">
        <div class="mensaje-exito">
            <h2>✓ ¡Registro Exitoso!</h2>
            <p>Tu perfil de paciente ha sido creado correctamente. Ahora puedes agendar citas médicas.</p>
            
            <div class="datos-confirmacion">
                <strong>👤 Nombre: <?php echo $nom . " " . $ape; ?></strong>
                <strong>📧 Correo: <?php echo $correo; ?></strong>
                <strong>🆔 Cédula: <?php echo $ced; ?></strong>
                <strong>📅 Edad: <?php echo $edad; ?> años</strong>
                <strong>👨‍⚕️ Especialidad Solicitada: <?php echo $esp; ?></strong>
                <strong>💬 Motivos: <?php echo $motivo; ?></strong>
                <strong>⏰ Registrado: <?php echo date("d/m/Y H:i:s"); ?></strong>
            </div>
            
            <p>Un correo de confirmación ha sido enviado a tu dirección.</p>
            <p><strong>Tu contraseña está protegida y encriptada de forma segura.</strong></p>
            <a href="dashboard.php" class="btn-volver">← Ir a Mi Dashboard</a>
        </div>
    </main>

    <footer class="footer">
        <p>© 2026 Sistema de Citas Médicas | AppoinMed</p>
    </footer>
</body>
</html>
            <?php
        } else {
            die("Error: No se pudo guardar el registro. Intenta más tarde.");
        }
    }
    
} else {
    die("Error: Tipo de formulario no reconocido. Por favor, completa el formulario correctamente.");
}
?>
