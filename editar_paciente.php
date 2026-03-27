<?php
require 'db.php';
$codigo = $_GET['codigo'] ?? '';

if (!$codigo) die("Falta el código.");

$stmt = $pdo->prepare("SELECT p.*, d.*, a.* FROM Paciente p 
                        LEFT JOIN Diagnostico d ON p.codigo = d.id_paciente 
                        LEFT JOIN Antecedentes_Personales a ON p.codigo = a.id_paciente 
                        WHERE p.codigo = ?");
$stmt->execute([$codigo]);
$datos = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$datos) die("Paciente no encontrado.");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo->beginTransaction();

        $sql1 = "UPDATE Paciente SET iniciales=?, fecha_nacimiento=?, sexo=? WHERE codigo=?";
        $pdo->prepare($sql1)->execute([$_POST['iniciales'], $_POST['fecha_nacimiento'], $_POST['sexo'], $codigo]);

        $sql2 = "UPDATE Diagnostico SET fecha_diagnostico=?, rai_diagnostico=?, binet_diagnostico=? WHERE id_paciente=?";
        $pdo->prepare($sql2)->execute([$_POST['fecha_diagnostico'], $_POST['rai'], $_POST['binet'], $codigo]);

        $sql3 = "UPDATE Antecedentes_Personales SET 
                    hta=?, dm=?, fa=?, 
                    valvulopatia_cardiaca=?, valvulopatia_renal_cronica=?, valvulopatia_hepatica=?, 
                    medicacion_concomitante=?, 
                    antiagregacion=?, anticoagulacion=? 
                WHERE id_paciente=?";

        $pdo->prepare($sql3)->execute([
            isset($_POST['hta']) ? 1 : 0,
            isset($_POST['dm']) ? 1 : 0,
            isset($_POST['fa']) ? 1 : 0,
            isset($_POST['valvulopatia_cardiaca']) ? 1 : 0,
            isset($_POST['valvulopatia_renal_cronica']) ? 1 : 0, 
            isset($_POST['valvulopatia_hepatica']) ? 1 : 0,
            isset($_POST['medicacion_concomitante']) ? 1 : 0,
            isset($_POST['antiagregacion']) ? 1 : 0,
            $_POST['anticoagulacion'], 
            $codigo
        ]);

        $pdo->commit();
        
        header("Location: index.php?mensaje=editado_ok");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error al actualizar: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Paciente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light pb-5">
    <div class="container mt-4">
        <h2 class="text-primary mb-4"> <i class="bi bi-person-gear"></i> Editar Paciente: <?= $codigo ?></h2>
        
        <?php if(isset($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>

        <form method="POST">
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-primary text-white fw-light">Datos Personales</div>
                <div class="card-body row">
                    <div class="col-md-4">
                        <label class="form-label">Iniciales</label>
                        <input type="text" name="iniciales" class="form-control" value="<?= $datos['iniciales'] ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha Nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" value="<?= $datos['fecha_nacimiento'] ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Sexo</label>
                        <select name="sexo" class="form-select">
                            <option value="Hombre" <?= $datos['sexo']=='Hombre'?'selected':'' ?>>Hombre</option>
                            <option value="Mujer" <?= $datos['sexo']=='Mujer'?'selected':'' ?>>Mujer</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-primary text-white fw-light">Diagnóstico</div>
                <div class="card-body row">
                    <div class="col-md-4">
                        <label class="form-label">Fecha Diagnóstico</label>
                        <input type="date" name="fecha_diagnostico" class="form-control" value="<?= $datos['fecha_diagnostico'] ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Estadio RAI</label>
                        <select name="rai" class="form-select">
                            <?php $opts = ['0','1','2','3','4','Desconocido']; 
                            foreach($opts as $o) echo "<option value='$o' ".($datos['rai_diagnostico']==$o?'selected':'').">$o</option>"; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Estadio Binet</label>
                        <select name="binet" class="form-select">
                            <?php $opts = ['A','B','C','Desconocido']; 
                            foreach($opts as $o) echo "<option value='$o' ".($datos['binet_diagnostico']==$o?'selected':'').">$o</option>"; ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-primary text-white fw-light">Antecedentes (Marcar los activos)</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3"><div class="form-check"><input type="checkbox" name="hta" class="form-check-input" <?= $datos['hta']?'checked':'' ?>> HTA</div></div>
                        <div class="col-md-3"><div class="form-check"><input type="checkbox" name="dm" class="form-check-input" <?= $datos['dm']?'checked':'' ?>> Diabetes (DM)</div></div>
                        <div class="col-md-3"><div class="form-check"><input type="checkbox" name="fa" class="form-check-input" <?= $datos['fa']?'checked':'' ?>> Fibrilación Aur.</div></div>

                        <div class="col-md-3"><div class="form-check"><input type="checkbox" name="valvulopatia_cardiaca" class="form-check-input" <?= $datos['valvulopatia_cardiaca']?'checked':'' ?>> Valvulopatía Card.</div></div>
                        <div class="col-md-3"><div class="form-check"><input type="checkbox" name="valvulopatia_renal_cronica" class="form-check-input" <?= $datos['valvulopatia_renal_cronica']?'checked':'' ?>> Valvulopatía Renal</div></div>
                        <div class="col-md-3"><div class="form-check"><input type="checkbox" name="valvulopatia_hepatica" class="form-check-input" <?= $datos['valvulopatia_hepatica']?'checked':'' ?>> Valvulopatía Hep.</div></div>
                        <div class="col-md-3"><div class="form-check"><input type="checkbox" name="medicacion_concomitante" class="form-check-input" <?= $datos['medicacion_concomitante']?'checked':'' ?>> Med. Concomitante</div></div>
                        
                        <div class="col-md-3">
                            <div class="form-check">
                                <input type="checkbox" name="antiagregacion" class="form-check-input" <?= $datos['antiagregacion'] ? 'checked' : '' ?>>
                                <label class="form-check-label">Antiagregación</label>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3"> 
                            <label class="form-label fw-bold">Anticoagulación</label>
                            <select name="anticoagulacion" class="form-select">
                                <option value="Ninguna" <?= ($datos['anticoagulacion'] == 'Ninguna') ? 'selected' : '' ?>>Ninguna</option>
                                <option value="Antivitamina K" <?= ($datos['anticoagulacion'] == 'Antivitamina K') ? 'selected' : '' ?>>Antivitamina K</option>
                                <option value="Apixaban" <?= ($datos['anticoagulacion'] == 'Apixaban') ? 'selected' : '' ?>>Apixaban</option>
                                <option value="Rivaroxaban" <?= ($datos['anticoagulacion'] == 'Rivaroxaban') ? 'selected' : '' ?>>Rivaroxaban</option>
                                <option value="Dabigatran" <?= ($datos['anticoagulacion'] == 'Dabigatran') ? 'selected' : '' ?>>Dabigatran</option>
                                <option value="Edoxaban" <?= ($datos['anticoagulacion'] == 'Edoxaban') ? 'selected' : '' ?>>Edoxaban</option>
                                <option value="HBPM" <?= ($datos['anticoagulacion'] == 'HBPM') ? 'selected' : '' ?>>HBPM</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4 mb-5">
                
                <a href="index.php" class="btn btn-secondary me-md-2 p-3 px-4">
                    <i class="bi bi-x-circle me-2"></i> Cancelar
                </a>

                <button type="submit" class="btn btn-primary p-3 px-4 fw-bold">
                    <i class="bi bi-save me-2"></i> GUARDAR CAMBIOS
                </button>
                
            </div>
        </form>
    </div>
</body>
</html>