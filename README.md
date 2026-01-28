# APLICACION WEB DE REGISTRO Y GESTION DE AFILIADOS

Aplicación web desarrollada en **PHP, JavaScript y HTML** para el **registro de afiliados** y la **organización eficiente de la información**.  
El sistema permite administrar datos mediante formularios, vistas dinámicas y conexión a una base de datos, facilitando el control y mantenimiento de la información.

Este proyecto está orientado a **fines académicos y administrativos**, aplicando buenas prácticas de desarrollo web y manejo de datos.

---

## 📌 Descripcion General

La aplicación funciona bajo una arquitectura **cliente-servidor**, donde:
- El usuario interactúa con formularios web.
- PHP procesa la lógica del sistema.
- JavaScript valida y mejora la experiencia del usuario.
- Los datos se almacenan en una base de datos MySQL.

---

## 🧱 Tecnologias Utilizadas

### Backend
- **PHP**  
  Manejo de lógica, procesamiento de formularios y conexión a la base de datos.

### Frontend
- **HTML** – Estructura de las vistas  
- **CSS** – Diseño y estilos  
- **JavaScript** – Validaciones y comportamiento dinámico  

### Base de Datos
- **MySQL / MariaDB**

### Herramientas
- **XAMPP** (Apache + MySQL)
- **Git** y **GitHub**
- **Visual Studio Code**

---

## 📂 Estructura del Proyecto

```text
/
├── .vscode/              # Configuracion del editor
│
├── bd/                   # Archivos relacionados con la base de datos
│
├── config/               # Configuracion del sistema
│
├── img/                  # Imagenes del sistema
│
├── js/                   # Scripts JavaScript
│
├── src/                  # Archivos fuente y logica principal
│
├── style/                # Hojas de estilo CSS
│
├── uploads/              # Archivos subidos por el usuario
│
├── curp.php               # Validacion y manejo de CURP
├── dashboard.php          # Panel principal del sistema
├── guardar.php            # Registro de nuevos afiliados
├── guardar_edicion.php    # Actualizacion de registros
├── index.php              # Pagina principal
├── logout.php             # Cierre de sesion
├── vista.php              # Vista y consulta de afiliados
│
├── README.md
└── .gitignore
