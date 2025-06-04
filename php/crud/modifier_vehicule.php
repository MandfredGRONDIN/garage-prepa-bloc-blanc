<?php
session_start();
require_once('../database/db.php');
$conn = connectDB();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header("Location: ../index.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM vehicules WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$vehicule = $stmt->get_result()->fetch_assoc();

if (!$vehicule) {
    echo "Véhicule introuvable.";
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        $error = "Token CSRF invalide.";
    } else {
        $marque = trim($_POST['marque'] ?? '');
        $modele = trim($_POST['modele'] ?? '');
        $annee = intval($_POST['annee'] ?? 0);
        $client_id = intval($_POST['client_id'] ?? 0);

        if (
            $marque === '' || $modele === '' || $annee <= 1900 || $client_id <= 0 ||
            !preg_match('/^[\p{L}\s\-]+$/u', $marque) ||
            !preg_match('/^[\p{L}0-9\s\-]+$/u', $modele)
        ) {
            $error = "Veuillez remplir correctement tous les champs.";
        } else {
            $update = $conn->prepare("UPDATE vehicules SET marque = ?, modele = ?, annee = ?, client_id = ? WHERE id = ?");
            $update->bind_param("ssiii", $marque, $modele, $annee, $client_id, $id);
            $update->execute();

            header("Location: ../index.php");
            exit();
        }
    }
}

// Récupération des clients avec requête préparée
$clientsStmt = $conn->prepare("SELECT id, nom FROM clients");
$clientsStmt->execute();
$clients = $clientsStmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Modifier le véhicule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">Modifier le véhicule</h4>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" novalidate>
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Marque</label>
                        <input type="text" name="marque" class="form-control"
                            value="<?= htmlspecialchars($vehicule['marque']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Modèle</label>
                        <input type="text" name="modele" class="form-control"
                            value="<?= htmlspecialchars($vehicule['modele']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Année</label>
                        <input type="number" name="annee" class="form-control"
                            value="<?= htmlspecialchars($vehicule['annee']) ?>" required min="1900" max="<?= date('Y') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Client</label>
                        <select name="client_id" class="form-select" required>
                            <option value="">-- Choisir un client --</option>
                            <?php while ($client = $clients->fetch_assoc()): ?>
                                <option value="<?= $client['id'] ?>" <?= $client['id'] == $vehicule['client_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($client['nom']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                    <a href="../index.php" class="btn btn-secondary ms-2">Annuler</a>
                </form>
            </div>
        </div>
    </div>
</body>

</html>