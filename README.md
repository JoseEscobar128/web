# Mesa Fácil — Módulo 1: Autenticación con 2FA (Laravel 11)

Bienvenido al repositorio del **Módulo 1** del sistema **Mesa Fácil**, un software escalable de gestión para negocios de comida.  
Este módulo implementa el sistema de **registro, inicio de sesión y verificación en dos pasos (2FA)** usando Laravel 11 y Laravel Sanctum.

---

## ✨ Características principales

- Registro de usuarios con validaciones fuertes.
- Login con verificación de correo vía código 2FA.
- Reenvío de código 2FA.
- Logout seguro con eliminación del token de acceso.
- Código limpio, documentado y basado en estándares de respuesta HTTP.
- Envío de correos con [Resend](https://resend.com/).

---

## 📦 Requisitos del entorno

Para correr este proyecto necesitas tener instalado:

- **PHP >= 8.2**
- **Composer >= 2.5**
- **Laravel 11**
- **MySQL**


- **Servidor de correo o cuenta en Resend**

---

## ⚙️ Instalación del proyecto

1. **Clonar el repositorio**
   git clone https://github.com/tuusuario/mesa-facil.git
   cd mesa-facil
Instalar dependencias
composer install
Copiar el archivo .env y configurar variables
cp .env.example .env



Luego edita .env y configura:

1. Conexión a la base de datos
2. Credenciales de correo (por ejemplo, Resend)
3. Nombre de la aplicación y otros datos relevantes
4. Generar la key de la aplicación
- php artisan key:generate
5. Migrar las tablas necesarias
- php artisan migrate
6. (Opcional) Publicar archivos de Laravel Sanctum
- php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
7. Levantar el servidor de desarrollo
- php artisan serve


🔐 Endpoints principales

Método	Ruta	Descripción
POST	/api/register	Registrar nuevo usuario
POST	/api/login	Iniciar sesión (envía 2FA)
POST	/api/verify-2fa	Verificar código 2FA
POST	/api/resend-2fa	Reenviar código 2FA
POST	/api/logout	Cerrar sesión (token Sanctum)
