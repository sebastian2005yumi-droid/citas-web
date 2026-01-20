<?php
/**
 * Script de inicialización y validación de archivos JSON
 * Ejecutar una sola vez para asegurar que los archivos estén correctamente creados
 */

$archivos_json = [
    "registros.json" => [],
    "chat_mensajes.json" => [],
    "recordatorios.json" => []
];

$resultados = [];

foreach ($archivos_json as $archivo => $contenido_default) {
    // Si el archivo no existe, crearlo
    if (!file_exists($archivo)) {
        $json = json_encode($contenido_default, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents($archivo, $json, LOCK_EX)) {
            $resultados[] = "✅ Creado: $archivo";
        } else {
            $resultados[] = "❌ Error al crear: $archivo (Verifica permisos)";
        }
    } else {
        // Validar que sea JSON válido
        $contenido = file_get_contents($archivo);
        $decoded = json_decode($contenido, true);
        
        if ($decoded !== null) {
            $resultados[] = "✅ Válido: $archivo (" . count($decoded) . " registros)";
        } else {
            // Si el archivo está corrupto, reconstruirlo
            $json = json_encode($contenido_default, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            if (file_put_contents($archivo, $json, LOCK_EX)) {
                $resultados[] = "⚠️ Reparado: $archivo (estaba corrupto)";
            } else {
                $resultados[] = "❌ No se pudo reparar: $archivo";
            }
        }
    }
}

// Mostrar resultados
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicialización de Sistema</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 600px;
            width: 100%;
        }
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        .resultado {
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            border-left: 4px solid #4CAF50;
            background: #f1f8f4;
        }
        .resultado.error {
            border-left-color: #f44336;
            background: #ffebee;
        }
        .resultado.warning {
            border-left-color: #ff9800;
            background: #fff3e0;
        }
        .botones {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            justify-content: center;
        }
        a, button {
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
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
        .success {
            color: #2e7d32;
        }
        .error {
            color: #c62828;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Inicialización del Sistema</h1>
        
        <div style="margin-bottom: 30px;">
            <?php foreach ($resultados as $resultado): ?>
                <?php 
                    $clase = 'resultado';
                    if (strpos($resultado, '❌') !== false) {
                        $clase .= ' error';
                    } elseif (strpos($resultado, '⚠️') !== false) {
                        $clase .= ' warning';
                    }
                ?>
                <div class="<?php echo $clase; ?>">
                    <?php echo htmlspecialchars($resultado); ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="botones">
            <a href="Inicio.php" class="btn-primary">← Volver al Inicio</a>
            <a href="Formulario.html" class="btn-secondary">📝 Registrarse</a>
        </div>
    </div>
</body>
</html>
