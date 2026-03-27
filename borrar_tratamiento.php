<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_tratamiento'])) {
    
    $id_tratamiento = $_POST['id_tratamiento'];
    $id_paciente = $_POST['id_paciente'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Tratamiento WHERE id_tratamiento = ?");
        $stmt->execute([$id_tratamiento]);

        header("Location: ver_paciente.php?codigo=" . urlencode($id_paciente));
        exit;

    } catch (PDOException $e) {
        
        die("Error técnico al intentar eliminar: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
    exit;
}