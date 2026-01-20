<?php
// Archivo de verificación de registros (solo para debugging)
// Este archivo ayuda a diagnosticar problemas con el login

error_reporting(E_ALL);

echo "<h2>🔍 Verificación de Registros</h2>";

$archivo_registros = "registros.json";

if (!file_exists($archivo_registros)) {
    echo "<p style='color: red;'>❌ El archivo registros.json NO existe</p>";
    exit;
}

echo "<p>✅ El archivo registros.json existe</p>";

$json_content = file_get_contents($archivo_registros);
echo "<p><strong>Tamaño del archivo:</strong> " . filesize($archivo_registros) . " bytes</p>";

$registros = json_decode($json_content, true);

if ($registros === null) {
    echo "<p style='color: red;'>❌ Error al decodificar JSON: " . json_last_error_msg() . "</p>";
    echo "<p><strong>Contenido actual:</strong></p>";
    echo "<pre>" . htmlspecialchars($json_content) . "</pre>";
    exit;
}

if (!is_array($registros)) {
    echo "<p style='color: red;'>❌ El JSON no es un array válido</p>";
    exit;
}

echo "<p style='color: green;'>✅ JSON válido</p>";
echo "<p><strong>Total de registros:</strong> " . count($registros) . "</p>";

if (count($registros) === 0) {
    echo "<p style='color: orange;'>⚠️ No hay registros en la base de datos</p>";
} else {
    echo "<h3>Registros guardados:</h3>";
    echo "<table border='1' style='width: 100%; border-collapse: collapse; margin-top: 10px;'>";
    echo "<tr style='background: #f0f0f0;'>";
    echo "<th style='padding: 10px;'>Nombres</th>";
    echo "<th style='padding: 10px;'>Apellidos</th>";
    echo "<th style='padding: 10px;'>Correo</th>";
    echo "<th style='padding: 10px;'>Cédula</th>";
    echo "<th style='padding: 10px;'>¿Tiene Contraseña?</th>";
    echo "</tr>";
    
    foreach ($registros as $reg) {
        echo "<tr>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($reg['nombres'] ?? 'N/A') . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($reg['apellidos'] ?? 'N/A') . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($reg['correo'] ?? 'N/A') . "</td>";
        echo "<td style='padding: 10px;'>" . htmlspecialchars($reg['cedula'] ?? 'N/A') . "</td>";
        echo "<td style='padding: 10px; text-align: center;'>";
        if (isset($reg['clave_encriptada']) && !empty($reg['clave_encriptada'])) {
            echo "✅ Sí";
        } else {
            echo "❌ No";
        }
        echo "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
}

echo "<hr style='margin-top: 30px;'>";
echo "<p><a href='Formulario.html'>← Volver a Registro</a></p>";
?>
