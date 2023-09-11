<?php
// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $login = $_POST["login"];
    $prenom = $_POST["prenom"];
    $nom = $_POST["nom"];
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirmPassword"];
    
    // Vérifier si les mots de passe correspondent
    if ($password === $confirmPassword) {
        // Connexion à la base de données
        $host = "localhost";
        $dbname = "cv-crafter";
        $username = "pma";
        $passwordDB = "plomkiplomki";
        
        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $passwordDB);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Insérer les données dans la table utilisateurs
            $query = "INSERT INTO utilisateurs (login, prenom, nom, password) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$login, $prenom, $nom, $password]);
            
            // Redirection vers la page de connexion
            header("Location: connexion.php");
            exit;
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    } else {
        echo "Les mots de passe ne correspondent pas.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
    <link id="style" rel="stylesheet" type="text/css" href="style6.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&display=swap');
  </style>
</head>
<body>
<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <h1>Inscription</h1>
        <label for="login" style="font-size: 0.5em">Login:</label>
        <input type="text" id="login" name="login" required><br>
        
        <label for="prenom" style="font-size: 0.5em">Prénom:</label>
        <input type="text" id="prenom" name="prenom" required><br>
        
        <label for="nom" style="font-size: 0.5em">Nom:</label>
        <input type="text" id="nom" name="nom" required><br>
        
        <label for="password" style="font-size: 0.5em">Mot de passe:</label>
        <input type="password" id="password" name="password" required><br>
        
        <label for="confirmPassword" style="font-size: 0.5em">Confirmer le mot de passe:</label>
        <input type="password" id="confirmPassword" name="confirmPassword" required><br>
        
        <input type="submit" value="S'inscrire">
    </form>
</body>
</html>
