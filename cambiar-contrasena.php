<?php
// Suprimir notices de PHP
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que venga del formulario anterior
if (!isset($_SESSION['codigo_verificacion']) || !isset($_SESSION['correo_recuperacion'])) {
    header("Location: recuperar-contrasena.html");
    exit;
}

// PASO 1: Verificar el código
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['paso'])) {
    if ($_POST['paso'] === '1') {
        // Validar que el código no haya expirado (15 minutos = 900 segundos)
        $tiempo_actual = time();
        $tiempo_transcurrido = $tiempo_actual - $_SESSION['tiempo_codigo'];

        if ($tiempo_transcurrido > 900) {
            die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Código Expirado</h2><p style='font-size:16px; color:#333;'>El código ha expirado. Por favor solicita uno nuevo.</p><a href='recuperar-contrasena.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Solicitar Nuevo Código</a></div>");
        }

        $codigo_ingresado = htmlspecialchars(trim($_POST['codigo'] ?? ''));

        // Validar que el código sea correcto
        if ($codigo_ingresado !== $_SESSION['codigo_verificacion']) {
            die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Código Inválido</h2><p style='font-size:16px; color:#333;'>El código que ingresaste no es correcto. Intenta de nuevo.</p><a href='javascript:history.back()' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
        }

        // Marcar código como verificado
        $_SESSION['codigo_verificado'] = true;
    }
    // PASO 2: Cambiar la contraseña
    elseif ($_POST['paso'] === '2' && isset($_SESSION['codigo_verificado'])) {
        $nueva_contrasena = $_POST['nueva_contrasena'] ?? '';
        $confirmar_contrasena = $_POST['confirmar_contrasena'] ?? '';

        // Validar contraseña
        if (empty($nueva_contrasena) || empty($confirmar_contrasena)) {
            die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Error</h2><p style='font-size:16px; color:#333;'>Por favor completa todos los campos.</p><a href='javascript:history.back()' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
        }

        if (strlen($nueva_contrasena) < 6) {
            die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Contraseña Débil</h2><p style='font-size:16px; color:#333;'>La contraseña debe tener al menos 6 caracteres.</p><a href='javascript:history.back()' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
        }

        if ($nueva_contrasena !== $confirmar_contrasena) {
            die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Contraseñas No Coinciden</h2><p style='font-size:16px; color:#333;'>Las contraseñas que ingresaste no son iguales.</p><a href='javascript:history.back()' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
        }

        // Leer registros
        $archivo_registros = "registros.json";
        if (!file_exists($archivo_registros)) {
            die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Error</h2><p style='font-size:16px; color:#333;'>Ocurrió un error. Por favor intenta de nuevo.</p><a href='recuperar-contrasena.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
        }

        $json_content = file_get_contents($archivo_registros);
        $registros = json_decode($json_content, true);

        // Buscar y actualizar usuario
        $usuario_actualizado = false;
        for ($i = 0; $i < count($registros); $i++) {
            if ($registros[$i]['correo'] === $_SESSION['correo_recuperacion']) {
                $registros[$i]['clave_encriptada'] = password_hash($nueva_contrasena, PASSWORD_BCRYPT);
                $usuario_actualizado = true;
                break;
            }
        }

        if (!$usuario_actualizado) {
            die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Usuario No Encontrado</h2><p style='font-size:16px; color:#333;'>Ocurrió un error al procesar tu solicitud.</p><a href='recuperar-contrasena.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
        }

        // Guardar cambios
        file_put_contents($archivo_registros, json_encode($registros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Limpiar sesión
        unset($_SESSION['codigo_verificacion']);
        unset($_SESSION['correo_recuperacion']);
        unset($_SESSION['tiempo_codigo']);
        unset($_SESSION['codigo_verificado']);

        // Mostrar página de éxito
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Contraseña Actualizada - AppoinMed</title>
            <link rel="stylesheet" href="css/formulario.css">
            <style>
                body {
                    background: linear-gradient(135deg, #0d47a1 0%, #1565c0 50%, #1976d2 100%);
                    min-height: 100vh;
                    padding: 20px;
                }

                .exito-container {
                    max-width: 500px;
                    margin: 100px auto;
                    padding: 40px;
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
                    animation: slideIn 0.6s ease;
                    text-align: center;
                }

                .icono {
                    font-size: 80px;
                    margin-bottom: 20px;
                    animation: bounceIn 0.8s ease;
                }

                h1 {
                    color: #2e7d32;
                    font-size: 28px;
                    margin: 0 0 15px 0;
                    font-weight: 700;
                }

                .mensaje {
                    color: #555;
                    font-size: 16px;
                    line-height: 1.8;
                    margin-bottom: 30px;
                }

                .btn {
                    display: inline-block;
                    padding: 14px 40px;
                    background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
                    color: white;
                    border-radius: 10px;
                    text-decoration: none;
                    font-weight: 600;
                    font-size: 15px;
                    box-shadow: 0 4px 15px rgba(21, 101, 192, 0.25);
                    transition: all 0.3s ease;
                    margin-top: 15px;
                }

                .btn:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 8px 25px rgba(21, 101, 192, 0.4);
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

                @keyframes bounceIn {
                    0% {
                        transform: scale(0);
                    }
                    50% {
                        transform: scale(1.1);
                    }
                    100% {
                        transform: scale(1);
                    }
                }
            </style>
        </head>
        <body>
            <div class="exito-container">
                <div class="icono">✓</div>
                <h1>¡Contraseña Actualizada!</h1>
                <p class="mensaje">Tu contraseña ha sido cambiada exitosamente. Ya puedes iniciar sesión con tu nueva contraseña.</p>
                <a href="Formulario.html" class="btn">← Ir al Login</a>
            </div>
        </body>
        </html>
        <?php
        exit;
    }
}

// Si no es POST o no está verificado, mostrar el formulario
$codigo_verificado = isset($_SESSION['codigo_verificado']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $codigo_verificado ? 'Cambiar Contraseña' : 'Verificar Código'; ?> - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 50%, #1976d2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 500px;
            margin: 80px auto;
            padding: 40px;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.1);
            animation: slideIn 0.6s ease;
        }

        .icono {
            font-size: 60px;
            text-align: center;
            margin-bottom: 20px;
        }

        h1 {
            color: #0d47a1;
            font-size: 26px;
            margin: 0 0 10px 0;
            font-weight: 700;
            text-align: center;
        }

        .descripcion {
            color: #666;
            font-size: 14px;
            text-align: center;
            margin-bottom: 30px;
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

        input[type="text"],
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

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #1976d2;
            background: white;
        }

        .requisitos {
            background: #e3f2fd;
            border-left: 4px solid #1976d2;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 13px;
            color: #0d47a1;
        }

        .requisitos ul {
            margin: 0;
            padding-left: 20px;
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
        <?php if (!$codigo_verificado): ?>
            <!-- PASO 1: Verificar Código -->
            <div class="icono">🔐</div>
            <h1>Verificar Código</h1>
            <p class="descripcion">Ingresa el código de 6 dígitos que recibiste.</p>

            <form method="POST" onsubmit="validarCodigo(event)">
                <input type="hidden" name="paso" value="1">

                <div class="form-group">
                    <label for="codigo">Código de Verificación</label>
                    <input type="text" id="codigo" name="codigo" placeholder="123456" maxlength="6" required autofocus>
                </div>

                <div class="botones">
                    <a href="recuperar-contrasena.html" class="btn btn-secundario">← Cancelar</a>
                    <button type="submit" class="btn btn-primario">✓ Verificar</button>
                </div>
            </form>
        <?php else: ?>
            <!-- PASO 2: Cambiar Contraseña -->
            <div class="icono">🔑</div>
            <h1>Crear Nueva Contraseña</h1>
            <p class="descripcion">Ingresa tu nueva contraseña para recuperar acceso.</p>

            <div class="requisitos">
                <strong>Requisitos:</strong>
                <ul>
                    <li>Mínimo 6 caracteres</li>
                    <li>Ambos campos deben coincidir</li>
                </ul>
            </div>

            <form method="POST" onsubmit="validarContrasena(event)">
                <input type="hidden" name="paso" value="2">

                <div class="form-group">
                    <label for="nueva_contrasena">Nueva Contraseña</label>
                    <input type="password" id="nueva_contrasena" name="nueva_contrasena" placeholder="••••••••" required autofocus>
                </div>

                <div class="form-group">
                    <label for="confirmar_contrasena">Confirmar Contraseña</label>
                    <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" placeholder="••••••••" required>
                </div>

                <div class="botones">
                    <a href="recuperar-contrasena.html" class="btn btn-secundario">← Cancelar</a>
                    <button type="submit" class="btn btn-primario">✓ Actualizar</button>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <script>
        function validarCodigo(event) {
            const codigo = document.getElementById('codigo').value;
            
            if (!/^\d{6}$/.test(codigo)) {
                alert('El código debe contener exactamente 6 dígitos');
                event.preventDefault();
                return false;
            }
        }

        function validarContrasena(event) {
            const nueva = document.getElementById('nueva_contrasena').value;
            const confirmar = document.getElementById('confirmar_contrasena').value;

            if (nueva.length < 6) {
                alert('La contraseña debe tener al menos 6 caracteres');
                event.preventDefault();
                return false;
            }

            if (nueva !== confirmar) {
                alert('Las contraseñas no coinciden');
                event.preventDefault();
                return false;
            }
        }
    </script>
</body>
</html>
