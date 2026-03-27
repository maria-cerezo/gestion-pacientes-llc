-- VISTAS

-- vista 1: paciente + diagnostico + antecedentes

CREATE OR REPLACE VIEW Vista_Exportacion_SPSS_Basal AS
SELECT 
    p.codigo AS ID_Paciente,
    p.sexo,
    TIMESTAMPDIFF(YEAR, p.fecha_nacimiento, CURDATE()) AS Edad_Actual,
    
    d.fecha_diagnostico,
    d.rai_diagnostico,
    d.binet_diagnostico,
    
    IF(a.hta = 1, 'Si', 'No') AS HTA,
    IF(a.dm = 1, 'Si', 'No') AS Diabetes,
    a.anticoagulacion
    
FROM Paciente p
LEFT JOIN Diagnostico d ON p.codigo = d.id_paciente
LEFT JOIN Antecedentes_Personales a ON p.codigo = a.id_paciente; 


-- vista 2: evolución  de los tratamientos

CREATE OR REPLACE VIEW Vista_Tratamientos_Resumen AS
SELECT 
    t.id_paciente,
    t.num_linea,
    IF(t.esquema = 'Otro', CONCAT('Otro: ', t.esquema_op_otro), t.esquema) AS Esquema_Real,
    t.fecha_inicio,
    t.respuesta,
    t.emr_sp AS EMR_Sangre, 
    t.emr_mo AS EMR_Medula
FROM Tratamiento t
ORDER BY t.id_paciente, t.num_linea;