<?php
require 'db.php';

$id_tratamiento = $_GET['id'] ?? '';
if (!$id_tratamiento) die("ID de tratamiento no facilitado.");

$stmt = $pdo->prepare("SELECT * FROM Tratamiento WHERE id_tratamiento = ?");
$stmt->execute([$id_tratamiento]);
$t = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$t) die("Tratamiento no encontrado.");

$linea_actual = $t['num_linea'];

$opciones_linea1 = [
    'R-FC', 'R-Bendamustina', 'O-Clb', 'R-Clb', 'Clorambucilo en monoterapia',
    'O-venetoclax', 'Ibrutinib', 'I+V', 'R-Ibrutinib', 'Acalabrutinib',
    'Zanubrutinib', 'A+V'
];
$opciones_restos = [
    'R-FC', 'R-Bendamustina', 'O-Clb', 'R-Clb', 'Clorambucilo en monoterapia',
    'Ciclofosfamida', 'Ibrutinib', 'R-Ibrutinib', 'R-idelalisib',
    'Acalabrutinib', 'Zanubrutinib', 'R-Venetoclax', 'Pirtobrutinib', 'Otro'
];

$lista_esquemas = ($linea_actual == 1) ? $opciones_linea1 : $opciones_restos;

$mapa_indicaciones = [
    'Insuficiencia medular' => 'Insuficiencia medular progresiva, con presencia o empeoramiento de anemia o trombocitopenia',
    'Esplenomegalia' => 'Esplenomegalia masiva, progresiva o sintomática',
    'Adenopatías' => 'Adenopatías de gran tamaño o de crecimiento progresivo o sintomático',
    'Linfocitosis' => 'Tiempo de duplicación linfocitario <= 6 meses o aumento mayor del 50% en 2 meses',
    'Autoinmune' => 'Anemia o trombocitopenia autoinmune que no responden al tratamiento con corticoides',
    'Afectación extraganglionar' => 'Afectación sintomática o funcional de otros órganos o tejidos: piel, riñón, pulmón',
    'Pérdida de peso' => 'Pérdida de peso no intencional >10% en los últimos 6 meses',
    'Astenia' => 'Astenia (ECOG >= 2)',
    'Fiebre' => 'Fiebre >38°C sin infección durante más de 2 semanas',
    'Sudoración nocturna' => 'Sudoración nocturna durante más de un mes',
    'Otra' => 'Otra razón no especificada'
];


