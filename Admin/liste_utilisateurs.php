<?php
require_once('../HeaderFooter/Admin/Header.php');
?>
<?php
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE MANAGEMENT') {
    header("Location: connexion.php");
    exit;
}
require_once('../Actions/zone_admin_repo.php');

$listesUsers = getAllUsers();
?>

<div class="flex">
    <!-- Main Content -->
    <div class="w-[calc(100%-200px)]">
        <div class="p-8">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold">Liste d'utilisateur</h1>
                <a href="create_utilisateur.php" class="px-4 py-2 bg-[#D84315] text-white rounded-lg hover:bg-[#BF360C] transition-colors">
                    Créer un utilisateur
                </a>
            </div>

            <!-- Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg">
                    <?php
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg">
                    <?php
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <!-- Users Table -->
            <?php if (!empty($listesUsers)): ?>
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom et prénom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rôle</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php foreach ($listesUsers as $row): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($row['nom']) ?> <?= htmlspecialchars($row['prenom']) ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?= htmlspecialchars($row['email']) ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            <?= htmlspecialchars($row['role']) ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex space-x-3">
                                            <a href="edit_utilisateur.php?id=<?= htmlspecialchars($row['id']) ?>" class="text-[#D84315] hover:text-[#BF360C]">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                            <a href="delete_utilisateur.php?id=<?= htmlspecialchars($row['id']) ?>"
                                                onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');"
                                                class="text-red-600 hover:text-red-800">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500">
                    <h2>Aucun utilisateur trouvé.</h2>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
require_once('../HeaderFooter/Admin/Footer.php');
?>