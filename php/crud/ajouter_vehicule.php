<?php
session_start();
require_once(__DIR__ . '/../database/db.php');
require_once(__DIR__ . '/../security/connexion.php');

// 🔐 Vérifie si l'utilisateur est authentifié via un token de session
if (!isset($_SESSION['token']) || !isTokenValid($_SESSION['token'])) {
    header('Location: ../index.php');
    exit();
}

$conn = connectDB();


// 🔒 Génération d'un token CSRF s'il n'existe pas encore
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = null;


// 📩 Si le formulaire est soumis (requête POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 🔒 Vérification du token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = "Requête non autorisée (CSRF token invalide).";
    } else {
        // 🔍 Récupération et nettoyage des données du formulaire
        $marque = trim($_POST['marque'] ?? '');
        $modele = trim($_POST['modele'] ?? '');
        $annee = intval($_POST['annee'] ?? 0);
        $client_id = intval($_POST['client_id'] ?? 0);

        // ✅ Validation simple des champs
        if ($marque === '' || $modele === '' || $annee <= 1900 || $client_id <= 0) {
            $error = "Veuillez remplir correctement tous les champs.";
        } else {
            // 🛠️ Préparation de la requête SQL avec des requêtes préparées (sécurité contre les injections SQL)
            $stmt = $conn->prepare("INSERT INTO vehicules (marque, modele, annee, client_id) VALUES (?, ?, ?, ?)");
            if ($stmt === false) {
                $error = "Erreur de préparation de la requête.";
            } else {
                $stmt->bind_param("ssii", $marque, $modele, $annee, $client_id);
                if ($stmt->execute()) {
                    header("Location: ../dashboard.php");
                    exit();
                } else {
                    $error = "Erreur lors de l'ajout du véhicule.";
                }
            }
        }
    }
}

// 🔄 Récupération de la liste des clients pour affichage dans le menu déroulant
$clients = $conn->query("SELECT id, nom FROM clients");
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Ajouter un véhicule</h4>
        </div>
        <div class="card-body">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                <div class="mb-3">
                    <label class="form-label" for="marque">Marque</label>
                    <input type="text" id="marque" name="marque" class="form-control" required value="<?= isset($marque) ? htmlspecialchars($marque) : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="modele">Modèle</label>
                    <input type="text" id="modele" name="modele" class="form-control" required value="<?= isset($modele) ? htmlspecialchars($modele) : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="annee">Année</label>
                    <input type="number" id="annee" name="annee" class="form-control" required min="1900" max="<?= date('Y') ?>" value="<?= isset($annee) && $annee > 0 ? intval($annee) : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="client_id">Client</label>
                    <select name="client_id" id="client_id" class="form-select" required>
                        <option value="">-- Choisir un client --</option>
                        <?php while ($client = $clients->fetch_assoc()): ?>
                            <option value="<?= $client['id'] ?>" <?= (isset($client_id) && $client_id == $client['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($client['nom']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Ajouter</button>
                <a href="../dashboard.php" class="btn btn-secondary ms-2">Annuler</a>
            </form>
        </div>
    </div>
</div>