<?php
require 'db.php';

if (!isset($_GET['codigo'])) {
    header("Location: index.php");
    exit;
}

$codigo = $_GET['codigo'];

$stmt = $pdo->prepare("SELECT * FROM Paciente WHERE codigo = ?");
$stmt->execute([$codigo]);
$paciente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$paciente) die("Paciente no encontrado.");

$stmtTrat = $pdo->prepare("SELECT COUNT(*) FROM Tratamiento WHERE id_paciente = ?");
$stmtTrat->execute([$codigo]);
$num_tratamientos = $stmtTrat->fetchColumn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación de Seguridad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { background-color: #e9ecef; }
        .card-danger-top { border-top: 4px solid #dc3545; }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh;">

    <div class="card shadow border-0 card-danger-top" style="max-width: 550px; width: 100%;">
        <div class="card-body p-4">
            <div class="text-center mb-4">
                <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                <h4 class="fw-bold mt-2">Confirmar Eliminación</h4>
                <p class="text-muted">Esta acción es irreversible.</p>
            </div>

            <div class="alert alert-light border text-start mb-4">
                <p class="mb-2">Se eliminarán los siguientes registros:</p>
                <ul class="mb-0 small text-secondary">
                    <li>Ficha del paciente: <strong><?= $paciente['codigo'] ?></strong></li>
                    <li>Historia clínica y antecedentes.</li>
                    <li><strong><?= $num_tratamientos ?></strong> líneas de tratamiento registradas.</li>
                </ul>
            </div>

            <form action="borrar_paciente.php" method="POST">
                <input type="hidden" name="codigo" value="<?= $paciente['codigo'] ?>">
                <input type="hidden" name="confirmacion" value="SI">
                
                <div class="d-grid gap-2 d-md-flex justify-content-center">
                    <a href="index.php" class="btn btn-light border px-4">Cancelar</a>
                    <button type="submit" class="btn btn-danger px-4"><i class="bi bi-trash me-2"></i>Eliminar Definitivamente</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>