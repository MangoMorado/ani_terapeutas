# ANI Terapeutas

Sistema de Agendamiento Terapéutico

## Descripción

ANI es un sistema web para agendar citas terapéuticas entre pacientes y terapeutas, permitiendo secuencias de sesiones, gestión de horarios, especialidades y terapeutas, con reglas avanzadas de disponibilidad y sin tiempos muertos entre sesiones.

## Características principales
- Agenda visual tipo calendario por día y terapeuta
- Secuencias de sesiones (Psicólogo → Fisioterapeuta → ...)
- Sin tiempos muertos entre sesiones de un paciente
- Gestión de pacientes, terapeutas y especialidades
- Configuración flexible de horarios y duración base de sesión
- Modal para ver detalles de paciente y su secuencia de citas

## Requisitos
- PHP 7.4+
- MySQL
- Servidor web (Apache recomendado)

## Estructura de carpetas
- `/api/` — Endpoints REST (PHP)
- `/controllers/` — Controladores PHP
- `/models/` — Modelos PHP
- `/views/` — Vistas HTML
- `/js/` — Lógica frontend JS
- `/css/` — Estilos CSS
- `/config/` — Configuración de base de datos

## Configuración inicial
1. Importa el archivo `database.sql` en tu base de datos MySQL.
2. Configura los datos de acceso en `config/database.php`.
3. Accede a `/views/index.html` desde tu navegador.

## Configuración de la duración base de sesión
- Ve al botón **Configurar Horarios**.
- Elige la hora de inicio, hora de fin y la duración base de cada sesión (en minutos).
- El sistema generará automáticamente los slots y guardará la duración base en la configuración.
- Todas las secuencias y opciones de duración usarán este valor como base.

## API de configuración
- `GET /api/configuracion.php?clave=duracion_sesion` — Obtiene la duración base de la sesión.
- `POST /api/configuracion.php` con `{ "clave": "duracion_sesion", "valor": 15 }` — Actualiza la duración base.

## Licencia
Proyecto privado para ANI IPS.
