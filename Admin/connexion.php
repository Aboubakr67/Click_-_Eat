<?php
require_once '../Actions/Databases.php';
require('../HeaderFooter/Admin/Header.php');


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

<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <form action="connexion.php" method="POST" class="max-w-md w-full bg-white p-6 rounded-lg shadow-md">
        <h1 class="text-3xl font-bold text-center mb-6">Se connecter</h1>

        <?php if (isset($error_message)): ?>
            <p class="text-red-500 text-center"><?php echo $error_message; ?></p>
        <?php endif; ?>

        <div class="mb-4">
            <label for="role" class="block text-gray-700">Rôle :</label>
            <select name="role" id="role" required class="mt-1 h-10 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-orange-500">
                <option value="ZONE CUISINE">ZONE CUISINE</option>
                <option value="ZONE STOCK">ZONE STOCK</option>
                <option value="ZONE MANAGEMENT">ZONE MANAGEMENT</option>
            </select>
        </div>

        <div class="mb-4">
            <label for="email" class="block text-gray-700">Email :</label>
            <input type="email" id="email" name="email" required class="mt-1 h-10 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-orange-500">
        </div>

        <div class="mb-4">
            <label for="mot_de_passe" class="block text-gray-700">Mot de passe :</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe" required class="mt-1 h-10 block w-full border border-gray-300 rounded-md shadow-sm focus:ring focus:ring-orange-500">
        </div>

        <button type="submit" name="validate" class="w-full bg-orange-500 text-white font-bold py-2 rounded hover:bg-orange-600">Se connecter</button>
    </form>
</div>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>