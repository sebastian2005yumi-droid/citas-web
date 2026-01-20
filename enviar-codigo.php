<?php
// Suprimir notices de PHP
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que sea POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: recuperar-contrasena.html");
    exit;
}

// Obtener correo
$correo = htmlspecialchars(trim($_POST['correo'] ?? ''));

// Validar que no esté vacío
if (empty($correo)) {
    die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Error</h2><p style='font-size:16px; color:#333;'>Por favor ingresa tu correo electrónico.</p><a href='recuperar-contrasena.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
}

// Validar email
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Correo Inválido</h2><p style='font-size:16px; color:#333;'>Por favor ingresa un correo válido.</p><a href='recuperar-contrasena.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
}

// Verificar si el archivo de registros existe
$archivo_registros = "registros.json";
if (!file_exists($archivo_registros)) {
    die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Usuario No Encontrado</h2><p style='font-size:16px; color:#333;'>No existe una cuenta con este correo electrónico.</p><a href='recuperar-contrasena.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
}

// Leer registros
$json_content = file_get_contents($archivo_registros);
$registros = json_decode($json_content, true);

// Buscar usuario
$usuario_encontrado = false;
foreach ($registros as $registro) {
    if ($registro['correo'] === $correo) {
        $usuario_encontrado = true;
        break;
    }
}

if (!$usuario_encontrado) {
    die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Usuario No Encontrado</h2><p style='font-size:16px; color:#333;'>No existe una cuenta registrada con este correo electrónico.</p><a href='recuperar-contrasena.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
}

// Generar código de verificación (6 dígitos)
$codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

// Guardar código en sesión (válido por 15 minutos)
$_SESSION['codigo_verificacion'] = $codigo;
$_SESSION['correo_recuperacion'] = $correo;
$_SESSION['tiempo_codigo'] = time();

// Mostrar código (en producción se enviaría por email)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código Enviado - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d47a1 0%, #1565c0 50%, #1976d2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .codigo-container {
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
        }

        h1 {
            color: #0d47a1;
            font-size: 28px;
            margin: 0 0 10px 0;
            font-weight: 700;
        }

        .descripcion {
            color: #666;
            font-size: 15px;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .codigo-box {
            background: linear-gradient(135deg, #f0f7ff 0%, #e3f2fd 100%);
            border: 2px solid #1976d2;
            border-radius: 12px;
            padding: 30px;
            margin: 30px 0;
        }

        .codigo-titulo {
            color: #0d47a1;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            margin-bottom: 15px;
            letter-spacing: 0.5px;
        }

        .codigo-valor {
            font-size: 48px;
            font-weight: 700;
            color: #1565c0;
            letter-spacing: 8px;
            font-family: 'Courier New', monospace;
            margin: 0;
        }

        .tiempo-expiracion {
            color: #f44336;
            font-size: 13px;
            font-weight: 600;
            margin-top: 15px;
        }

        .aviso {
            background: #e8f5e9;
            border-left: 4px solid #4caf50;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 25px;
            color: #2e7d32;
            font-size: 14px;
            line-height: 1.6;
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
            text-decoration: none;
            transition: all 0.3s ease;
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
            .codigo-container {
                margin: 80px 20px;
                padding: 25px;
            }

            .codigo-valor {
                font-size: 36px;
                letter-spacing: 6px;
            }

            .botones {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="codigo-container">
        <div class="icono">✉️</div>
        <h1>Código de Verificación</h1>
        <p class="descripcion">Hemos generado un código para verificar tu identidad y cambiar tu contraseña.</p>

        <div class="aviso">
            ✓ El código ha sido enviado a: <strong><?php echo htmlspecialchars($correo); ?></strong>
        </div>

        <div class="codigo-box">
            <div class="codigo-titulo">Tu Código</div>
            <p class="codigo-valor"><?php echo $codigo; ?></p>
            <div class="tiempo-expiracion">⏰ Este código expira en 15 minutos</div>
        </div>

        <form action="cambiar-contrasena.php" method="POST">
            <div style="margin-bottom: 20px;">
                <label style="display: block; color: #0d47a1; font-weight: 600; margin-bottom: 10px; font-size: 14px; text-transform: uppercase;">Ingresa el Código</label>
                <input type="text" name="codigo" placeholder="Ej: 123456" maxlength="6" required style="width: 100%; padding: 14px 16px; border: 2px solid #e3f2fd; border-radius: 10px; background: #f8f9fa; color: #1a1a1a; font-size: 18px; font-weight: 600; text-align: center; font-family: 'Courier New', monospace;">
            </div>

            <div class="botones">
                <a href="recuperar-contrasena.html" class="btn btn-secundario">← Volver</a>
                <button type="submit" class="btn btn-primario">✓ Continuar</button>
            </div>
        </form>
    </div>

</body>
</html>
