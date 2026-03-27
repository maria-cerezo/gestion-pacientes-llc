# Base de Datos Clínica LLC (Gestión de Pacientes)

Proyecto de diseño e implementación de una base de datos relacional para gestionar historiales clínicos de pacientes de hematología. 

El objetivo principal de este proyecto era modelar una base de datos robusta para almacenar pacientes, diagnósticos y registrar la evolución de sus tratamientos. Para que el cliente final pudiera usar la base de datos en su día a día, desarrollé también una interfaz web básica.

## Lo más destacado del proyecto
* **Modelo Relacional:** Diseño de tablas conectadas mediante claves foráneas con borrados y actualizaciones en cascada para mantener la integridad de los datos.
* **Automatización con Triggers:** Creación de disparadores (Triggers) en SQL. Por ejemplo, uno que averigua y asigna automáticamente el número de línea de tratamiento que le toca a un paciente al insertar un nuevo registro.
* **Vistas (Views) para exportación:** Vistas precalculadas en SQL para sacar datos limpios (calculando la edad exacta del paciente, por ejemplo) listos para exportar a programas de estadística.

## Interfaz Web
Para interactuar con la base de datos sin usar la consola, monté un sistema CRUD:
* Conexión a la base de datos usando **PHP**.
* Pantallas para dar de alta, editar y borrar registros de forma segura.
* Interfaz visual hecha con HTML y **Bootstrap 5** para que sea fácil de usar.

## Tecnologías utilizadas
* **Base de datos:** MariaDB / SQL (HeidiSQL).
* **Interfaz:** PHP, HTML, CSS (Bootstrap).

---
**Aviso de Privacidad:** *Este proyecto se hizo para resolver un problema en un entorno real. Sin embargo, por motivos de privacidad, este repositorio **SOLO contiene la estructura (código SQL y PHP)**. No hay ningún dato real de pacientes.*

**Nota de la autora sobre el desarrollo web:** Como estudiante de 2º año de Ingeniería del Software, mi conocimiento actual y el foco principal de este proyecto es el **diseño y modelado de Bases de Datos**. Para poder entregar una solución completa y usable al cliente, la interfaz gráfica (PHP/HTML) fue construida apoyándome en herramientas de Inteligencia Artificial (IA).
