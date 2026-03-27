<?php
require 'db.php';

$id = $_GET['id'] ?? '';
if (!$id) die("ID de tratamiento no especificado.");


$stmt = $pdo->prepare("
    SELECT t.*, p.codigo as cod_paciente 
    FROM Tratamiento t 
    JOIN Paciente p ON t.id_paciente = p.codigo 
    WHERE t.id_tratamiento = ?
");
$stmt->execute([$id]);
$t = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$t) die("Tratamiento no encontrado.");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles del Tratamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .data-label { color: #6c757d; font-size: 0.85rem; text-transform: uppercase; font-weight: bold; margin-bottom: 0.2rem; display: block; }
        .data-value { font-size: 1.1rem; font-weight: 500; }
        .card-header { background-color: #ffffff; border-bottom: 2px solid #eee; }
    </style>
</head>
<body class="p-4">
    <div class="container" style="max-width: 800px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3><i class="bi bi-eye me-2"></i>Detalle de Tratamiento</h3>
            <a href="ver_paciente.php?codigo=<?= $t['id_paciente'] ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver al Paciente
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-header fw-bold text-primary">
                Línea <?= $t['num_linea'] ?> - <?= htmlspecialchars($t['cod_paciente']) ?>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    
                    <div class="col-12">
                        <h6 class="text-primary border-bottom pb-2 mb-3">1. Esquema Terapéutico</h6>
                    </div>

                    <div class="col-12">
                        <span class="data-label">Esquema / Fármaco</span>
                        <div class="p-3 bg-light border rounded">
                            <?= ($t['esquema'] == 'Otro') ? htmlspecialchars($t['esquema_op_otro']) : htmlspecialchars($t['esquema']) ?>
                        </div>
                    </div>

                    <div class="col-12">
                        <span class="data-label">Indicación GELLC</span>
                        <span class="data-value"><?= htmlspecialchars($t['indicacion_gellc'] ?? '-') ?></span>
                    </div>

                    <div class="col-md-6">
                        <span class="data-label">Fecha Inicio</span>
                        <span class="data-value"><?= $t['fecha_inicio'] ? date("d/m/Y", strtotime($t['fecha_inicio'])) : '-' ?></span>
                    </div>
                    <div class="col-md-6">
                        <span class="data-label">Fecha Fin</span>
                        <span class="data-value"><?= $t['fecha_fin'] ? date("d/m/Y", strtotime($t['fecha_fin'])) : 'En curso / No registrada' ?></span>
                    </div>

                    <div class="col-md-6">
                        <span class="data-label">Tipo de Duración</span>
                        <span class="data-value"><?= htmlspecialchars($t['tipo_duracion'] ?? '-') ?></span>
                    </div>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3">2. Analítica y Biología</h6>
                    </div>

                    <div class="col-md-2">
                        <span class="data-label">Linfocitos</span>
                        <span class="data-value"><?= htmlspecialchars($t['linfocitos'] ?? '-') ?></span>
                    </div>
                    <div class="col-md-2">
                        <span class="data-label">Leucocitos</span>
                        <span class="data-value"><?= htmlspecialchars($t['leucocitos'] ?? '-') ?></span>
                    </div>
                    <div class="col-md-2">
                        <span class="data-label">Plaquetas</span>
                        <span class="data-value"><?= htmlspecialchars($t['plaquetas'] ?? '-') ?></span>
                    </div>
                    <div class="col-md-2">
                        <span class="data-label">Neutrófilos</span>
                        <span class="data-value"><?= htmlspecialchars($t['neutrofilos'] ?? '-') ?></span>
                    </div>
                    <div class="col-md-2">
                        <span class="data-label">Creatinina</span>
                        <span class="data-value"><?= htmlspecialchars($t['creatinina'] ?? '-') ?></span>
                    </div>

                    <div class="col-12">
                        <span class="data-label">Otro</span>
                        <span class="data-value"><?= htmlspecialchars($t['otra_analitica'] ?? '-') ?></span>
                    </div>

                    <div class="col-md-3">
                        <span class="data-label">IGVH</span>
                        <span class="data-value"><?= htmlspecialchars($t['estado_igvh'] ?? '-') ?></span>
                    </div>
                    <div class="col-md-3">
                        <span class="data-label">TP53</span>
                        <span class="data-value"><?= htmlspecialchars($t['mutacion_tp53'] ?? '-') ?></span>
                    </div>
                    <div class="col-md-3">
                        <span class="data-label">Cariotipo Complejo</span>
                        <span class="data-value fw-bold <?= ($t['cariotipo_complejo'] == 1) ? 'text-danger' : '' ?>">
                            <?= ($t['cariotipo_complejo'] == 1) ? 'SÍ' : 'NO' ?>
                        </span>
                    </div>
                    
                    <div class="col-12">
                        <span class="data-label">FISH</span>
                        <div class="d-flex gap-2 flex-wrap">
                            <?php 
                                $hay_fish = false;
                                if ($t['fish_del17p']) { echo '<span class="badge bg-secondary">del(17p)</span>'; $hay_fish=true; }
                                if ($t['fish_del11q']) { echo '<span class="badge bg-secondary">del(11q)</span>'; $hay_fish=true; }
                                if ($t['fish_trisomia12']) { echo '<span class="badge bg-secondary">+12</span>'; $hay_fish=true; }
                                if ($t['fish_del13q']) { echo '<span class="badge bg-secondary">del(13q)</span>'; $hay_fish=true; }
                                
                                if (!$hay_fish) echo '<span class="text-muted small">Sin alteraciones registradas</span>';
                            ?>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <h6 class="text-primary border-bottom pb-2 mb-3">3. Evaluación de Respuesta</h6>
                    </div>

                    <div class="col-md-6">
                        <span class="data-label">Respuesta</span>
                        <span class="badge bg-info text-dark fs-6"><?= htmlspecialchars($t['respuesta'] ?? 'Sin evaluar') ?></span>
                    </div>

                    <div class="col-md-6">
                        </div>

                    <div class="col-md-6">
                        <span class="data-label">EMR (Sangre Periférica)</span>
                        <span class="data-value"><?= htmlspecialchars($t['emr_sp'] ?? '-') ?></span>
                    </div>
                    <div class="col-md-6">
                        <span class="data-label">EMR (Médula Ósea)</span>
                        <span class="data-value"><?= htmlspecialchars($t['emr_mo'] ?? '-') ?></span>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-end">
                <a href="editar_tratamiento.php?id=<?= $t['id_tratamiento'] ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar este registro
                </a>
            </div>
        </div>
    </div>
</body>
</html>