$indicaciones_guardadas = !empty($t['indicacion_gellc']) ? explode(", ", $t['indicacion_gellc']) : [];


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $escala_cirs = !empty($_POST['escala_cirs']) ? $_POST['escala_cirs'] : null;
        $ipi_valor   = !empty($_POST['escala_cll_ipi_valor']) ? $_POST['escala_cll_ipi_valor'] : null;
        $ipi_riesgo  = !empty($_POST['escala_cll_ipi_riesgo']) ? $_POST['escala_cll_ipi_riesgo'] : null;

        $linfocitos = !empty($_POST['linfocitos']) ? $_POST['linfocitos'] : null;
        $leucocitos = !empty($_POST['leucocitos']) ? $_POST['leucocitos'] : null;
        $plaquetas = !empty($_POST['plaquetas']) ? $_POST['plaquetas'] : null;
        $neutrofilos = !empty($_POST['neutrofilos']) ? $_POST['neutrofilos'] : null;
        $creatinina = !empty($_POST['creatinina']) ? $_POST['creatinina'] : null;

        $cariotipo_complejo = isset($_POST['cariotipo_complejo']) ? 1 : 0;
        $otra_analitica = !empty($_POST['otra_analitica']) ? $_POST['otra_analitica'] : null;

        $esquema = $_POST['esquema'];
        $esquema_op_otro = ($esquema === 'Otro') ? $_POST['esquema_otro'] : null;
        $respuesta = !empty($_POST['respuesta']) ? $_POST['respuesta'] : null;
        $fecha_fin = ($_POST['duracion'] === 'Finito' && !empty($_POST['fecha_fin'])) ? $_POST['fecha_fin'] : null;

        $indicacion_texto = null;
        if (isset($_POST['indicacion']) && is_array($_POST['indicacion'])) {
            $indicacion_texto = implode(", ", $_POST['indicacion']);
        }

        $sql = "UPDATE Tratamiento SET 
            escala_cirs=?, escala_cll_ipi_valor=?, escala_cll_ipi_riesgo=?,
            fecha_inicio=?, fecha_fin=?, esquema=?, esquema_op_otro=?, indicacion_gellc=?, tipo_duracion=?,
            linfocitos=?, leucocitos=?, plaquetas=?, neutrofilos=?, creatinina=?,
            cariotipo_complejo=?, otra_analitica=?,
            estado_igvh=?, mutacion_tp53=?, 
            fish_del17p=?, fish_del11q=?, fish_trisomia12=?, fish_del13q=?,
            respuesta=?, emr_sp=?, emr_mo=?
            WHERE id_tratamiento=?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $escala_cirs, $ipi_valor, $ipi_riesgo,
            $_POST['fecha_inicio'], $fecha_fin, $esquema, $esquema_op_otro, $indicacion_texto, $_POST['duracion'],
            $linfocitos, $leucocitos, $plaquetas, $neutrofilos, $creatinina,
            $cariotipo_complejo, $otra_analitica,
            $_POST['igvh'], $_POST['tp53'],
            isset($_POST['fish_del17p'])?1:0, isset($_POST['fish_del11q'])?1:0,
            isset($_POST['fish_trisomia12'])?1:0, isset($_POST['fish_del13q'])?1:0,
            $respuesta, $_POST['emr_sp'], $_POST['emr_mo'],
            $id_tratamiento
        ]);

        header("Location: ver_paciente.php?codigo=" . urlencode($t['id_paciente']));
        exit;

    } catch (PDOException $e) {
        $error = "Error SQL: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Tratamiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script>
        function toggleOtro(select) {
            var div = document.getElementById('div_otro');
            div.style.display = (select.value === 'Otro') ? 'block' : 'none';
            if (select.value !== 'Otro') document.getElementById('input_otro').value = '';
        }

        function toggleFechaFin(select) {
            var div = document.getElementById('div_fecha_fin');
            div.style.display = (select.value === 'Finito') ? 'block' : 'none';
            if (select.value !== 'Finito') document.getElementById('input_fecha_fin').value = '';
        }
        
        window.onload = function() {
            toggleOtro(document.querySelector('select[name="esquema"]'));
            toggleFechaFin(document.querySelector('select[name="duracion"]'));
        };
    </script>
</head>
<body class="bg-light">
    <div class="container py-5" style="max-width: 900px;">
        <div class="d-flex justify-content-between mb-4">
            <h4 class="text-primary fw-bold">Editar Tratamiento (Línea <?= $t['num_linea'] ?>)</h4>
            <a href="ver_paciente.php?codigo=<?= urlencode($t['id_paciente']) ?>" class="btn btn-outline-secondary">Cancelar</a>
        </div>

        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" action="editar_tratamiento.php?id=<?= $id_tratamiento ?>">
            
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">0. Evaluación Basal y Escalas</div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Escala CIRS</label>
                            <div class="input-group">
                                <input type="number" name="escala_cirs" class="form-control" value="<?= htmlspecialchars($t['escala_cirs'] ?? '') ?>">
                                <a href="https://www.mdcalc.com/calc/10088/cumulative-illness-rating-scale-geriatric-cirs-g" target="_blank" class="btn btn-outline-primary"><i class="bi bi-calculator"></i></a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CLL-IPI (Valor)</label>
                            <div class="input-group">
                                <input type="number" name="escala_cll_ipi_valor" class="form-control" value="<?= htmlspecialchars($t['escala_cll_ipi_valor'] ?? '') ?>">
                                <a href="https://www.mdcalc.com/calc/4054/international-prognostic-index-chronic-lymphocytic-leukemia-cll-ipi" target="_blank" class="btn btn-outline-primary"><i class="bi bi-calculator"></i></a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CLL-IPI (Riesgo)</label>
                            <select name="escala_cll_ipi_riesgo" class="form-select">
                                <option value="">Seleccione...</option>
                                <?php 
                                $riesgos = ['Bajo Riesgo(0-1)', 'Riesgo Intermedio (2-3)', 'Alto Riesgo (<04)'];
                                foreach($riesgos as $r) {
                                    $sel = ($t['escala_cll_ipi_riesgo'] == $r) ? 'selected' : '';
                                    echo "<option value='$r' $sel>$r</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">1. Esquema Terapéutico</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Fecha Inicio *</label>
                            <input type="date" name="fecha_inicio" class="form-control" value="<?= $t['fecha_inicio'] ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Esquema</label>
                            <select name="esquema" class="form-select" onchange="toggleOtro(this)" required>
                                <option value="">Seleccione...</option>
                                <?php foreach ($lista_esquemas as $op): ?>
                                    <option value="<?= $op ?>" <?= ($t['esquema'] == $op) ? 'selected' : '' ?>><?= $op ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4" id="div_otro" style="display:none;">
                            <label class="form-label">Especificar cual:</label>
                            <input type="text" name="esquema_otro" id="input_otro" class="form-control" value="<?= htmlspecialchars($t['esquema_op_otro'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tipo Duración</label>
                            <select name="duracion" class="form-select" onchange="toggleFechaFin(this)">
                                <option value="Indefinido" <?= ($t['tipo_duracion'] == 'Indefinido') ? 'selected' : '' ?>>Indefinido</option>
                                <option value="Finito" <?= ($t['tipo_duracion'] == 'Finito') ? 'selected' : '' ?>>Finito</option>
                            </select>
                        </div>
                        <div class="col-md-4" id="div_fecha_fin" style="display:none;">
                            <label class="form-label">Fecha Fin</label>
                            <input type="date" name="fecha_fin" id="input_fecha_fin" class="form-control" value="<?= $t['fecha_fin'] ?>">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold">Indicación GELLC (Selección Múltiple)</label>
                            
                            <div class="accordion mb-3" id="accordionGellc">
                                <div class="accordion-item">
                                    <h2 class="accordion-header">
                                        <button class="accordion-button collapsed bg-light text-primary py-2" type="button" data-bs-toggle="collapse" data-bs-target="#collapseGellc">
                                            <i class="bi bi-info-circle me-2"></i> Ver Guía Detallada
                                        </button>
                                    </h2>
                                    <div id="collapseGellc" class="accordion-collapse collapse" data-bs-parent="#accordionGellc">
                                        <div class="accordion-body small">
                                            <ul class="list-unstyled mb-0">
                                                <?php foreach ($mapa_indicaciones as $corto => $largo): ?>
                                                    <li class="mb-2"><strong><?= $corto ?>:</strong> <?= $largo ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-3 border rounded">
                                <div class="row">
                                    <?php foreach ($mapa_indicaciones as $corto => $largo): ?>
                                        <div class="col-md-6 mb-2">
                                            <div class="form-check">
                                                <?php $checked = in_array($corto, $indicaciones_guardadas) ? 'checked' : ''; ?>
                                                <input class="form-check-input" type="checkbox" name="indicacion[]" value="<?= $corto ?>" id="chk_<?= md5($corto) ?>" <?= $checked ?>>
                                                <label class="form-check-label" for="chk_<?= md5($corto) ?>">
                                                    <?= $corto ?>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">2. Analítica y Biología</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2"><label class="small">Linfocitos</label><input type="number" step="0.01" name="linfocitos" class="form-control form-control-sm" value="<?= htmlspecialchars($t['linfocitos'] ?? '') ?>"></div>
                        <div class="col-md-2"><label class="small">Leucocitos</label><input type="number" step="0.01" name="leucocitos" class="form-control form-control-sm" value="<?= htmlspecialchars($t['leucocitos'] ?? '') ?>"></div>
                        <div class="col-md-2"><label class="small">Plaquetas</label><input type="number" step="0.01" name="plaquetas" class="form-control form-control-sm" value="<?= htmlspecialchars($t['plaquetas'] ?? '') ?>"></div>
                        <div class="col-md-2"><label class="small">Neutrófilos</label><input type="number" step="0.01" name="neutrofilos" class="form-control form-control-sm" value="<?= htmlspecialchars($t['neutrofilos'] ?? '') ?>"></div>
                        <div class="col-md-2"><label class="small">Creatinina</label><input type="number" step="0.01" name="creatinina" class="form-control form-control-sm" value="<?= htmlspecialchars($t['creatinina'] ?? '') ?>"></div>
                    </div>
                    
                    <div class="row g-3 mt-1">
                        <div class="col-md-12">
                            <label class="small">Otro</label>
                            <input type="text" name="otra_analitica" class="form-control form-control-sm" value="<?= htmlspecialchars($t['otra_analitica'] ?? '') ?>">
                        </div>
                    </div>

                    <hr>

                    <div class="row g-3 align-items-center">
                        <div class="col-md-3">
                            <label class="small">IGVH</label>
                            <select name="igvh" class="form-select form-select-sm">
                                <option value="Desconocido" <?= ($t['estado_igvh'] == 'Desconocido')?'selected':'' ?>>Desconocido</option>
                                <option value="Mutado" <?= ($t['estado_igvh'] == 'Mutado')?'selected':'' ?>>Mutado</option>
                                <option value="No mutado" <?= ($t['estado_igvh'] == 'No mutado')?'selected':'' ?>>No mutado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small">TP53</label>
                            <select name="tp53" class="form-select form-select-sm">
                                <option value="Desconocido" <?= ($t['mutacion_tp53'] == 'Desconocido')?'selected':'' ?>>Desconocido</option>
                                <option value="Si" <?= ($t['mutacion_tp53'] == 'Si')?'selected':'' ?>>Si</option>
                                <option value="No" <?= ($t['mutacion_tp53'] == 'No')?'selected':'' ?>>No</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-check mt-4">
                                <input type="checkbox" name="cariotipo_complejo" value="1" class="form-check-input" <?= ($t['cariotipo_complejo'] == 1)?'checked':'' ?>>
                                <label class="form-check-label small fw-bold">Cariotipo Complejo</label>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label class="small">FISH</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="form-check"><input type="checkbox" name="fish_del17p" class="form-check-input" <?= ($t['fish_del17p']==1)?'checked':'' ?>> 17p</div>
                                <div class="form-check"><input type="checkbox" name="fish_del11q" class="form-check-input" <?= ($t['fish_del11q']==1)?'checked':'' ?>> 11q</div>
                                <div class="form-check"><input type="checkbox" name="fish_trisomia12" class="form-check-input" <?= ($t['fish_trisomia12']==1)?'checked':'' ?>> +12</div>
                                <div class="form-check"><input type="checkbox" name="fish_del13q" class="form-check-input" <?= ($t['fish_del13q']==1)?'checked':'' ?>> 13q</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">3. Evaluación de Respuesta</div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Respuesta</label>
                            <select name="respuesta" class="form-select">
                                <option value="">Seleccione...</option>
                                <?php 
                                $respuestas = ['RC (Respuesta completa)', 'RCi (Respuesta completa incompleta)', 'RP (Respuesta parcial)', 'RP-linfocitosis', 'EE (Enfermedad estable)', 'E.Progresiva'];
                                foreach ($respuestas as $res) {
                                    $sel = ($t['respuesta'] == $res) ? 'selected' : '';
                                    echo "<option value='$res' $sel>$res</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">EMR (SP)</label>
                            <select name="emr_sp" class="form-select">
                                <option value="No realizada" <?= ($t['emr_sp'] == 'No realizada')?'selected':'' ?>>No realizada</option>
                                <option value="Positiva" <?= ($t['emr_sp'] == 'Positiva')?'selected':'' ?>>Positiva</option>
                                <option value="Negativa" <?= ($t['emr_sp'] == 'Negativa')?'selected':'' ?>>Negativa</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">EMR (MO)</label>
                            <select name="emr_mo" class="form-select">
                                <option value="No realizada" <?= ($t['emr_mo'] == 'No realizada')?'selected':'' ?>>No realizada</option>
                                <option value="Positiva" <?= ($t['emr_mo'] == 'Positiva')?'selected':'' ?>>Positiva</option>
                                <option value="Negativa" <?= ($t['emr_mo'] == 'Negativa')?'selected':'' ?>>Negativa</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold">GUARDAR CAMBIOS</button>
        </form>
    </div>
</body>
</html>