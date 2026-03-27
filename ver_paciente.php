<?php
require 'db.php';

if (!isset($_GET['codigo'])) die("Error: Paciente no especificado.");
$codigo = $_GET['codigo'];

$stmt = $pdo->prepare("
    SELECT p.*, d.*, a.* FROM Paciente p
    LEFT JOIN Diagnostico d ON p.codigo = d.id_paciente
    LEFT JOIN Antecedentes_Personales a ON p.codigo = a.id_paciente
    WHERE p.codigo = ?
");
$stmt->execute([$codigo]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) die("Paciente no encontrado.");


$stmtTrat = $pdo->prepare("SELECT * FROM Tratamiento WHERE id_paciente = ? ORDER BY num_linea ASC");
$stmtTrat->execute([$codigo]);
$tratamientos = $stmtTrat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ficha Clínica: <?= htmlspecialchars($paciente['codigo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">
    <div class="container mt-4 mb-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Ficha del Paciente</h3>
            <a href="index.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Volver al listado</a>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white">
                Datos Personales y Diagnóstico
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label class="text-muted small">Código</label>
                        <div class="fw-bold fs-5"><?= htmlspecialchars($paciente['codigo']) ?></div>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Iniciales</label>
                        <div><?= htmlspecialchars($paciente['iniciales']) ?></div>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Fecha Nacimiento</label>
                        <div><?= date("d/m/Y", strtotime($paciente['fecha_nacimiento'])) ?></div>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Sexo</label>
                        <div><?= htmlspecialchars($paciente['sexo']) ?></div>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-3">
                        <label class="text-muted small">Fecha Diagnóstico</label>
                        <div><?= $paciente['fecha_diagnostico'] ? date("d/m/Y", strtotime($paciente['fecha_diagnostico'])) : '-' ?></div>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Estadio RAI</label>
                        <div><?= htmlspecialchars($paciente['rai_diagnostico'] ?? '-') ?></div>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted small">Estadio Binet</label>
                        <div><?= htmlspecialchars($paciente['binet_diagnostico'] ?? '-') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">Historial de Tratamientos</h5>
                <a href="nuevo_tratamiento.php?codigo=<?= $paciente['codigo'] ?>" class="btn btn-success btn-sm">
                    <i class="bi bi-plus-lg"></i> Añadir Tratamiento
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($tratamientos)): ?>
                    <div class="text-center py-5 text-muted">
                        <p>No hay tratamientos registrados para este paciente.</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($tratamientos as $t): ?>
                        <div class="border-bottom p-3 hover-bg">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-primary mb-0">Línea <?= $t['num_linea'] ?>: <?= htmlspecialchars($t['esquema']) ?></h6>
                                <div>
                                    <a href="ver_tratamiento.php?id=<?= $t['id_tratamiento'] ?>" class="btn btn-sm btn-outline-primary">Ver Detalles</a>
                                    
                                    <a href="confirmar_borrado_tratamiento.php?id=<?= $t['id_tratamiento'] ?>" class="btn btn-sm btn-outline-danger ms-1" title="Eliminar Tratamiento">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3">
                                    <span class="d-block small text-muted">Fecha Inicio</span>
                                    <span><?= $t['fecha_inicio'] ? date("d/m/Y", strtotime($t['fecha_inicio'])) : '-' ?></span>
                                </div>
                                <div class="col-md-3">
                                    <span class="d-block small text-muted">Duración</span>
                                    <span><?= htmlspecialchars($t['tipo_duracion'] ?? '-') ?></span>
                                </div>
                                <div class="col-md-3">
                                    <span class="d-block small text-muted">Respuesta</span>
                                    <strong><?= htmlspecialchars($t['respuesta'] ?? 'En curso') ?></strong>
                                </div>
                                <div class="col-md-3">
                                    <span class="d-block small text-muted">EMR (SP/MO)</span>
                                    <small><?= $t['emr_sp'] ?? '-' ?> / <?= $t['emr_mo'] ?? '-' ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</body>
</html>