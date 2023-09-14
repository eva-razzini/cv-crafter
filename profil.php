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
    $query = "SELECT login, prenom, nom, password, phone, postal, ville, photo FROM utilisateurs WHERE login = ?";
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
        $newphoto = $_FILES["photo"];
    

           // Vérifie si le fichier a été uploadé sans erreur.
            $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
            $filename = $_FILES["photo"]["name"];
            $filetype = $_FILES["photo"]["type"];
            $filesize = $_FILES["photo"]["size"];

            var_dump("holaaa",$_FILES["photo"]["name"]);

            // Vérifie l'extension du fichier
            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if(!array_key_exists($ext, $allowed)) die("Erreur : Veuillez sélectionner un format de fichier valide.");

            // Vérifie la taille du fichier - 5Mo maximum
            $maxsize = 5 * 1024 * 1024;
            if($filesize > $maxsize) die("Error: La taille du fichier est supérieure à la limite autorisée.");

                    // Mettre à jour les informations de l'utilisateur dans la base de données
            $updateQuery = "UPDATE utilisateurs SET login = ?, prenom = ?, nom = ?, password = ?, phone = ?, postal = ?, ville = ?, photo = ?  WHERE login = ?";
            $updateStmt = $conn->prepare($updateQuery);
            $updateStmt->execute([$newLogin, $newPrenom, $newNom, $newPassword, $newphone, $newpostal, $newville, $_FILES["photo"]["name"], $_SESSION["login"]]);

            // Mettre à jour le login de l'utilisateur dans la variable de session
            $_SESSION["login"] = $newLogin;

        
                    move_uploaded_file($_FILES["photo"]["tmp_name"], "upload/" . $_FILES["photo"]["name"]);
                    echo "Votre fichier a été téléchargé avec succès.";
                    // Redirection vers la page de profil mise à jour
            header("Location: profil.php");
            echo "<img src='upload/".$row['photo']."'>";

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
    <form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>" enctype="multipart/form-data">
        <h1>Profil</h1>
        <!-- Display the user's current login and other fields here -->
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

        <!-- Display the default profile photo -->
        <?php //if (empty($row["photo"])) : ?>
            <!-- <img src="upload/profil_default.jpg" alt="Default Profile Photo" width="150"> -->
        <?php //else : ?>
            <?php //echo '<img src="upload/'.$row["photo"].'" width="128" height="117"> </img>' ?>
            <?php

    ?>

    <img src=upload/<?=$row['photo']?> height="430">

              <?php //endif; ?>

        <!-- Allow the user to upload a new photo -->
        <label for="fileUpload" style="font-size: 0.5em">Photo de profil:</label>
        <input type="file" id="fileUpload" name="photo" required><br>

        <label for="password" style="font-size: 0.5em">Nouveau mot de passe:</label>
        <input type="text" id="password" name="password"><br>

        <input type="submit" value="Enregistrer les modifications">
    </form>
    <br>
    <form method="GET" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <input type="hidden" name="logout" value="true">
        <input type="submit" value="Se déconnecter">
    </form>

      <!-- Bouton pour créer un nouveau CV -->
      <a href="nouveaucv.php">Créer un nouveau CV</a>

    <!-- Bouton pour voir un CV enregistré -->
    <a href="voircv.php">Voir un CV enregistré</a>

</body>
</html>
