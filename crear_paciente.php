<?php
require 'db.php';
$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO Paciente (codigo, iniciales, fecha_nacimiento, sexo) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['codigo'],
            $_POST['iniciales'],
            $_POST['fecha_nacimiento'],
            $_POST['sexo']
        ]);

        
        $stmt = $pdo->prepare("INSERT INTO Diagnostico (id_paciente, fecha_diagnostico, rai_diagnostico, binet_diagnostico) VALUES (?, ?, ?, ?)");
        $stmt->execute([
            $_POST['codigo'],
            $_POST['fecha_diagnostico'],
            !empty($_POST['rai']) ? $_POST['rai'] : null,
            !empty($_POST['binet']) ? $_POST['binet'] : null
        ]);

        
        $hta = isset($_POST['hta']) ? 1 : 0;
        $dm = isset($_POST['dm']) ? 1 : 0;
        $fa = isset($_POST['fa']) ? 1 : 0;
        $valv_card = isset($_POST['valvulopatia_cardiaca']) ? 1 : 0;
        $valv_renal = isset($_POST['valvulopatia_renal_cronica']) ? 1 : 0;
        $valv_hep = isset($_POST['valvulopatia_hepatica']) ? 1 : 0;
        $med_concom = isset($_POST['medicacion_concomitante']) ? 1 : 0;
        
        $antiagregacion = isset($_POST['antiagregacion']) ? 1 : 0;

        $stmt = $pdo->prepare("INSERT INTO Antecedentes_Personales (
            id_paciente, hta, dm, fa, 
            valvulopatia_cardiaca, valvulopatia_renal_cronica, valvulopatia_hepatica, 
            medicacion_concomitante, anticoagulacion, antiagregacion
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->execute([
            $_POST['codigo'], $hta, $dm, $fa, 
            $valv_card, $valv_renal, $valv_hep, 
            $med_concom, $_POST['anticoagulacion'], $antiagregacion
        ]);

        $pdo->commit();
        
        
        header("Location: ver_paciente.php?codigo=" . $_POST['codigo']);
        exit;

    } catch (PDOException $e) {
        $pdo->rollBack();
        if ($e->getCode() == 23000) {
             $error_msg = "El código " . $_POST['codigo'] . " ya existe.";
        } else {
             $error_msg = "Error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Nuevo Paciente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

    <div class="container py-5" style="max-width: 900px;">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="text-primary fw-bold"><i class="bi bi-person-plus"></i> Nuevo Paciente</h3>
            <a href="index.php" class="btn btn-outline-secondary">Cancelar</a>
        </div>

        <?php if(isset($error_msg)): ?>
            <div class="alert alert-danger"><?= $error_msg ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white text-primary fw-bold">1. Datos Personales</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Código ID *</label>
                            <input type="text" name="codigo" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Iniciales *</label>
                            <input type="text" name="iniciales" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Fecha Nacimiento *</label>
                            <input type="date" name="fecha_nacimiento" class="form-control" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sexo *</label>
                            <select name="sexo" class="form-select" required>
                                <option value="Hombre">Hombre</option>
                                <option value="Mujer">Mujer</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white text-primary fw-bold">2. Diagnóstico</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Fecha Diagnóstico *</label>
                            <input type="date" name="fecha_diagnostico" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estadio RAI</label>
                            <select name="rai" class="form-select">
                                <option value="">No aplica</option>
                                <option value="0">0</option>
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Estadio Binet</label>
                            <select name="binet" class="form-select">
                                <option value="">No aplica</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white text-primary fw-bold">3. Antecedentes Médicos</div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="hta" class="form-check-input" value="1">
                                <label class="form-check-label">Hipertensión (HTA)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="dm" class="form-check-input" value="1">
                                <label class="form-check-label">Diabetes (DM)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="fa" class="form-check-input" value="1">
                                <label class="form-check-label">Fibrilación Auricular</label>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="valvulopatia_cardiaca" class="form-check-input" value="1">
                                <label class="form-check-label">Valvulopatía Cardíaca</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="valvulopatia_renal_cronica" class="form-check-input" value="1">
                                <label class="form-check-label">Insuf. Renal Crónica</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="valvulopatia_hepatica" class="form-check-input" value="1">
                                <label class="form-check-label">Hepatopatía</label>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="medicacion_concomitante" class="form-check-input" value="1">
                                <label class="form-check-label fw-bold">Medicación Concomitante</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="antiagregacion" class="form-check-input" value="1">
                                <label class="form-check-label fw-bold">Antiagregación</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Anticoagulación</label>
                            <select name="anticoagulacion" class="form-select">
                                <option value="Ninguna">Ninguna</option>
                                <option value="Antivitamina K">Antivitamina K</option>
                                <option value="Apixaban">Apixaban</option>
                                <option value="Rivaroxaban">Rivaroxaban</option>
                                <option value="Dabigatran">Dabigatran</option>
                                <option value="Edoxaban">Edoxaban</option>
                                <option value="HBPM">HBPM</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2 pb-5">
                <button type="submit" class="btn btn-primary btn-lg fw-bold p-3">
                    <i class="bi bi-save me-2"></i> GUARDAR PACIENTE
                </button>
            </div>

        </form>
    </div>
</body>
</html>
