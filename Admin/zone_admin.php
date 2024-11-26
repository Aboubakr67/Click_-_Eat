<?php
require('../HeaderFooter/Admin/Header.php');
?>
<?php
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}
?>


<h1>Admin</h1>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>