-- procedures

-- procedure 1: insertar paciente nuevo
delimiter //
CREATE PROCEDURE Nuevo_paciente(
	IN p_codigo VARCHAR(20),
	IN p_iniciales VARCHAR(10),
	IN p_fecha_nac DATE,
	IN p_sexo VARCHAR(10)
)

BEGIN 
	INSERT INTO Paciente (codigo, iniciales, fecha_nacimiento, sexo)
	VALUES (p_codigo, p_iniciales, p_fecha_nac, p_sexo);
END//

delimiter ;

-- procedure 2: insertar tratamiento 

delimiter //

CREATE PROCEDURE Nuevo_tratamiento(

	IN p_id_paciente VARCHAR(20),
	IN p_esquema VARCHAR(50),
	IN p_esquema_otro VARCHAR(100),
	IN p_fecha_inicio DATE,
	IN p_tipo_duracion VARCHAR(20)
	
)
BEGIN 
	if p_esquema != 'Otro' then 
		SET p_esquema_otro = NULL;
	END if;
	
	INSERT INTO Tratamiento (id_paciente, esquema, esquema_op_otro, fecha_inicio, tipo_duracion)
	VALUES (p_id_paciente, p_esquema, p_esquema_otro, p_fecha_inicio, p_tipo_duracion);
END // 

delimiter ;

