<?php
// Démarrer la session
session_start();

// Récupérer les informations de l'utilisateur connecté depuis la base de données
$host = "localhost";
$dbname = "cv-crafter";
$username = "pma";
$passwordDB = "plomkiplomki";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $passwordDB);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer les informations de l'utilisateur connecté
    $query = "SELECT login, prenom, nom FROM utilisateurs WHERE login = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$_SESSION["login"]]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier si le formulaire de mise à jour a été soumis
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Récupérer les nouvelles données du formulaire
        $newLogin = $_POST["login"];
        $newPrenom = $_POST["prenom"];
        $newNom = $_POST["nom"];
        $newPassword = $_POST["password"];

        // Mettre à jour les informations de l'utilisateur dans la base de données
        $updateQuery = "UPDATE utilisateurs SET login = ?, prenom = ?, nom = ?, password = ? WHERE login = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([$newLogin, $newPrenom, $newNom, $newPassword, $_SESSION["login"]]);

        // Mettre à jour le login de l'utilisateur dans la variable de session
        $_SESSION["login"] = $newLogin;

        // Redirection vers la page de profil mise à jour
        header("Location: profil.php");
        exit;
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}

// Traitement de la déconnexion
if (isset($_GET["logout"])) {
    // Supprimer toutes les variables de session
    session_unset();

    // Détruire la session
    session_destroy();

    // Redirection vers la page de connexion
    header("Location: connexion.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profil</title>
    <link id="style" rel="stylesheet" type="text/css" href="style6.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&display=swap');
  </style>
</head>
<body class="bodyprof">
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <h1>Profil</h1>
        <label for="login" style="font-size: 0.5em">Login:</label>
        <input type="text" id="login" name="login" value="<?php echo $row["login"]; ?>" required><br>

        <label for="prenom" style="font-size: 0.5em">Prénom:</label>
        <input type="text" id="prenom" name="prenom" value="<?php echo $row["prenom"]; ?>" required><br>

        <label for="nom" style="font-size: 0.5em">Nom:</label>
        <input type="text" id="nom" name="nom" value="<?php echo $row["nom"]; ?>" required><br>

        <label for="password" style="font-size: 0.5em">Nouveau mot de passe:</label>
        <input type="password" id="password" name="password" required><br>

        <input type="submit" value="Enregistrer les modifications">
    </form>
    <br>
    <form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <input type="hidden" name="logout" value="true">
        <input type="submit" value="Se déconnecter">
    </form>
</body>
</html>
