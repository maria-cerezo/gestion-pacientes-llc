<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['codigo']) && $_POST['confirmacion'] == 'SI') {
    
    $codigo = $_POST['codigo'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Paciente WHERE codigo = ?");
        $stmt->execute([$codigo]);
        
        header("Location: index.php?mensaje=borrado_ok"); 

    } catch (PDOException $e) {
        die("❌ Error técnico al borrar: " . $e->getMessage());
    }

} else {
    header("Location: index.php");
    exit;
}
?>