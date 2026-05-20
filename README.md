# Booking Template (PHP + HTML + CSS)

Proyecto listo para poner en **htdocs** (XAMPP/MAMP/WAMP) y abrir en VS Code.

## Estructura del Proyecto

El proyecto utiliza el patrón de arquitectura **MVC (Modelo-Vista-Controlador)** para separar las responsabilidades de la aplicación:

- **`Model` (Modelo):** Se encarga de gestionar los datos, realizar consultas, modificar e interactuar directamente con la base de datos.
- **`View` (Vista):** Se encarga de la interfaz visual y de todo aquello con lo que interactúa el usuario de forma directa (Frontend).
- **`Controller` (Controlador):** Actúa como intermediario entre la Vista y el Modelo, gestionando la lógica del negocio y la funcionalidad de la página (Backend).

## Cómo correrlo (XAMPP)
1. Copia la carpeta `booking_template_php` dentro de:
   - Windows: `C:\xampp\htdocs\`
   - macOS (MAMP): `Applications/MAMP/htdocs/` (depende de tu setup)
2. Enciende Apache.
3. Abre en el browser:
   - `http://localhost/proyecto_final/index_usuario.php`

## Notas de Configuración

- **Entorno de desarrollo:** Para efectos de pruebas y preparación académica, el proyecto se configuró inicialmente utilizando una base de datos en la nube (**Aiven Cloud**).
- **Requisito para despliegue:** Para que el proyecto funcione de manera local o independiente, deberá crear su propia base de datos y actualizar los archivos de configuración correspondientes para establecer la nueva conexión.
 
