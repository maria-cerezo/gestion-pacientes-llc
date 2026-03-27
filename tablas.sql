DROP DATABASE if EXISTS BaseDatos_LLC;
CREATE DATABASE BaseDatos_LLC
CHARACTER SET UTF8MB4  -- me deja meter cualquier idioma y caracteres raros como emojis etc...
COLLATE UTF8MB4_UNICODE_CI;  -- el ci hace que cuando el cliente busque, el programa sea insensible a las mayusc y minusc
 
USE BaseDatos_LLC;

-- TABLA PACIENTES

CREATE TABLE Paciente(
	codigo VARCHAR(20) NOT NULL COMMENT 'Código único del paciente',
	iniciales VARCHAR(10) COMMENT 'Iniciales',
	fecha_nacimiento DATE NOT NULL,
	sexo ENUM('Hombre', 'Mujer') NOT NULL,
	fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (codigo)
)  ENGINE=INNODB;  


-- TABLA DIAGNOSTICO

CREATE TABLE Diagnostico(
	id_diagnostico INT AUTO_INCREMENT PRIMARY KEY,
	id_paciente VARCHAR(20) NOT NULL, 
	fecha_diagnostico DATE NOT NULL,
	-- estadios diagnóstico
	rai_diagnostico ENUM('0', '1', '2', '3', '4', 'Desconocido') DEFAULT NULL,
	binet_diagnostico ENUM('A', 'B', 'C', 'Desconocido') DEFAULT NULL,
	FOREIGN KEY (id_paciente) REFERENCES Paciente(codigo)
		ON DELETE CASCADE 
		ON UPDATE CASCADE 
) ENGINE = INNODB;

-- TABLA ANTECEDENTES PERSONALES


CREATE TABLE Antecedentes_Personales(
	id_antecedentes INT AUTO_INCREMENT PRIMARY KEY,
	id_paciente VARCHAR(20) NOT NULL,
	hta TINYINT(1) DEFAULT 0, 
	dm TINYINT(1) DEFAULT 0,
	fa TINYINT(1) DEFAULT 0,
	dl TINYINT(1) DEFAULT 0,
	valvulopatia_cardiaca TINYINT(1) DEFAULT 0,  -- 0 es NO, 1 es SI
	valvulopatia_renal_cronica TINYINT(1) DEFAULT 0,
	valvulopatia_hepatica TINYINT(1) DEFAULT 0,
	medicacion_concomitante TINYINT(1) DEFAULT 0,
	anticoagulacion ENUM(
	'Ninguna',
	'Antivitamina K',
	'Apixaban',
	'Rivaroxaban',
	'Dabigatran',
	'Edoxaban',
	'HBPM'
	) DEFAULT 'Ninguna',
	antiagregacion TINYINT(1) DEFAULT 0,
	FOREIGN KEY (id_paciente) REFERENCES Paciente(codigo)
		ON DELETE CASCADE 
		ON UPDATE CASCADE 
) ENGINE = INNODB;

CREATE TABLE Tratamiento (
	id_tratamiento INT AUTO_INCREMENT PRIMARY KEY,
	id_paciente VARCHAR(20) NOT NULL,
	num_linea TINYINT UNSIGNED NOT NULL,
	
	-- datos previos al tratamiento
	
	escala_cirs TINYINT UNSIGNED DEFAULT NULL COMMENT 'Puntuación 0-56',
	escala_cll_ipi_valor TINYINT UNSIGNED DEFAULT NULL COMMENT 'Valor numérico',
	escala_cll_ipi_riesgo ENUM (
		'Bajo Riesgo(0-1)',
		'Riesgo Intermedio (2-3)',
		'Alto Riesgo (<04)',
		'Muy Alto') DEFAULT NULL,
		
	linfocitos DECIMAL(10, 2) DEFAULT NULL, 
	leucocitos DECIMAL(10, 2) DEFAULT NULL, 
	plaquetas DECIMAL(10, 2) DEFAULT NULL, 
	neutrofilos DECIMAL(10, 2) DEFAULT NULL,
	creatinina DECIMAL(5,2) DEFAULT NULL,
	
	cariotipo_complejo TINYINT(1) DEFAULT 0,
	otra_analitica VARCHAR(100) DEFAULT NULL,
	
	estado_igvh ENUM('Mutado', 'No mutado', 'Desconocido') DEFAULT 'Desconocido',
	fish_del17p TINYINT(1) DEFAULT 0,
	fish_del11q TINYINT(1) DEFAULT 0,
	fish_trisomia12 TINYINT(1) DEFAULT 0,
	fish_del13q TINYINT(1) DEFAULT 0,
	mutacion_tp53 ENUM('Si', 'No', 'Desconocido') DEFAULT 'Desconocido',
	
	fecha_inicio DATE DEFAULT NULL,
	fecha_fin DATE DEFAULT NULL,
	indicacion_gellc TEXT DEFAULT NULL,
	
	esquema ENUM(
	'R-FC',
	'R-Bendamustina',
	'O-Clb',
	'R-Clb',
	'Clorambucilo en monoterapia',
	'O-venetoclax',
	'Ibrutinib',
	'I+V',
	'R-Ibrutinib',
	'Acalabrutinib',
	'Zanubrutinib',
	'A+V',
	'RFC retratamiento',
	'Ciclofosfamida',
	'R-idelalisib',
	'Acalabtinib',
	'R-Venetoclax',
	'Pirtobrutinib',
	'Otro'
	) DEFAULT NULL,
	esquema_op_otro VARCHAR(100) DEFAULT NULL COMMENT 'Rellenar solo si esquema es Otro',
	tipo_duracion ENUM('Finito', 'Indefinido') DEFAULT NULL, 
	respuesta ENUM('RC (Respuesta completa)', 'RCi (Respuesta completa incompleta)', 'RP (Respuesta parcial)', 'RP-linfocitosis', 'EE (Enfermedad estable)', 'E.Progresiva') DEFAULT NULL,
	emr_sp ENUM('No realizada', 'Positiva', 'Negativa') DEFAULT 'No realizada',
	emr_mo ENUM('No realizada', 'Positiva', 'Negativa') DEFAULT 'No realizada',
	FOREIGN KEY (id_paciente) REFERENCES Paciente(codigo)
		ON DELETE CASCADE ON UPDATE CASCADE, 
	INDEX idx_paciente_linea (id_paciente, num_linea) -- creo indice compuesto por el paciente y el numero de linea
) ENGINE = INNODB; 








