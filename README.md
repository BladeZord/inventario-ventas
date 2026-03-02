# Sistema de Inventario y Ventas

## Descripción de la aplicación

Aplicación web para la **gestión de inventario y ventas**. Permite administrar:

- **Categorías** de productos (CRUD).
- **Unidades de medida** (CRUD).
- **Productos** con categoría, unidad de medida, stock y precios (CRUD).
- **Clientes** (CRUD).
- **Registro de ventas**: elegir cliente, agregar líneas de detalle (producto, cantidad, precio, descuento), registrar la venta, consultar ventas realizadas y anular ventas.

La interfaz incluye botón **Volver** en las vistas para retroceder a la pantalla anterior y **paginación** en todos los listados (productos, categorías, clientes, unidades de medida y ventas).

---

## Declaración de uso de IA

Este proyecto ha sido desarrollado con asistencia de **herramientas de inteligencia artificial** (IA) para tareas de correccion puntual, optimización de codigo existente, documentación y revisión. La arquitectura, decisiones de diseño y requisitos funcionales son responsabilidad del autor.

---

## Enlaces

| Recurso        | Enlace |
|----------------|--------|
| **Repositorio** | [https://github.com/BladeZord/inventario-ventas](https://github.com/BladeZord/inventario-ventas) |
| **LinkedIn**   | [www.linkedin.com/in/kevin-quito-23881824b](https://www.linkedin.com/in/kevin-quito-23881824b) |
| **Hoja de vida** | [https://github.com/BladeZord/Hoja-de-vida](https://github.com/BladeZord/Hoja-de-vida) |

---

## Requisitos
- **Node JS** 20.x superior
- **PHP** 8.0 o superior (con extensión PDO MySQL).
- **MySQL** 8.x (o MariaDB compatible).
- **Composer** (para dependencias PHP).
- Navegador web actualizado.

---

## Cómo levantar la aplicación

1. **Clonar el repositorio** (o descargar el código):
   ```bash
   git clone https://github.com/BladeZord/inventario-ventas.git
   cd inventario-ventas
   ```

2. **Crear la base de datos** en MySQL y, si lo deseas, un usuario con permisos sobre ella.
   - `app/database/01 Crear base de datos.sql`
3. **Importar el esquema y datos iniciales** (ajusta nombres de archivos si tu proyecto los tiene distintos):
   - Estructuras: `app/database/02 Crear estructuras.sql`
   - Datos iniciales (opcional): `app/database/03 datos iniciales.sql`

4. **Configurar el entorno**:
   - Copiar `.env.example` a `.env`.
   - Editar `.env` y configurar las variables de base de datos, por ejemplo:
     ```env
     DB_HOST=localhost
     DB_PORT=3306
     DB_NAME=inventario_ventas
     DB_USERNAME=tu_usuario
     DB_PASSWORD=tu_contraseña
     ```

5. **Instalar dependencias con Composer**:
   ```bash
   composer install
   ```
Se usó composer para el uso de namespaces e imports automáticos \n
6. **Arrancar el servidor PHP** (desde la raíz del proyecto):
   ```bash
   php -S localhost:8000 -t public
   ```

7. **Abrir en el navegador**: [http://localhost:8000](http://localhost:8000)

---

## Estructura del proyecto

- `app/` — Lógica de backend (modelos, servicios, controladores, base de datos).
- `public/` — Punto de entrada (`index.php`), vistas HTML, CSS y JavaScript (SPA con hash routing).
- `.env` — Configuración local (no se sube al repositorio).
- `.env.example` — Plantilla de variables de entorno.


Nota: El backend puede ejecutarse de manera independiente mediante la interacción con POSTMAN