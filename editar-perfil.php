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
$mensaje_exito = '';
$mensaje_error = '';

// Leer datos actuales del usuario
$archivo_registros = "registros.json";
if (!file_exists($archivo_registros)) {
    die("Error: No se encontraron registros.");
}

$json_content = file_get_contents($archivo_registros);
$registros = json_decode($json_content, true);

// Buscar el usuario actual
$usuario_actual = null;
$indice_usuario = -1;

foreach ($registros as $idx => $registro) {
    if ($registro['correo'] === $correo_usuario) {
        $usuario_actual = $registro;
        $indice_usuario = $idx;
        break;
    }
}

if ($usuario_actual === null) {
    die("Error: Usuario no encontrado.");
}

// Procesar formulario de actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nuevos_datos = array(
        'nombres' => htmlspecialchars(trim($_POST['nombres'] ?? '')),
        'apellidos' => htmlspecialchars(trim($_POST['apellidos'] ?? '')),
        'cedula' => htmlspecialchars(trim($_POST['cedula'] ?? '')),
        'correo' => htmlspecialchars(trim($_POST['correo'] ?? '')),
        'edad' => htmlspecialchars(trim($_POST['edad'] ?? '')),
        'fecha_nacimiento' => htmlspecialchars(trim($_POST['fecha_nacimiento'] ?? '')),
        'genero' => htmlspecialchars(trim($_POST['genero'] ?? '')),
        'telefono' => htmlspecialchars(trim($_POST['telefono'] ?? '')),
        'direccion' => htmlspecialchars(trim($_POST['direccion'] ?? '')),
        'ciudad' => htmlspecialchars(trim($_POST['ciudad'] ?? '')),
        'motivo_consulta' => htmlspecialchars(trim($_POST['motivo_consulta'] ?? '')),
        'especialidad' => htmlspecialchars(trim($_POST['especialidad'] ?? ''))
    );

    // Validar campos requeridos
    if (empty($nuevos_datos['nombres']) || empty($nuevos_datos['apellidos']) || empty($nuevos_datos['correo'])) {
        $mensaje_error = "Por favor completa los campos requeridos (Nombres, Apellidos, Correo).";
    } else {
        // Verificar si el nuevo correo ya existe (si es diferente al actual)
        $correo_existe = false;
        if ($nuevos_datos['correo'] !== $correo_usuario) {
            foreach ($registros as $registro) {
                if ($registro['correo'] === $nuevos_datos['correo']) {
                    $correo_existe = true;
                    break;
                }
            }
        }

        if ($correo_existe) {
            $mensaje_error = "El correo electrónico ya está registrado.";
        } else {
            // Mantener campos que no se pueden cambiar
            $nuevos_datos['clave_encriptada'] = $usuario_actual['clave_encriptada'];
            $nuevos_datos['foto'] = $usuario_actual['foto'] ?? '';
            $nuevos_datos['satisfaccion'] = $usuario_actual['satisfaccion'] ?? '';
            $nuevos_datos['color_favorito'] = $usuario_actual['color_favorito'] ?? '';
            $nuevos_datos['comentarios'] = $usuario_actual['comentarios'] ?? '';

            // Actualizar registro
            $registros[$indice_usuario] = $nuevos_datos;

            // Guardar cambios
            file_put_contents($archivo_registros, json_encode($registros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Actualizar sesión
            $_SESSION['usuario_nombre'] = $nuevos_datos['nombres'];
            $_SESSION['usuario_correo'] = $nuevos_datos['correo'];
            $correo_usuario = $nuevos_datos['correo'];
            $usuario_actual = $nuevos_datos;

            $mensaje_exito = "✓ Perfil actualizado exitosamente";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 50%, #1976d2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .layout {
            max-width: 900px;
            margin: 30px auto;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 30px;
        }

        .sidebar {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
            height: fit-content;
        }

        .perfil-card {
            text-align: center;
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 50px;
            margin: 0 auto 20px;
            color: white;
            box-shadow: 0 4px 15px rgba(21, 101, 192, 0.3);
        }

        .perfil-card h2 {
            color: #0d47a1;
            margin: 0 0 5px 0;
            font-size: 20px;
        }

        .perfil-card p {
            color: #666;
            margin: 0;
            font-size: 13px;
        }

        .menu-secundario {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e3f2fd;
        }

        .menu-btn {
            display: block;
            width: 100%;
            padding: 12px 16px;
            background: #f5f7fa;
            border: none;
            border-radius: 8px;
            color: #0d47a1;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            margin-bottom: 10px;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: left;
        }

        .menu-btn:hover {
            background: #1565c0;
            color: white;
            transform: translateX(5px);
        }

        .main-content {
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h1 {
            color: #0d47a1;
            margin: 0 0 10px 0;
            font-size: 28px;
        }

        .form-header p {
            color: #666;
            margin: 0;
            font-size: 14px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 14px;
        }

        .alert-success {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            color: #2e7d32;
        }

        .alert-error {
            background: #ffebee;
            border-left: 4px solid #f44336;
            color: #c62828;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-row.full {
            grid-template-columns: 1fr;
        }

        .form-group {
            margin-bottom: 0;
        }

        label {
            display: block;
            color: #0d47a1;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 13px;
            text-transform: uppercase;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"],
        input[type="tel"],
        textarea,
        select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e3f2fd;
            border-radius: 10px;
            background: #f8f9fa;
            color: #1a1a1a;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus,
        input[type="date"]:focus,
        input[type="tel"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #1976d2;
            background: white;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #1976d2;
            padding: 15px;
            border-radius: 8px;
            font-size: 13px;
            color: #0d47a1;
            margin-bottom: 25px;
        }

        .botones {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 14px 20px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
        }

        .btn-primario {
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(21, 101, 192, 0.25);
        }

        .btn-primario:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(21, 101, 192, 0.4);
        }

        .btn-secundario {
            background: #f3f8fc;
            color: #1565c0;
            border: 2px solid #1976d2;
        }

        .btn-secundario:hover {
            background: #e3f2fd;
        }

        @media (max-width: 768px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .main-content {
                padding: 25px;
            }

            .botones {
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

    <div class="layout">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="perfil-card">
                <div class="avatar">👤</div>
                <h2><?php echo htmlspecialchars($usuario_actual['nombres']); ?></h2>
                <p><?php echo htmlspecialchars($usuario_actual['correo']); ?></p>

                <div class="menu-secundario">
                    <a href="cambiar-contrasena-usuario.php" class="menu-btn">🔐 Cambiar Contraseña</a>
                    <a href="dashboard.php" class="menu-btn">📊 Mis Datos</a>
                    <a href="historial-citas.php" class="menu-btn">📋 Mis Citas</a>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <div class="form-header">
                <h1>Editar Perfil</h1>
                <p>Actualiza tu información personal</p>
            </div>

            <?php if (!empty($mensaje_exito)): ?>
                <div class="alert alert-success"><?php echo $mensaje_exito; ?></div>
            <?php endif; ?>

            <?php if (!empty($mensaje_error)): ?>
                <div class="alert alert-error"><?php echo $mensaje_error; ?></div>
            <?php endif; ?>

            <div class="info-box">
                ℹ️ Los campos marcados con * son obligatorios. Tu contraseña no puede ser modificada desde aquí.
            </div>

            <form method="POST" onsubmit="validarFormulario(event)">
                <!-- Nombres y Apellidos -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombres">Nombres *</label>
                        <input type="text" id="nombres" name="nombres" value="<?php echo htmlspecialchars($usuario_actual['nombres']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="apellidos">Apellidos *</label>
                        <input type="text" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($usuario_actual['apellidos']); ?>" required>
                    </div>
                </div>

                <!-- Cédula y Edad -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="cedula">Cédula de Identidad</label>
                        <input type="text" id="cedula" name="cedula" value="<?php echo htmlspecialchars($usuario_actual['cedula'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="edad">Edad</label>
                        <input type="number" id="edad" name="edad" min="1" max="120" value="<?php echo htmlspecialchars($usuario_actual['edad'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Correo y Teléfono -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="correo">Correo Electrónico *</label>
                        <input type="email" id="correo" name="correo" value="<?php echo htmlspecialchars($usuario_actual['correo']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" value="<?php echo htmlspecialchars($usuario_actual['telefono'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Fecha de Nacimiento y Género -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="fecha_nacimiento">Fecha de Nacimiento</label>
                        <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" value="<?php echo htmlspecialchars($usuario_actual['fecha_nacimiento'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="genero">Género</label>
                        <select id="genero" name="genero">
                            <option value="">Selecciona...</option>
                            <option value="Masculino" <?php echo ($usuario_actual['genero'] ?? '') === 'Masculino' ? 'selected' : ''; ?>>Masculino</option>
                            <option value="Femenino" <?php echo ($usuario_actual['genero'] ?? '') === 'Femenino' ? 'selected' : ''; ?>>Femenino</option>
                            <option value="Otro" <?php echo ($usuario_actual['genero'] ?? '') === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                        </select>
                    </div>
                </div>

                <!-- Dirección y Ciudad -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="direccion">Dirección</label>
                        <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario_actual['direccion'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="ciudad">Ciudad</label>
                        <input type="text" id="ciudad" name="ciudad" value="<?php echo htmlspecialchars($usuario_actual['ciudad'] ?? ''); ?>">
                    </div>
                </div>

                <!-- Motivo de Consulta y Especialidad -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="motivo_consulta">Motivo de Consulta</label>
                        <input type="text" id="motivo_consulta" name="motivo_consulta" value="<?php echo htmlspecialchars($usuario_actual['motivo_consulta'] ?? ''); ?>">
                    </div>
                    <div class="form-group">
                        <label for="especialidad">Especialidad Preferida</label>
                        <select id="especialidad" name="especialidad">
                            <option value="">Selecciona...</option>
                            <option value="Medicina General" <?php echo ($usuario_actual['especialidad'] ?? '') === 'Medicina General' ? 'selected' : ''; ?>>Medicina General</option>
                            <option value="Pediatría" <?php echo ($usuario_actual['especialidad'] ?? '') === 'Pediatría' ? 'selected' : ''; ?>>Pediatría</option>
                            <option value="Cardiología" <?php echo ($usuario_actual['especialidad'] ?? '') === 'Cardiología' ? 'selected' : ''; ?>>Cardiología</option>
                            <option value="Dermatología" <?php echo ($usuario_actual['especialidad'] ?? '') === 'Dermatología' ? 'selected' : ''; ?>>Dermatología</option>
                            <option value="Odontología" <?php echo ($usuario_actual['especialidad'] ?? '') === 'Odontología' ? 'selected' : ''; ?>>Odontología</option>
                        </select>
                    </div>
                </div>

                <!-- Botones -->
                <div class="botones">
                    <a href="dashboard.php" class="btn btn-secundario">← Cancelar</a>
                    <button type="submit" class="btn btn-primario">✓ Guardar Cambios</button>
                </div>
            </form>
        </main>
    </div>

    <script>
        function validarFormulario(event) {
            const nombres = document.getElementById('nombres').value.trim();
            const apellidos = document.getElementById('apellidos').value.trim();
            const correo = document.getElementById('correo').value.trim();

            if (!nombres || !apellidos || !correo) {
                alert('Por favor completa los campos requeridos');
                event.preventDefault();
                return false;
            }

            if (!correo.includes('@')) {
                alert('Por favor ingresa un correo válido');
                event.preventDefault();
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
