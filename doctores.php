<?php
// Suprimir notices de PHP
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);

// Verificar si el usuario está registrado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio de Doctores - AppoinMed</title>
    <link rel="stylesheet" href="css/formulario.css">
    <style>
        .doctores-container {
            max-width: 1400px;
            margin: 140px auto 80px;
            padding: 0 30px;
        }

        .doctores-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .doctores-header h1 {
            color: #0d47a1;
            font-size: 40px;
            margin: 0 0 15px 0;
            font-weight: 700;
        }

        .doctores-header p {
            color: #666;
            font-size: 17px;
            margin: 0;
            max-width: 600px;
            margin: 0 auto;
        }

        .filtros {
            display: flex;
            gap: 15px;
            margin-bottom: 50px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .filtros select {
            padding: 12px 20px;
            border: 2px solid #e3f2fd;
            border-radius: 10px;
            background: white;
            color: #1a1a1a;
            font-size: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filtros select:hover {
            border-color: #1976d2;
        }

        .grid-doctores {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 40px;
            margin-bottom: 50px;
        }

        .doctor-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: 1px solid #f0f0f0;
        }

        .doctor-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 55px rgba(13, 71, 161, 0.15);
            border-color: #1976d2;
        }

        .doctor-foto {
            width: 100%;
            height: 300px;
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 100px;
            border-bottom: 2px solid #e3f2fd;
        }

        .doctor-info {
            padding: 30px;
        }

        .doctor-nombre {
            color: #0d47a1;
            font-size: 22px;
            font-weight: 700;
            margin: 0 0 8px 0;
        }

        .doctor-especialidad {
            color: #1976d2;
            font-weight: 600;
            margin: 0 0 15px 0;
            font-size: 15px;
        }

        .doctor-detalles {
            color: #666;
            font-size: 14px;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .doctor-horario {
            background: #f0f7ff;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #0d47a1;
            font-weight: 500;
        }

        .doctor-calificacion {
            color: #ffc107;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .doctor-botones {
            display: flex;
            gap: 10px;
        }

        .doctor-botones a {
            flex: 1;
            padding: 12px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .btn-cita {
            background: linear-gradient(135deg, #1565c0 0%, #1976d2 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(21, 101, 192, 0.25);
        }

        .btn-cita:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(21, 101, 192, 0.4);
        }

        .btn-info {
            background: #f3f8fc;
            color: #1565c0;
            border: 2px solid #1976d2;
        }

        .btn-info:hover {
            background: #e3f2fd;
        }

        @media (max-width: 900px) {
            .doctores-container {
                margin: 110px auto 60px;
                padding: 0 20px;
            }

            .grid-doctores {
                grid-template-columns: 1fr;
                gap: 30px;
            }

            .doctores-header h1 {
                font-size: 32px;
            }
        }

        @media (max-width: 600px) {
            .doctores-container {
                margin: 100px auto 40px;
                padding: 0 15px;
            }

            .doctores-header h1 {
                font-size: 26px;
            }

            .filtros {
                flex-direction: column;
            }

            .filtros select {
                width: 100%;
            }

            .doctor-info {
                padding: 20px;
            }

            .doctor-foto {
                height: 250px;
            }
        }
    </style>
</head>
<body>
    <!-- ENCABEZADO -->
    <header class="top-bar">
        <nav class="nav">
            <div class="logo">
                <img src="Img/imagen1.png" alt="Logo">
                <span>Sistema de Citas</span>
            </div>
            <ul class="menu">
                <li><a href="Inicio.php">Inicio</a></li>
                <?php if (isset($_SESSION['usuario_registrado']) && $_SESSION['usuario_registrado'] === true) { ?>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="historial-citas.php">Mis Citas</a></li>
                <?php } ?>
                <li><a href="doctores.php" class="active">Doctores</a></li>
                <li><a href="sobre-nosotros.html">Sobre Nosotros</a></li>
                <li><a href="contacto.html">Contacto</a></li>
                <?php if (isset($_SESSION['usuario_registrado']) && $_SESSION['usuario_registrado'] === true) { ?>
                    <li><a href="logout.php" class="logout">Cerrar Sesión</a></li>
                <?php } else { ?>
                    <li><a href="Formulario.html">Acceso</a></li>
                <?php } ?>
            </ul>
        </nav>
    </header>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="doctores-container fade-in">
        <!-- HEADER -->
        <div class="doctores-header">
            <h1>👨‍⚕️ Nuestros Doctores</h1>
            <p>Conoce a nuestro equipo de especialistas altamente calificados, listos para cuidar de tu salud.</p>
        </div>

        <!-- FILTROS -->
        <div class="filtros">
            <select id="filtro-especialidad">
                <option value="">Todas las especialidades</option>
                <option value="Medicina General">Medicina General</option>
                <option value="Pediatría">Pediatría</option>
                <option value="Cardiología">Cardiología</option>
                <option value="Dermatología">Dermatología</option>
                <option value="Odontología">Odontología</option>
            </select>
        </div>

        <!-- GRID DE DOCTORES -->
        <div class="grid-doctores">
            <!-- DOCTOR 1 -->
            <div class="doctor-card">
                <div class="doctor-foto">👨‍⚕️</div>
                <div class="doctor-info">
                    <h3 class="doctor-nombre">Dr. Carlos Rodríguez</h3>
                    <p class="doctor-especialidad">Medicina General</p>
                    <div class="doctor-calificacion">⭐⭐⭐⭐⭐ (4.8)</div>
                    <div class="doctor-detalles">
                        <strong>Experiencia:</strong> 15 años<br>
                        <strong>Pacientes:</strong> 1,200+<br>
                    </div>
                    <div class="doctor-horario">Lunes a Viernes: 8:00 AM - 5:00 PM</div>
                    <div class="doctor-botones">
                        <a href="index.php" class="btn-cita">📅 Agendar</a>
                        <a href="#" class="btn-info">ℹ️ Info</a>
                    </div>
                </div>
            </div>

            <!-- DOCTOR 2 -->
            <div class="doctor-card">
                <div class="doctor-foto">👨‍⚕️</div>
                <div class="doctor-info">
                    <h3 class="doctor-nombre">Dra. María López</h3>
                    <p class="doctor-especialidad">Pediatría</p>
                    <div class="doctor-calificacion">⭐⭐⭐⭐⭐ (5.0)</div>
                    <div class="doctor-detalles">
                        <strong>Experiencia:</strong> 12 años<br>
                        <strong>Pacientes:</strong> 800+<br>
                    </div>
                    <div class="doctor-horario">Martes a Sábado: 9:00 AM - 6:00 PM</div>
                    <div class="doctor-botones">
                        <a href="index.php" class="btn-cita">📅 Agendar</a>
                        <a href="#" class="btn-info">ℹ️ Info</a>
                    </div>
                </div>
            </div>

            <!-- DOCTOR 3 -->
            <div class="doctor-card">
                <div class="doctor-foto">👨‍⚕️</div>
                <div class="doctor-info">
                    <h3 class="doctor-nombre">Dr. Juan Martínez</h3>
                    <p class="doctor-especialidad">Cardiología</p>
                    <div class="doctor-calificacion">⭐⭐⭐⭐ (4.9)</div>
                    <div class="doctor-detalles">
                        <strong>Experiencia:</strong> 20 años<br>
                        <strong>Pacientes:</strong> 1,500+<br>
                    </div>
                    <div class="doctor-horario">Lunes a Viernes: 10:00 AM - 4:00 PM</div>
                    <div class="doctor-botones">
                        <a href="index.php" class="btn-cita">📅 Agendar</a>
                        <a href="#" class="btn-info">ℹ️ Info</a>
                    </div>
                </div>
            </div>

            <!-- DOCTOR 4 -->
            <div class="doctor-card">
                <div class="doctor-foto">👩‍⚕️</div>
                <div class="doctor-info">
                    <h3 class="doctor-nombre">Dra. Laura Sánchez</h3>
                    <p class="doctor-especialidad">Dermatología</p>
                    <div class="doctor-calificacion">⭐⭐⭐⭐⭐ (4.7)</div>
                    <div class="doctor-detalles">
                        <strong>Experiencia:</strong> 10 años<br>
                        <strong>Pacientes:</strong> 900+<br>
                    </div>
                    <div class="doctor-horario">Miércoles a Domingo: 11:00 AM - 7:00 PM</div>
                    <div class="doctor-botones">
                        <a href="index.php" class="btn-cita">📅 Agendar</a>
                        <a href="#" class="btn-info">ℹ️ Info</a>
                    </div>
                </div>
            </div>

            <!-- DOCTOR 5 -->
            <div class="doctor-card">
                <div class="doctor-foto">👨‍⚕️</div>
                <div class="doctor-info">
                    <h3 class="doctor-nombre">Dr. Roberto García</h3>
                    <p class="doctor-especialidad">Odontología</p>
                    <div class="doctor-calificacion">⭐⭐⭐⭐⭐ (4.9)</div>
                    <div class="doctor-detalles">
                        <strong>Experiencia:</strong> 18 años<br>
                        <strong>Pacientes:</strong> 1,100+<br>
                    </div>
                    <div class="doctor-horario">Lunes a Sábado: 8:00 AM - 8:00 PM</div>
                    <div class="doctor-botones">
                        <a href="index.php" class="btn-cita">📅 Agendar</a>
                        <a href="#" class="btn-info">ℹ️ Info</a>
                    </div>
                </div>
            </div>

            <!-- DOCTOR 6 -->
            <div class="doctor-card">
                <div class="doctor-foto">👨‍⚕️</div>
                <div class="doctor-info">
                    <h3 class="doctor-nombre">Dr. Miguel Flores</h3>
                    <p class="doctor-especialidad">Medicina General</p>
                    <div class="doctor-calificacion">⭐⭐⭐⭐ (4.6)</div>
                    <div class="doctor-detalles">
                        <strong>Experiencia:</strong> 8 años<br>
                        <strong>Pacientes:</strong> 600+<br>
                    </div>
                    <div class="doctor-horario">Martes a Jueves: 2:00 PM - 9:00 PM</div>
                    <div class="doctor-botones">
                        <a href="index.php" class="btn-cita">📅 Agendar</a>
                        <a href="#" class="btn-info">ℹ️ Info</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer">
        <p>© 2026 Sistema de Citas Médicas | AppoinMed</p>
    </footer>

</body>
</html>
