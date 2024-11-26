<?php
require('../HeaderFooter/Admin/Header.php');
?>
<?php
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE STOCK') {
    header("Location: connexion.php");
    exit;
}
?>


<h1>Stock</h1>

<?php
require('../HeaderFooter/Admin/Footer.php');
?>