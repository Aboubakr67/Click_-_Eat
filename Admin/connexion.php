<?php
require_once '../Actions/Databases.php';
require_once('../HeaderFooter/Admin/Header.php');


if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    if ($_SESSION['role'] == 'ZONE CUISINE') {
        header("Location: zone_cuisine.php");
    } elseif ($_SESSION['role'] == 'ZONE STOCK') {
        header("Location: zone_stock_reel.php");
    } elseif ($_SESSION['role'] == 'ZONE MANAGEMENT') {
        header("Location: zone_admin.php");
    }
    exit;
}

if (isset($_POST['validate'])) {

    $email = $_POST['email'] ?? '';
    $mot_de_passe = $_POST['mot_de_passe'] ?? '';
    $role = $_POST['role'] ?? '';


    if (!empty($email) && !empty($mot_de_passe) && !empty($role)) {


        $con = connexion();

        $stmt = $con->prepare("SELECT * FROM USERS WHERE email = :email AND role = :role");
        $stmt->execute(['email' => $email, 'role' => $role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        if ($user && password_verify($mot_de_passe, $user['mot_de_passe'])) {


            $_SESSION['auth'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role'] = $user['role'];


            if ($_SESSION['role'] == 'ZONE CUISINE') {
                header("Location: zone_cuisine.php");
            } elseif ($_SESSION['role'] == 'ZONE STOCK') {
                header("Location: zone_stock_reel.php");
            } elseif ($_SESSION['role'] == 'ZONE MANAGEMENT') {
                header("Location: zone_admin.php");
            }
            exit;
        } else {
            $error_message = "Identifiants incorrects ou rôle incorrect.";
        }
    } else {
        $error_message = "Veuillez remplir tous les champs.";
    }
}
?>


<h1>Se connecter</h1>

<?php if (isset($error_message)): ?>
    <p style="color: red;"><?php echo $error_message; ?></p>
<?php endif; ?>

<form action="connexion.php" method="POST">

    <label for="role">Rôle :</label>
    <select name="role" id="role" required>
        <option value="ZONE CUISINE">ZONE CUISINE</option>
        <option value="ZONE STOCK">ZONE STOCK</option>
        <option value="ZONE MANAGEMENT">ZONE MANAGEMENT</option>
    </select><br>


    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required><br>

    <label for="mot_de_passe">Mot de passe :</label>
    <input type="password" id="mot_de_passe" name="mot_de_passe" required><br>

    <button type="submit" name="validate">Se connecter</button>
</form>

<?php
require_once('../HeaderFooter/Admin/Footer.php');
?>