# Lógica de Funcionamiento de Secuencias de Citas

## Índice
1. [Introducción](#introducción)
2. [Estructura de Datos](#estructura-de-datos)
3. [Flujo de Trabajo](#flujo-de-trabajo)
4. [Componentes del Sistema](#componentes-del-sistema)
5. [Ejemplos de Uso](#ejemplos-de-uso)
6. [Consideraciones Técnicas](#consideraciones-técnicas)

## Introducción

El sistema de secuencias de citas permite agendar múltiples citas consecutivas con diferentes terapeutas para un paciente en un mismo día. Este documento explica la lógica de funcionamiento y los componentes involucrados.

## Estructura de Datos

### Tablas Principales

#### 1. Secuencias
```sql
CREATE TABLE secuencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);
```
- Almacena las definiciones base de las secuencias
- Cada secuencia tiene un nombre y una descripción opcional

#### 2. Secuencia Terapeutas
```sql
CREATE TABLE secuencia_terapeutas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    secuencia_id INT NOT NULL,
    terapeuta_id INT NOT NULL,
    orden INT NOT NULL,
    FOREIGN KEY (secuencia_id) REFERENCES secuencias(id),
    FOREIGN KEY (terapeuta_id) REFERENCES terapeutas(id)
);
```
- Define el orden de los terapeutas en una secuencia
- Mantiene la relación entre secuencias y terapeutas
- El campo `orden` determina la secuencia de atención

#### 3. Citas
```sql
CREATE TABLE citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    paciente_id INT NOT NULL,
    terapeuta_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL,
    secuencia_id INT,
    orden_en_secuencia INT,
    estado VARCHAR(20) DEFAULT 'agendada',
    FOREIGN KEY (paciente_id) REFERENCES pacientes(id),
    FOREIGN KEY (terapeuta_id) REFERENCES terapeutas(id),
    FOREIGN KEY (secuencia_id) REFERENCES secuencias(id)
);
```
- Almacena las citas individuales
- Campos clave para secuencias:
  - `secuencia_id`: ID de la secuencia a la que pertenece
  - `orden_en_secuencia`: Orden dentro de la secuencia
  - `estado`: Estado de la cita (agendada, completada, etc.)

## Flujo de Trabajo

### 1. Definición de Secuencia
1. Se crea una secuencia con nombre y descripción
2. Se asignan terapeutas en un orden específico
3. Cada terapeuta puede tener una duración diferente

### 2. Proceso de Agendamiento

#### Fase de Sugerencia
1. El sistema recibe:
   - ID del paciente
   - Fecha deseada
   - Lista de pasos (terapeutas y duraciones)
2. El sistema:
   - Verifica disponibilidad de horarios
   - Busca bloques consecutivos disponibles
   - Genera opciones de horarios válidos
3. Se presentan las opciones al usuario

#### Fase de Agendamiento
1. Usuario selecciona una opción de horario
2. Sistema crea las citas en orden secuencial
3. Cada cita se vincula a la secuencia
4. Se mantiene el orden mediante `orden_en_secuencia`

### 3. Validaciones
- Verificación de disponibilidad de terapeutas
- Comprobación de horarios consecutivos
- Validación de duraciones
- Verificación de no solapamiento de citas

## Componentes del Sistema

### 1. Backend (PHP)

#### Modelos
- `Secuencia.php`: Gestión de secuencias
- `Cita.php`: Manejo de citas individuales
- `Terapeuta.php`: Información de terapeutas

#### Controladores
- `SecuenciaController.php`: Lógica de secuencias
- `AgendarController.php`: Gestión de agendamiento

#### API Endpoints
- `/api/secuencias.php`: CRUD de secuencias
- `/api/agendar_secuencia.php`: Agendamiento de secuencias
- `/api/agendar.php`: Gestión de citas individuales

### 2. Frontend (JavaScript)

#### Funciones Principales
- `agregarPasoSecuencia()`: Añade pasos a la secuencia
- `mostrarSugerenciasHorarios()`: Muestra opciones de horarios
- `agendarSecuencia()`: Procesa el agendamiento
- `cargarAgenda()`: Visualización de citas

## Ejemplos de Uso

### 1. Crear una Secuencia Simple
```javascript
// Ejemplo de creación de secuencia
const secuencia = {
    pasos: [
        { terapeuta_id: 1, duracion: 30 }, // Fisioterapeuta
        { terapeuta_id: 2, duracion: 45 }  // Psicólogo
    ]
};
```

### 2. Agendamiento de Secuencia
```javascript
// Ejemplo de agendamiento
const agendamiento = {
    paciente_id: 123,
    fecha: "2024-03-20",
    pasos: secuencia.pasos,
    accion: "sugerir"  // o "agendar"
};
```

## Consideraciones Técnicas

### 1. Optimizaciones
- Búsqueda eficiente de horarios disponibles
- Validación de solapamientos
- Manejo de transacciones en base de datos

### 2. Limitaciones
- Horarios deben ser consecutivos
- Duración mínima por cita
- Disponibilidad de terapeutas

### 3. Mejores Prácticas
- Validación de datos en frontend y backend
- Manejo de errores y excepciones
- Registro de operaciones
- Confirmación de agendamiento

### 4. Seguridad
- Validación de permisos
- Sanitización de datos
- Protección contra solapamientos
- Verificación de disponibilidad en tiempo real

---

Este documento proporciona una visión general del sistema de secuencias de citas. Para implementaciones específicas o detalles técnicos adicionales, consulte la documentación del código fuente. 