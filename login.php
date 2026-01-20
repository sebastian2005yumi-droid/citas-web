<?php
// Suprimir notices de PHP
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Iniciar sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que sea POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: Formulario.html");
    exit;
}

// Obtener datos del formulario
$correo = htmlspecialchars(trim($_POST['correo'] ?? ''));
$clave = $_POST['clave'] ?? '';

// Validar que no estén vacíos
if (empty($correo) || empty($clave)) {
    die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Error de Validación</h2><p style='font-size:16px; color:#333;'>Por favor completa todos los campos.</p><a href='Formulario.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
}

// Validar formato de email
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Correo Inválido</h2><p style='font-size:16px; color:#333;'>El correo no tiene un formato válido.</p><a href='Formulario.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
}

// Verificar si el archivo de registros existe
$archivo_registros = "registros.json";
if (!file_exists($archivo_registros)) {
    die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Usuario No Encontrado</h2><p style='font-size:16px; color:#333;'>No hay registros de usuarios. Por favor regístrate primero.</p><a href='Formulario.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Ir a Registro</a></div>");
}

// Leer el archivo JSON de registros
$json_content = file_get_contents($archivo_registros);
$registros = json_decode($json_content, true);

if (!is_array($registros) || empty($registros)) {
    die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Usuario No Encontrado</h2><p style='font-size:16px; color:#333;'>No hay registros de usuarios. Por favor regístrate primero.</p><a href='Formulario.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Ir a Registro</a></div>");
}

// Buscar usuario por correo (sin espacios, minúsculas)
$usuario_encontrado = null;
$correo_busqueda = strtolower(trim($correo));

foreach ($registros as $registro) {
    $correo_registrado = strtolower(trim($registro['correo'] ?? ''));
    if ($correo_registrado === $correo_busqueda) {
        $usuario_encontrado = $registro;
        break;
    }
}

// Si no encuentra el usuario
if ($usuario_encontrado === null) {
    die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Usuario No Encontrado</h2><p style='font-size:16px; color:#333;'>No existe una cuenta registrada con el correo <strong>" . htmlspecialchars($correo) . "</strong></p><p style='font-size:14px; color:#666; margin-top: 10px;'>Verifica que el correo sea correcto o regístrate si no tienes cuenta.</p><a href='Formulario.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Ir a Registro</a></div>");
}

// Verificar que tenga la contraseña encriptada
if (!isset($usuario_encontrado['clave_encriptada']) || empty($usuario_encontrado['clave_encriptada'])) {
    die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Error en la Cuenta</h2><p style='font-size:16px; color:#333;'>Hay un problema con tu cuenta. Por favor contacta al soporte.</p><a href='Formulario.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Volver</a></div>");
}

// Verificar contraseña usando password_verify
if (!password_verify($clave, $usuario_encontrado['clave_encriptada'])) {
    die("<div style='max-width:600px; margin:150px auto; padding:40px; background:#ffebee; border:2px solid #f44336; border-radius:10px; text-align:center; font-family:Arial;'><h2 style='color:#c62828;'>❌ Contraseña Incorrecta</h2><p style='font-size:16px; color:#333;'>La contraseña que ingresaste es incorrecta. Intenta de nuevo.</p><a href='Formulario.html' style='display:inline-block; background:#f44336; color:white; padding:12px 30px; border-radius:8px; text-decoration:none; font-weight:bold; margin-top:15px;'>Intentar de Nuevo</a></div>");
}

// Crear sesión con datos del usuario
$_SESSION['usuario_registrado'] = true;
$_SESSION['usuario_nombre'] = ($usuario_encontrado['nombres'] ?? '') . " " . ($usuario_encontrado['apellidos'] ?? '');
$_SESSION['usuario_correo'] = $usuario_encontrado['correo'];
$_SESSION['usuario_cedula'] = $usuario_encontrado['cedula'] ?? '';

// Redirigir al dashboard
header("Location: dashboard.php");
exit;
?>
