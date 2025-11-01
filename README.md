# 🐳 DockerLabs — Nueva Actualización 2025

Bienvenido a la nueva versión en desarrollo de **DockerLabs**, una plataforma pensada para el aprendizaje y la práctica en ciberseguridad, hacking ético y DevOps.  
Este repositorio contiene la base de la aplicación en **Laravel**, y actualmente estamos trabajando en una gran actualización con novedades que harán la experiencia mucho más completa y moderna.

---

## 🚀 Novedades de la actualización

### 🎨 Interfaz y experiencia de usuario
- **Nuevo diseño azul moderno** con animaciones suaves y micro-interacciones.  
- **Header y footer fijos** con navegación mejorada.  
- Estilos más accesibles y responsive para todos los dispositivos.  
- Formularios y tablas renovadas con mejor contraste y enfoque en la usabilidad.  

### 👤 Sistema de usuarios
- Se incorpora un **menú de Perfil** en la navegación:  
  - Ver y editar información personal (nombre, email).  
  - Cambiar contraseña con validación segura.  
- Eliminación del **antiguo Dashboard**: ahora el acceso principal tras iniciar sesión será al perfil o a la home.  

### 📚 Gestión de contenido
- Sección **Enviar máquina** actualizada para subir y gestionar laboratorios.  
- Listados de máquinas con un estilo tipo “tiras horizontales” con badges de dificultad.  
- Modales de descripción con diseño moderno y soporte responsive.  

### 🔒 Seguridad y backend
- Validaciones más robustas en formularios.  
- Limpieza de rutas y controladores obsoletos (se eliminó la lógica de Dashboard).  
- Uso de middleware `auth` para proteger las secciones sensibles.  

### 🛠️ Roadmap
- Implementación futura de **estadísticas personalizadas** por usuario.  
- Posibilidad de integrar **Writeups verificados** enlazados a cada máquina.  
- Extensión de la API para que la comunidad pueda consumir datos de las máquinas.  

---

## 📦 Tecnologías utilizadas
- [Laravel](https://laravel.com/) (PHP) — para la lógica principal y las vistas.  
- [FastAPI](https://fastapi.tiangolo.com/) (Python) — endpoints y servicios extra.  
- [SQLite](https://www.sqlite.org/) — base de datos ligera para desarrollo.  
- [Tailwind / CSS Custom](https://tailwindcss.com/) (estilos adaptados).  

---

## 📝 Cómo contribuir
1. Haz un fork del proyecto.  
2. Crea una nueva rama para tu funcionalidad:  
   ```bash
   git checkout -b feature/nueva-funcionalidad
