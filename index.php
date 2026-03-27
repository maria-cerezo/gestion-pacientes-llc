<?php
require 'db.php'; 

$busqueda = $_GET['busqueda'] ?? '';
$sql = "SELECT p.*, d.rai_diagnostico, d.binet_diagnostico 
        FROM Paciente p 
        LEFT JOIN Diagnostico d ON p.codigo = d.id_paciente
        WHERE p.codigo LIKE :b1 OR p.iniciales LIKE :b2
        ORDER BY p.fecha_registro DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([':b1' => "%$busqueda%", ':b2' => "%$busqueda%"]);
$pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Directorio de Pacientes - Gestión LLC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f6f8; }
        .table-hover tbody tr:hover { background-color: #e9ecef; }
        .btn-action { margin-right: 2px; }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-activity"></i> GESTIÓN CLÍNICA LLC
            </a>
        </div>
    </nav>

    <div class="container mt-4">
        
        <?php if(isset($_GET['mensaje']) && $_GET['mensaje'] == 'borrado_ok'): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> El registro del paciente ha sido eliminado correctamente del sistema.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-3 d-flex justify-content-between align-items-center bg-white rounded">
                <h5 class="mb-0 text-secondary"><i class="bi bi-people-fill me-2"></i>Directorio de Pacientes</h5>
                <div class="d-flex gap-2">
                    <form class="d-flex" role="search">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                            <input class="form-control border-start-0 ps-0" type="search" name="busqueda" placeholder="Buscar ID o Iniciales..." value="<?= htmlspecialchars($busqueda) ?>">
                        </div>
                    </form>
                    <a href="crear_paciente.php" class="btn btn-primary d-flex align-items-center gap-2">
                        <i class="bi bi-plus-lg"></i> <span>Nuevo Paciente</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-light text-uppercase small text-muted">
                        <tr>
                            <th class="ps-4">Código ID</th>
                            <th>Iniciales</th>
                            <th>F. Nacimiento</th>
                            <th>Sexo</th>
                            <th>Estadio (RAI/Binet)</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pacientes as $p): ?>
                        <tr>
                            <td class="ps-4 fw-bold text-dark"><?= $p['codigo'] ?></td>
                            <td><?= $p['iniciales'] ?></td>
                            <td><?= date("d/m/Y", strtotime($p['fecha_nacimiento'])) ?></td>
                            <td><?= $p['sexo'] ?></td>
                            <td>
                                <span class="badge rounded-pill text-bg-light border text-dark"><?= $p['rai_diagnostico'] ?? '-' ?></span>
                                <span class="badge rounded-pill text-bg-light border text-dark"><?= $p['binet_diagnostico'] ?? '-' ?></span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="btn-group" role="group">
                                    <a href="ver_paciente.php?codigo=<?= $p['codigo'] ?>" class="btn btn-sm btn-outline-secondary btn-action" title="Consultar"><i class="bi bi-eye"></i></a>
                                    <a href="editar_paciente.php?codigo=<?= $p['codigo'] ?>" class="btn btn-sm btn-outline-secondary btn-action" title="Modificar"><i class="bi bi-pencil"></i></a>
                                    <a href="confirmar_borrado.php?codigo=<?= $p['codigo'] ?>" class="btn btn-sm btn-outline-danger btn-action" title="Eliminar"><i class="bi bi-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($pacientes)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No se encontraron registros coincidentes.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>