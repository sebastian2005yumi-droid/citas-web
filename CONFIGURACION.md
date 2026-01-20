# 🔧 Guía de Configuración - Sistema de Citas

## ✅ Archivos Críticos para el Guardado

El sistema utiliza 3 archivos JSON para persistencia de datos:

### 1. **registros.json** - Base de datos de usuarios
- 📁 Ubicación: `/registros.json`
- 📌 Contenido: Información de usuarios registrados
- 🔑 Campos: nombre, apellido, cédula, correo, contraseña encriptada, etc.
- 🔒 Seguridad: Las contraseñas se guardan encriptadas con BCRYPT

### 2. **chat_mensajes.json** - Mensajes del chat en vivo
- 📁 Ubicación: `/chat_mensajes.json`
- 📌 Contenido: Conversaciones entre pacientes y soporte
- 🔑 Campos: remitente, mensaje, timestamp, correo del usuario

### 3. **recordatorios.json** - Citas y recordatorios
- 📁 Ubicación: `/recordatorios.json`
- 📌 Contenido: Citas agendadas de los pacientes
- 🔑 Campos: fecha, hora, doctor, especialidad, correo del usuario

---

## 🚀 Scripts de Inicialización

### 1. **inicializar-json.php**
Inicializa todos los archivos JSON si no existen o están corruptos.

**Cómo usar:**
```
Accede a: http://tu-servidor/inicializar-json.php
```

### 2. **prueba-guardado.php**
Verifica que el sistema de guardado funciona correctamente.

**Cómo usar:**
```
Accede a: http://tu-servidor/prueba-guardado.php
```

Datos de prueba creados:
- **Correo:** juan@example.com
- **Contraseña:** 123456

---

## 🐛 Solución de Problemas

### Problema: "Usuario no encontrado" al iniciar sesión

**Posibles causas:**
1. El archivo `registros.json` está vacío
2. El usuario no fue guardado correctamente
3. Problemas de permisos en el servidor

**Soluciones:**

#### ✅ Opción 1: Usar el script de prueba
```
1. Accede a http://tu-servidor/prueba-guardado.php
2. Verificará y creará un usuario de prueba
3. Podrás iniciar sesión con: juan@example.com / 123456
```

#### ✅ Opción 2: Ejecutar inicialización
```
1. Accede a http://tu-servidor/inicializar-json.php
2. Reparará los archivos JSON si están corruptos
3. Vuelve a registrarte normalmente
```

#### ✅ Opción 3: Verificar permisos
```
Windows:
- Click derecho en carpeta → Propiedades → Seguridad
- Asegúrate que el usuario IUSR (IIS) o NETWORK SERVICE tenga permisos de escritura

Linux/Unix:
- Ejecuta: chmod 755 /ruta/a/carpeta/citas
- Ejecuta: chmod 666 /ruta/a/carpeta/citas/*.json
```

---

## 📊 Flujo de Datos

### Registro de Usuario
```
Formulario → registrar.php → registros.json + registros.txt + Sesión
```

### Inicio de Sesión
```
Formulario → login.php → Busca en registros.json → Valida contraseña → Sesión
```

### Chat en Vivo
```
Mensaje → chat-soporte.php → chat_mensajes.json
```

### Recordatorios de Citas
```
Cita → recordatorios.php → recordatorios.json → Dashboard
```

---

## 🔐 Seguridad

### ✅ Implementado
- [x] Contraseñas encriptadas con BCRYPT
- [x] Validación de emails
- [x] Protección contra XSS con `htmlspecialchars()`
- [x] Sesiones protegidas
- [x] LOCK_EX en guardados para evitar corrupción

### 📋 Campos Guardados en Registro
```json
{
  "id": 1705318200,
  "fecha_registro": "2026-01-15 10:00:00",
  "nombres": "Juan",
  "apellidos": "Pérez",
  "cedula": "1234567890",
  "correo": "juan@example.com",
  "clave_encriptada": "$2y$10$...",
  "edad": "30",
  "fecha_nacimiento": "1996-01-15",
  "genero": "Masculino",
  "motivo_consulta": "Consulta general",
  "especialidad": "Medicina General",
  "satisfaccion": "9",
  "color_favorito": "Azul",
  "comentarios": "Observaciones",
  "foto": "usuario_1705318200_foto.jpg"
}
```

---

## ✅ Checklist de Verificación

Antes de usar el sistema en producción, ejecuta:

- [ ] `http://tu-servidor/inicializar-json.php` - Inicializar archivos
- [ ] `http://tu-servidor/prueba-guardado.php` - Verificar guardado
- [ ] Registrarse con un nuevo usuario en `Formulario.html`
- [ ] Iniciar sesión con la cuenta creada
- [ ] Verificar que los datos aparezcan en dashboard
- [ ] Probar el chat en vivo
- [ ] Probar recordatorios de citas

---

## 📞 Soporte

Si tienes problemas:

1. Revisa los logs del servidor (error_log en directorio raíz)
2. Ejecuta `prueba-guardado.php` para diagnosticar
3. Verifica los permisos del servidor
4. Asegúrate que PHP versión sea ≥ 7.0

---

**Última actualización:** 15 de Enero de 2026  
**Sistema:** AppoinMed - Sistema de Citas Médicas
