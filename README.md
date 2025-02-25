# Gestión de Tareas con Laravel

¡Bienvenido al proyecto de gestión de tareas desarrollado como parte de la prueba técnica de NovaCrew! Este proyecto es una API REST construida con Laravel 8.1 que permite a los usuarios gestionar tareas de manera eficiente, con autenticación mediante tokens y paginación de resultados.

---

## Características principales

- **Autenticación segura**: Uso de Laravel Sanctum para autenticación mediante tokens.
- **Gestión de tareas**: Crear, leer, actualizar y eliminar tareas (CRUD).
- **Paginación**: Listado de tareas paginado (10 tareas por página).
- **Validaciones**: Validaciones robustas en los endpoints para garantizar la integridad de los datos.
- **Relaciones**: Uso de Eloquent ORM para manejar las relaciones entre usuarios y tareas.
- **Extras**:
  - Filtrado de tareas por estado (`estado=pending`).
  - Ordenación de tareas por fecha de vencimiento (`sort_by=fecha_vencimiento&order=asc`).
  - Notificación por correo electrónico cuando una tarea cambia de estado.

---

## Requisitos del sistema

- PHP >= 7.3
- Composer
- SQL Server (o cualquier base de datos compatible con Laravel)
- Laravel 8.1

---

## Instalación

Sigue estos pasos para configurar el proyecto en tu entorno local:

1. **Clona el repositorio**:
   ```bash
   git clone https://github.com/tu-usuario/tu-repositorio.git
   cd tu-repositorio
