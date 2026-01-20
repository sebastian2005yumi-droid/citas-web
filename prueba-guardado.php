<?php
/**
 * Script de prueba para verificar el guardado de registros
 */

// Crear un usuario de prueba
$usuario_prueba = [
    "id" => time(),
    "fecha_registro" => date("Y-m-d H:i:s"),
    "nombres" => "Juan",
    "apellidos" => "Pérez",
    "cedula" => "1234567890",
    "correo" => "juan@example.com",
    "clave_encriptada" => password_hash("123456", PASSWORD_BCRYPT),
    "edad" => "30",
    "fecha_nacimiento" => "1996-01-15",
    "genero" => "Masculino",
    "motivo_consulta" => "Consulta general",
    "especialidad" => "Medicina General",
    "satisfaccion" => "9",
    "color_favorito" => "Azul",
    "comentarios" => "Usuario de prueba",
    "foto" => ""
];

$archivo_json = "registros.json";
$registros = [];

// Leer registros existentes
if (file_exists($archivo_json)) {
    $json_content = file_get_contents($archivo_json);
    if (!empty($json_content)) {
        $registros = json_decode($json_content, true);
        if (!is_array($registros)) {
            $registros = [];
        }
    }
}

// Agregar usuario de prueba
$registros[] = $usuario_prueba;

// Intentar guardar
$json_resultado = json_encode($registros, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

if ($json_resultado !== false) {
    $guardado = file_put_contents($archivo_json, $json_resultado, LOCK_EX);
    if ($guardado !== false) {
        $exito = true;
    } else {
        $exito = false;
        $error = "Problema de permisos o disco lleno";
    }
} else {
    $exito = false;
    $error = json_last_error_msg();
}

// Verificar lectura
$contenido_guardado = file_get_contents($archivo_json);
$usuarios_guardados = json_decode($contenido_guardado, true);
$cantidad = is_array($usuarios_guardados) ? count($usuarios_guardados) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prueba de Guardado</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .status {
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            font-size: 16px;
        }
        .success {
            background: #e8f5e9;
            border-left: 4px solid #4CAF50;
            color: #2e7d32;
        }
        .error {
            background: #ffebee;
            border-left: 4px solid #f44336;
            color: #c62828;
        }
        .info {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
            color: #1565c0;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
        }
        .code {
            background: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            font-family: 'Courier New', monospace;
            font-size: 13px;
            overflow-x: auto;
            max-height: 300px;
            overflow-y: auto;
        }
        .pruebas {
            margin: 30px 0;
        }
        .prueba {
            margin: 15px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #2196F3;
            border-radius: 4px;
        }
        .ok {
            color: #4CAF50;
            font-weight: bold;
        }
        .botones {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }
        a {
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-block;
        }
        .btn-primary {
            background: #667eea;
            color: white;
        }
        .btn-primary:hover {
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        .btn-secondary:hover {
            background: #d0d0d0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Prueba de Guardado de Registros</h1>

        <?php if ($exito): ?>
            <div class="status success">
                ✅ <strong>Guardado Exitoso</strong><br>
                El usuario de prueba fue guardado correctamente en registros.json
            </div>
        <?php else: ?>
            <div class="status error">
                ❌ <strong>Error en Guardado</strong><br>
                <?php echo htmlspecialchars($error ?? "Error desconocido"); ?>
            </div>
        <?php endif; ?>

        <div class="info">
            <strong>📊 Estadísticas:</strong><br>
            • Total de usuarios: <strong><?php echo $cantidad; ?></strong><br>
            • Archivo: <strong>registros.json</strong><br>
            • Tamaño: <strong><?php echo filesize($archivo_json); ?> bytes</strong>
        </div>

        <div class="pruebas">
            <h2>📋 Verificaciones Realizadas</h2>
            
            <div class="prueba">
                <span class="ok">✓</span> Archivo registros.json existe: 
                <?php echo file_exists($archivo_json) ? '<span class="ok">SÍ</span>' : 'NO'; ?>
            </div>

            <div class="prueba">
                <span class="ok">✓</span> Archivo es legible: 
                <?php echo is_readable($archivo_json) ? '<span class="ok">SÍ</span>' : 'NO'; ?>
            </div>

            <div class="prueba">
                <span class="ok">✓</span> Archivo es escribible: 
                <?php echo is_writable($archivo_json) ? '<span class="ok">SÍ</span>' : 'NO'; ?>
            </div>

            <div class="prueba">
                <span class="ok">✓</span> JSON es válido: 
                <?php echo (json_decode($contenido_guardado) !== null) ? '<span class="ok">SÍ</span>' : 'NO'; ?>
            </div>

            <div class="prueba">
                <span class="ok">✓</span> Usuarios guardados en JSON: 
                <strong><?php echo $cantidad; ?></strong>
            </div>
        </div>

        <h2>🔐 Datos del Usuario de Prueba</h2>
        <div class="info">
            <strong>Correo:</strong> juan@example.com<br>
            <strong>Contraseña:</strong> 123456<br>
            <strong>Nombre:</strong> Juan Pérez
        </div>

        <h2>📝 Contenido del JSON</h2>
        <div class="code">
            <pre><?php echo htmlspecialchars(json_encode($usuarios_guardados, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8'); ?></pre>
        </div>

        <div class="botones">
            <a href="Formulario.html" class="btn-primary">📝 Ir al Registro</a>
            <a href="login.php" class="btn-secondary">🔐 Ir al Login</a>
            <a href="Inicio.php" class="btn-secondary">← Volver</a>
        </div>
    </div>
</body>
</html>
