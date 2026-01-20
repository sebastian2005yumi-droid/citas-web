<?php
// Suprimir notices de PHP
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Verificar si el usuario está registrado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si intenta acceder a agendar sin estar registrado
if (!isset($_SESSION['usuario_registrado']) || $_SESSION['usuario_registrado'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Acceso Restringido - AppoinMed</title>
        <link rel="stylesheet" href="css/formulario.css">
        <style>
            * {
                box-sizing: border-box;
            }
            
            body {
                margin: 0;
                padding: 0;
                background: linear-gradient(135deg, #0d47a1 0%, #1565c0 50%, #1976d2 100%);
                min-height: 100vh;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            header {
                position: fixed;
                top: 0;
                width: 100%;
                z-index: 1000;
            }

            .contenedor-acceso {
                background: white;
                border-radius: 20px;
                padding: 60px 50px;
                max-width: 600px;
                width: 100%;
                box-shadow: 0 25px 70px rgba(13, 71, 161, 0.3);
                animation: deslizar 0.6s ease;
                text-align: center;
            }

            .icono-candado {
                font-size: 120px;
                margin-bottom: 30px;
                animation: rebotar 1s ease infinite;
            }

            h1 {
                color: #0d47a1;
                font-size: 36px;
                margin: 0 0 20px 0;
                font-weight: 700;
                letter-spacing: -0.5px;
            }

            .subtitulo {
                color: #666;
                font-size: 16px;
                line-height: 1.8;
                margin: 0 0 25px 0;
            }

            .subtitulo strong {
                color: #0d47a1;
                font-weight: 600;
            }

            .descripcion {
                background: linear-gradient(135deg, #f0f7ff 0%, #e3f2fd 100%);
                border-left: 5px solid #1976d2;
                padding: 20px;
                border-radius: 10px;
                margin: 30px 0;
                color: #444;
                font-size: 14px;
                line-height: 1.7;
            }

            .botones {
                display: flex;
                gap: 15px;
                margin-top: 40px;
                justify-content: center;
                flex-wrap: wrap;
            }

            .btn {
                padding: 15px 36px;
                border: none;
                border-radius: 12px;
                font-weight: 600;
                font-size: 15px;
                cursor: pointer;
                text-decoration: none;
                transition: all 0.3s ease;
                display: inline-flex;
                align-items: center;
                gap: 10px;
                min-width: 180px;
                justify-content: center;
            }

            .btn-primario {
                background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
                color: white;
                box-shadow: 0 8px 20px rgba(21, 101, 192, 0.3);
            }

            .btn-primario:hover {
                transform: translateY(-3px);
                box-shadow: 0 12px 30px rgba(21, 101, 192, 0.4);
            }

            .btn-secundario {
                background: #f8f9fa;
                color: #1565c0;
                border: 2px solid #1976d2;
            }

            .btn-secundario:hover {
                background: #e3f2fd;
                transform: translateY(-2px);
            }

            @keyframes deslizar {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @keyframes rebotar {
                0%, 100% {
                    transform: translateY(0);
                }
                50% {
                    transform: translateY(-20px);
                }
            }

            @media (max-width: 600px) {
                .contenedor-acceso {
                    margin: 20px;
                    padding: 40px 25px;
                }

                h1 {
                    font-size: 28px;
                }

                .icono-candado {
                    font-size: 100px;
                    margin-bottom: 20px;
                }

                .botones {
                    flex-direction: column;
                }

                .btn {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <div class="contenedor-acceso">
            <div class="icono-candado">🔐</div>
            
            <h1>Acceso Restringido</h1>
            
            <p class="subtitulo">
                Para agendar una cita médica, primero debes <strong>registrarte</strong> como paciente en nuestro sistema.
            </p>

            <div class="descripcion">
                <strong>✓ Ventajas de registrarse:</strong><br>
                Acceso rápido a tus citas, historial médico seguro, gestión completa de tus citas y notificaciones automáticas.
            </div>

            <div class="botones">
                <a href="Formulario.html" class="btn btn-primario">
                    <span>✓</span> Ir a Registro
                </a>
                <a href="Inicio.html" class="btn btn-secundario">
                    <span>←</span> Volver a Inicio
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>
