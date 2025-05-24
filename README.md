<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 📰 Blog API - Laravel

Este proyecto es una **API RESTful de un blog** desarrollada con [Laravel](https://laravel.com/). Permite la gestión de publicaciones (posts), comentarios y likes, además de contar con autenticación mediante JWT.  
Ideal como base para aplicaciones tipo red social, portafolios, o blogs personales.

## ✨ Funcionalidades

- Registro y login de usuarios con autenticación JWT.
- CRUD completo de publicaciones.
- Agregar, editar y eliminar comentarios por post.
- Dar y quitar likes a publicaciones.
- Documentación automática generada con Swagger (OpenAPI).

## 🚀 Requisitos

- PHP >= 8.1
- Composer
- MySQL o MariaDB
- Laravel 10+
- Node.js y npm (opcional, solo si usas frontend integrado)
- Extensiones PHP recomendadas:
  - `pdo`
  - `mbstring`
  - `openssl`
  - `tokenizer`
  - `xml`
  - `bcmath`
  - `ctype`
  - `json`

## ⚙️ Instalación

1. Clona el repositorio:

```bash
git clone https://github.com/AngelJV128a/blog-backend-laravel
cd blog-backend
```
2. Instala las dependencias:

```bash
composer install
```

3. Copia el archivo de entorno

```bash
cp .env.example .env
```

4. Genera la clave de aplicacion
```bash	
php artisan key:generate
```

5. Configura tus credenciales de conexión a la base de datos

6. Ejecuta las migraciones 
```bash
php artisan migrate
```

7. Configura JWT en el archivo `.env`
```bash
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

8. Ejecuta el servidor de desarrollo

```bash
php artisan serve
```

9. Accede a la API en `http://localhost:8000/api/auth/login`

10. Genera la documentación de la API

```bash
php artisan openapi:generate
```

11. Accede a la documentación de la API en `http://localhost:8000/swagger`
