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
    $query = "SELECT login, prenom, nom, phone, postal, ville, photo FROM utilisateurs WHERE login = ?";
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
        $newphone = $_POST["phone"];
        $newpostal = $_POST["postal"];
        $newville = $_POST["ville"];
        $newphoto = $_POST["photo"];
    

           // Vérifie si le fichier a été uploadé sans erreur.
        if(isset( $newphoto) &&  $newphoto["error"] == 0){
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
            $filename = $newphoto["name"];
            $filetype = $newphoto["type"];
            $filesize = $newphoto["size"];

            // Vérifie l'extension du fichier
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(!array_key_exists($ext, $allowed)) die("Erreur : Veuillez sélectionner un format de fichier valide.");

            // Vérifie la taille du fichier - 5Mo maximum
            $maxsize = 5 * 1024 * 1024;
            if($filesize > $maxsize) die("Error: La taille du fichier est supérieure à la limite autorisée.");

            // Vérifie le type MIME du fichier
            if(in_array($filetype, $allowed)){
                // Vérifie si le fichier existe avant de le télécharger.
                if(file_exists("upload/" . $newphoto["name"])){
                    echo     $newphoto["name"] ." existe déjà.";
                } else{
                    // Mettre à jour les informations de l'utilisateur dans la base de données
            $updateQuery = "UPDATE utilisateurs SET login = ?, prenom = ?, nom = ?, password = ?, phone = ?, postal = ?, ville = ?, photo = ?  WHERE login = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->execute([$newLogin, $newPrenom, $newNom, $newPassword, $newphone, $newpostal, $newville, $newphoto, $_SESSION["login"]]);

            // Mettre à jour le login de l'utilisateur dans la variable de session
            $_SESSION["login"] = $newLogin;

        
                    move_uploaded_file($newphoto["tmp_name"], "../upload" . $newphoto["name"]);
                    echo "Votre fichier a été téléchargé avec succès.";
                    // Redirection vers la page de profil mise à jour
            header("Location: profil.php");
            exit;
                } 
            } else{
                echo "Error: Il y a eu un problème de téléchargement de votre fichier. Veuillez réessayer."; 
            }
        } else{
            echo "Error: " . $newphoto["error"];
        }

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

        <label for="phone" style="font-size: 0.5em">Numéro de téléphone:</label>
        <input type="tel" id="phone" name="phone" value="<?php echo $row["phone"]; ?>" required><br>

        <label for="postal" style="font-size: 0.5em">Code postal:</label>
        <input type="text" id="postal" name="postal" value="<?php echo $row["postal"]; ?>" required><br>

        <label for="ville" style="font-size: 0.5em">Ville:</label>
        <input type="text" id="ville" name="ville" value="<?php echo $row["ville"]; ?>" required><br>

        <label for="fileUpload" style="font-size: 0.5em">Photo de profil:</label>
        <input type="file" id="fileUpload" name="photo" value="<?php echo $row["photo"]; ?>" required><br>

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
