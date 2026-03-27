<?php

$codigos_error = [
    'db_connection' => [
        'titulo' => 'Error de Conexión',
        'desc' => 'No es posible establecer comunicación con el servidor de base de datos. Verifique el servicio MySQL.'
    ],
    '1062' => [
        'titulo' => 'Registro Duplicado',
        'desc' => 'El identificador (Código de Paciente) ingresado ya existe en la base de datos. Verifique el código.'
    ],
    
    'col_sexo' => [
        'titulo' => 'Valor no válido: Sexo',
        'desc' => 'El campo Sexo solo admite los valores: "Hombre" o "Mujer".'
    ],
    'col_fecha_nacimiento' => [
        'titulo' => 'Formato de Fecha Incorrecto',
        'desc' => 'La fecha de nacimiento no es válida o tiene un formato incorrecto.'
    ],

    'col_rai_diagnostico' => [
        'titulo' => 'Valor no válido: Estadio RAI',
        'desc' => 'El sistema de clasificación RAI solo admite: 0, 1, 2, 3, 4 o Desconocido.'
    ],
    'col_binet_diagnostico' => [
        'titulo' => 'Valor no válido: Estadio Binet',
        'desc' => 'El sistema de clasificación Binet solo admite: A, B, C o Desconocido.'
    ],

    'col_estado_igvh' => [
        'titulo' => 'Dato Biológico Incorrecto',
        'desc' => 'El estado IGVH debe ser: Mutado, No mutado o Desconocido.'
    ],
    'col_mutacion_tp53' => [
        'titulo' => 'Dato Genético Incorrecto',
        'desc' => 'La mutación TP53 solo admite valores binarios (Si/No) o Desconocido.'
    ],
    'col_esquema' => [
        'titulo' => 'Esquema de Tratamiento No Reconocido',
        'desc' => 'El protocolo seleccionado no se encuentra en la lista de esquemas autorizados (R-FC, Ibrutinib, Venetoclax, etc.).'
    ],
    
    'general' => [
        'titulo' => 'Excepción del Sistema',
        'desc' => 'Ha ocurrido una operación no válida. Contacte al administrador del sistema.'
    ]
];

$tipo = $_GET['error'] ?? 'general';
$columna = $_GET['col'] ?? ''; 

if (!empty($columna) && isset($codigos_error["col_" . $columna])) {
    $info = $codigos_error["col_" . $columna];
} elseif (isset($codigos_error[$tipo])) {
    $info = $codigos_error[$tipo];
} else {
    $info = $codigos_error['general'];
    if($tipo == 'sql_error') $info['desc'] .= " (Detalle técnico oculto por seguridad)";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notificación del Sistema</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; 
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #343a40;
        }
        .error-container {
            max-width: 600px;
            margin-top: 80px;
            background: #ffffff;
            border: 1px solid #dee2e6; 
            border-left: 5px solid #a71d2a; 
            border-radius: 4px; 
        }
        .card-header {
            background-color: #ffffff;
            border-bottom: 1px solid #eff2f5;
            padding: 20px 25px;
            font-weight: 600;
            color: #a71d2a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }
        .card-body {
            padding: 30px 25px;
        }
        .error-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 15px;
            color: #212529;
        }
        .error-desc {
            font-size: 0.95rem;
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 25px;
        }
        .btn-action {
            background-color: #5c636a;
            color: white;
            border-radius: 2px;
            padding: 8px 20px;
            font-size: 0.9rem;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-action:hover {
            background-color: #424649;
            color: white;
        }
        .footer-log {
            font-size: 0.75rem;
            color: #adb5bd;
            margin-top: 20px;
            border-top: 1px solid #f1f3f5;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container d-flex justify-content-center">
        <div class="error-container shadow-sm">
            <div class="card-header">
                Sistema de Gestión Clínica LLC
            </div>
            <div class="card-body">
                <div class="error-title"><?= htmlspecialchars($info['titulo']) ?></div>
                <div class="error-desc"><?= htmlspecialchars($info['desc']) ?></div>
                
                <a href="javascript:history.back()" class="btn-action">Regresar al Formulario</a>
                
                <?php if(isset($_GET['debug']) && $_GET['debug'] == 1): ?>
                    <div class="footer-log">Código: <?= htmlspecialchars($tipo) ?> | Ref: <?= htmlspecialchars($columna) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>