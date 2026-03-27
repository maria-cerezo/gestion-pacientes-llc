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
    <title>Confirmar Borrado de Tratamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
    <div class="container" style="max-width: 500px;">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5 text-center">
                <i class="bi bi-exclamation-octagon text-danger" style="font-size: 4rem;"></i>
                <h3 class="mt-3 fw-bold">¿Estás seguro?</h3>
                <p class="text-muted">Vas a eliminar la <strong>Línea <?= $t['num_linea'] ?></strong> del tratamiento del paciente <strong><?= htmlspecialchars($t['cod_paciente']) ?></strong>.</p>
                
                <div class="alert alert-warning text-start mb-4">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Atención:</strong> Esta acción es irreversible. Toda la información registrada de este tratamiento (esquema, fechas y respuestas) se borrará y <strong>no se podrá recuperar</strong>.
                </div>

                <form action="borrar_tratamiento.php" method="POST">
                    <input type="hidden" name="id_tratamiento" value="<?= $t['id_tratamiento'] ?>">
                    <input type="hidden" name="id_paciente" value="<?= $t['id_paciente'] ?>">
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-danger btn-lg fw-bold">Sí, borrar permanentemente</button>
                        <a href="ver_paciente.php?codigo=<?= $t['id_paciente'] ?>" class="btn btn-light border">Cancelar y volver</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>