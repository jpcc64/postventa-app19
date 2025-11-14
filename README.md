<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="#"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="#"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="#"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="#"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

Acerca de la Aplicación de Postventa
Esta aplicación es un sistema de gestión de postventa desarrollado con el framework Laravel 10. Su objetivo principal es permitir a los empleados del departamento de postventa crear, consultar, modificar y gestionar partes de servicio (llamadas de servicio), interactuando en tiempo real con un sistema SAP externo a través de una API.

La aplicación facilita las operaciones diarias del departamento de postventa, centralizando la gestión de incidencias de clientes y proveedores y asegurando que los datos se mantengan consistentes con el sistema SAP.

Características Principales
Gestión de Partes de Servicio: Creación y modificación de partes de trabajo.

Integración con SAP: Todas las operaciones de negocio (clientes, productos, partes) se realizan en tiempo real contra una API de SAP.

Búsqueda Avanzada: Permite buscar partes por múltiples criterios como código de cliente, nombre, RMA, etc.

Autenticación de Usuarios: Sistema de login para empleados basado en una base de datos local.

Notificaciones a Clientes: Funcionalidad para enviar avisos por WhatsApp cuando un producto está listo.

Generación de Documentos: Creación de una vista de impresión para los partes de servicio.

Especificaciones Técnicas
Framework: Laravel 10

Lenguaje: PHP 8.1+

Base de Datos (para usuarios): MySQL

Frontend: Tailwind CSS, Alpine.js (vía CDN).

Dependencias Clave:

guzzlehttp/guzzle: Cliente HTTP para las peticiones a la API de SAP.

barryvdh/laravel-dompdf: Para la generación de vistas de impresión.

Instalación y Configuración
Siga los siguientes pasos para instalar el proyecto en un entorno de desarrollo.

Prerrequisitos
Servidor web compatible con PHP 8.1+ (Apache, Nginx)

Composer

Base de datos MySQL

Pasos de Instalación
Clonar el repositorio:

git clone <url-del-repositorio>
cd <directorio-del-proyecto>

Instalar dependencias:

composer install

Configurar el entorno:

Cree el archivo .env a partir del ejemplo: cp .env.example .env

Genere la clave de la aplicación: php artisan key:generate

Configurar .env:

Ajuste las credenciales de la base de datos para la conexión mysql_usuario.

Asegúrese de que la URL de la API de SAP sea correcta y accesible.

Ejecutar las migraciones:

php artisan migrate

Esto creará las tablas necesarias para la autenticación de usuarios.

Funcionamiento
Autenticación
El sistema utiliza el gestor de autenticación de Laravel. Los usuarios se gestionan en una tabla users en la base de datos MySQL local. El controlador App\Http\Controllers\Login se encarga de la lógica de inicio y cierre de sesión.

Interacción con la API de SAP
La aplicación no tiene una base de datos de negocio propia. Toda la información de clientes, productos y partes se obtiene y modifica a través de peticiones a una API externa de SAP.

Controlador Principal: App\Http\Controllers\ParteController.

Método: Peticiones POST con un payload JSON (Http::asForm()->post(...)).

Endpoint: La URL de la API está actualmente codificada en los controladores.


## Guía de Uso

Esta guía describe el flujo de trabajo principal para gestionar partes de servicio en la aplicación.

### 1. Búsqueda de Clientes y Partes

-   **Página Principal:** Al iniciar sesión, accederá a la página principal donde se encuentra el buscador.
-   **Buscar un Cliente:** Utilice el campo de búsqueda principal para encontrar un cliente por su nombre, CIF o número de teléfono. Al hacer clic en "Buscar", la aplicación consultará la base de datos de SAP y mostrará la información del cliente y una lista de sus partes de servicio existentes.
-   **Seleccionar un Parte Existente:** Si el cliente tiene varios partes, se mostrarán en tarjetas. Puede usar el filtro para encontrar un parte específico por su número, fecha o descripción. Haga clic en "Seleccionar" en el parte deseado para cargar todos sus detalles en el formulario.

### 2. Creación de un Nuevo Parte

-   Si el cliente no tiene un parte para la incidencia actual, puede crear uno nuevo de dos maneras:
    1.  Después de buscar un cliente, aparecerá una tarjeta para "Crear nuevo parte".
    2.  Si ya tiene un parte cargado, puede usar el botón "Nuevo parte" para iniciar un nuevo formulario para el mismo cliente.
-   Complete todos los campos requeridos en el formulario, como el artículo, el motivo de la llamada y la descripción del problema.
-   Haga clic en "Guardar" para enviar la información a SAP y crear el nuevo parte de servicio.

### 3. Modificación de un Parte Existente

-   Una vez que un parte está cargado en el formulario, puede modificar cualquiera de sus campos.
-   Después de realizar los cambios, haga clic en "Guardar". La aplicación enviará una petición a SAP para actualizar la información.

### 4. Reabrir un Parte Cerrado

-   Cuando se carga un parte con estado **Cerrado**, todos los campos del formulario aparecerán deshabilitados para prevenir modificaciones accidentales.
-   **Para reabrir el parte:**
    1.  Diríjase al campo **"Estado"**.
    2.  Cámbielo de "Cerrado" a "Abierto" o a cualquier otro estado.
    3.  Automáticamente, todos los demás campos del formulario se habilitarán, permitiéndole realizar cambios.
    4.  Haga clic en "Guardar" para aplicar el nuevo estado y cualquier otra modificación.

### 5. Acciones Adicionales

-   **Imprimir Parte:** Con un parte cargado, puede usar el botón "Imprimir Parte" para generar una vista lista para imprimir con el resumen del servicio.
-   **Avisar a Cliente:** Utilice el botón "Avisar por WhatsApp" para enviar una notificación al cliente.

Contribuciones
Gracias por considerar contribuir a este proyecto. Actualmente, el desarrollo es interno.

Vulnerabilidades de Seguridad
Si descubre una vulnerabilidad de seguridad dentro de esta aplicación, por favor envíe un correo electrónico al responsable del proyecto. Todas las vulnerabilidades de seguridad serán tratadas con prontitud.

Licencia
Este proyecto es un software propietario. Todos los derechos reservados.
