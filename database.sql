-- Base de datos: ani
CREATE DATABASE IF NOT EXISTS ani;
USE ani;

CREATE TABLE IF NOT EXISTS especialidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS terapeutas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    especialidad_id INT,
    FOREIGN KEY (especialidad_id) REFERENCES especialidades(id)
);

CREATE TABLE IF NOT EXISTS pacientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100)
);

CREATE TABLE IF NOT EXISTS secuencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT
);

CREATE TABLE IF NOT EXISTS secuencia_terapeutas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    secuencia_id INT NOT NULL,
    terapeuta_id INT NOT NULL,
    orden INT NOT NULL,
    FOREIGN KEY (secuencia_id) REFERENCES secuencias(id),
    FOREIGN KEY (terapeuta_id) REFERENCES terapeutas(id)
);

CREATE TABLE IF NOT EXISTS citas (
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

CREATE TABLE IF NOT EXISTS horarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL
);

CREATE TABLE IF NOT EXISTS configuracion (
    clave VARCHAR(50) PRIMARY KEY,
    valor VARCHAR(100) NOT NULL
);

-- Insertar valores por defecto
INSERT INTO configuracion (clave, valor) VALUES 
('horaInicio', '08:00'),
('horaFin', '18:00'),
('minTime', '15'); 