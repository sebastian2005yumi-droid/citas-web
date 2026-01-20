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

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $contrasena_actual = $_POST['contrasena_actual'] ?? '';
    $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
    $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

    // Validar que los campos no estén vacíos
    if (empty($contrasena_actual) || empty($nueva_contrasena) || empty($confirmar_contrasena)) {
        $mensaje_error = "Por favor completa todos los campos.";
    } else if (strlen($nueva_contrasena) < 6) {
        $mensaje_error = "La nueva contraseña debe tener al menos 6 caracteres.";
    } else if ($nueva_contrasena !== $confirmar_contrasena) {
        $mensaje_error = "Las nuevas contraseñas no coinciden.";
    } else {
        // Leer registros
        $archivo_registros = "registros.json";
        if (!file_exists($archivo_registros)) {
            $mensaje_error = "Error: No se encontraron registros.";
        } else {
            $json_content = file_get_contents($archivo_registros);
            $registros = json_decode($json_content, true);

            // Buscar el usuario
            $usuario_encontrado = false;
            for ($i = 0; $i < count($registros); $i++) {
                if ($registros[$i]['correo'] === $correo_usuario) {
                    // Verificar contraseña actual
                    if (password_verify($contrasena_actual, $registros[$i]['clave_encriptada'])) {
                        // Actualizar contraseña
                        $registros[$i]['clave_encriptada'] = password_hash($nueva_contrasena, PASSWORD_BCRYPT);
                        $usuario_encontrado = true;

                        // Guardar cambios
                        file_put_contents($archivo_registros, json_encode($registros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                        $mensaje_exito = "✓ Contraseña actualizada exitosamente";
                    } else {
                        $mensaje_error = "La contraseña actual es incorrecta.";
                    }
                    break;
                }
            }

            if (!$usuario_encontrado && empty($mensaje_error)) {
                $mensaje_error = "Error: Usuario no encontrado.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar Contraseña - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 50%, #1976d2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 80px auto;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.6s ease;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .icono {
            font-size: 60px;
            display: block;
            margin-bottom: 15px;
        }

        h1 {
            color: #0d47a1;
            margin: 0 0 10px 0;
            font-size: 28px;
            font-weight: 700;
        }

        .descripcion {
            color: #666;
            font-size: 14px;
            margin: 0;
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

        .info-box {
            background: #e3f2fd;
            border-left: 4px solid #1976d2;
            padding: 15px;
            border-radius: 8px;
            font-size: 13px;
            color: #0d47a1;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            color: #0d47a1;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 13px;
            text-transform: uppercase;
        }

        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e3f2fd;
            border-radius: 10px;
            background: #f8f9fa;
            color: #1a1a1a;
            font-size: 16px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        input[type="password"]:focus {
            outline: none;
            border-color: #1976d2;
            background: white;
        }

        .separador {
            height: 2px;
            background: #e3f2fd;
            margin: 30px 0;
            border: none;
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

        @media (max-width: 600px) {
            .container {
                margin: 60px 20px;
                padding: 25px;
            }

            .botones {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <span class="icono">🔐</span>
            <h1>Cambiar Contraseña</h1>
            <p class="descripcion">Actualiza tu contraseña de forma segura</p>
        </div>

        <?php if (!empty($mensaje_exito)): ?>
            <div class="alert alert-success"><?php echo $mensaje_exito; ?></div>
        <?php endif; ?>

        <?php if (!empty($mensaje_error)): ?>
            <div class="alert alert-error"><?php echo $mensaje_error; ?></div>
        <?php endif; ?>

        <div class="info-box">
            ℹ️ Por seguridad, debes ingresar tu contraseña actual para establecer una nueva.
        </div>

        <form method="POST" onsubmit="validarFormulario(event)">
            <div class="form-group">
                <label for="contrasena_actual">Contraseña Actual *</label>
                <input type="password" id="contrasena_actual" name="contrasena_actual" placeholder="••••••••" required autofocus>
            </div>

            <hr class="separador">

            <div class="form-group">
                <label for="nueva_contrasena">Nueva Contraseña *</label>
                <input type="password" id="nueva_contrasena" name="nueva_contrasena" placeholder="••••••••" required>
                <small style="color: #666; font-size: 12px; display: block; margin-top: 5px;">Mínimo 6 caracteres</small>
            </div>

            <div class="form-group">
                <label for="confirmar_contrasena">Confirmar Nueva Contraseña *</label>
                <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="••••••••" required>
            </div>

            <div class="botones">
                <a href="editar-perfil.php" class="btn btn-secundario">← Atrás</a>
                <button type="submit" class="btn btn-primario">✓ Actualizar</button>
            </div>
        </form>
    </div>

    <script>
        function validarFormulario(event) {
            const actual = document.getElementById('contrasena_actual').value;
            const nueva = document.getElementById('nueva_contrasena').value;
            const confirmar = document.getElementById('confirmar_contrasena').value;

            if (!actual || !nueva || !confirmar) {
                alert('Por favor completa todos los campos');
                event.preventDefault();
                return false;
            }

            if (nueva.length < 6) {
                alert('La nueva contraseña debe tener al menos 6 caracteres');
                event.preventDefault();
                return false;
            }

            if (nueva !== confirmar) {
                alert('Las nuevas contraseñas no coinciden');
                event.preventDefault();
                return false;
            }

            return true;
        }
    </script>
</body>
</html>
