-- TRIGGERS

delimiter //

-- Trigger 1: cuando el programa mande a guardar un tratamiento, puede mandar num_linea vacío o 0 y 
-- la base de datos lo rellenará con el número correcto

CREATE TRIGGER before_insert_tratamiento
BEFORE INSERT ON Tratamiento
FOR EACH ROW
BEGIN 
	DECLARE max_linea INT;
	SELECT MAX(num_linea) INTO max_linea
	FROM Tratamiento 
	WHERE id_paciente = NEW.id_paciente;
	
	if max_linea IS NULL then 
		SET NEW.num_linea = 1;
	ELSE 
		SET NEW.num_linea = max_linea + 1;
	END if;
END //

delimiter ;
	
	
