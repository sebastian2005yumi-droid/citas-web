# 🔐 Sistema de Autenticación - Guía Rápida

## ¿Cómo Registrarse?

1. **Ve a la página de Registro** (Formulario.html)
2. **Completa el formulario** con:
   - ✅ Nombres y Apellidos
   - ✅ Cédula de Identidad
   - ✅ **Correo Electrónico** (único - no puede repetirse)
   - ✅ **Contraseña** (mínimo 6 caracteres)
   - Otros datos opcionales (foto, edad, especialidad, etc.)

3. **Haz clic en "Registrarse"**
4. **Verás un mensaje de confirmación** - Tu cuenta está creada

## ¿Cómo Iniciar Sesión?

1. **Ve a la página de Login** (Formulario.html - Tab "Iniciar Sesión")
2. **Ingresa:**
   - 📧 El correo electrónico con el que te registraste
   - 🔐 La contraseña que creaste
3. **Haz clic en "Entrar"**
4. **Si los datos son correctos**, se abrirá tu Dashboard

## ⚠️ Errores Comunes y Soluciones

| Error | Causa | Solución |
|-------|-------|----------|
| "Usuario No Encontrado" | El correo no está registrado | Verifica que el correo sea correcto. Si es nuevo, regístrate primero |
| "Contraseña Incorrecta" | Escribiste mal la contraseña | Verifica que sea la contraseña correcta (mayúsculas/minúsculas importan) |
| "Correo ya registrado" | Intentas registrarte con un correo que ya existe | Usa otro correo o inicia sesión si ya tienes cuenta |
| Campo vacío | No completaste todos los campos obligatorios | Completa los campos marcados con * |

## 💾 Dónde se Guardan los Registros

Los registros se guardan en: **`registros.json`**

El archivo contiene:
- ✅ Nombres y Apellidos
- ✅ Correo (en minúsculas para evitar errores)
- ✅ Cédula
- ✅ Contraseña (encriptada con BCRYPT)
- ✅ Edad, género, especialidad, etc.

**Los datos se guardan de forma segura y permanente**

## 🔍 Verificar Registros

Si tienes problemas, puedes ver todos los registros en:
`verificar-registros.php`

## ✅ Importante

- ✔️ **Los correos se guardan en minúsculas** - No importa si escribes MAYUSCULAS o minúsculas
- ✔️ **Las contraseñas se encriptan** - Nadie puede verlas en texto plano
- ✔️ **Los datos son permanentes** - Incluso si cierras sesión, tu cuenta seguirá ahí
- ✔️ **Un correo = Una cuenta** - No puedes registrar dos cuentas con el mismo correo
