<?php
require_once('../HeaderFooter/Admin/Header.php');
?>
<?php
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE STOCK') {
    header("Location: connexion.php");
    exit;
}
?>

<div class="flex">
    <!-- Main Content -->
    <div class="w-[calc(100%-200px)]">
        <div class="p-8">
            <!-- Header Section -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-2xl font-bold">Stock des ingrédients en temps réel</h1>
            </div>

            <!-- Table Section -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table id="ingredientsTable" class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Unitaire (€)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- Les données seront insérées ici par AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function updateStock() {
        fetch('../Actions/get_stock_real_time.php')
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                console.log(data);

                const tbody = document.querySelector("#ingredientsTable tbody");
                tbody.innerHTML = '';

                data.forEach(ingredient => {
                    const row = document.createElement('tr');
                    row.className = 'hover:bg-gray-50';

                    // Image cell
                    const cellImage = document.createElement('td');
                    cellImage.className = 'px-6 py-4 whitespace-nowrap';
                    const img = document.createElement('img');
                    img.src = `../Assets/img/ingredients/${ingredient.image}`;
                    img.alt = ingredient.nom;
                    img.className = 'w-12 h-12 object-cover rounded';
                    cellImage.appendChild(img);

                    // Nom cell
                    const cellNom = document.createElement('td');
                    cellNom.className = 'px-6 py-4 whitespace-nowrap';
                    const nomDiv = document.createElement('div');
                    nomDiv.className = 'text-sm font-medium text-gray-900';
                    nomDiv.textContent = ingredient.nom;
                    cellNom.appendChild(nomDiv);

                    // Quantité cell
                    const cellQuantite = document.createElement('td');
                    cellQuantite.className = 'px-6 py-4 whitespace-nowrap';
                    const quantiteSpan = document.createElement('span');
                    quantiteSpan.className = 'px-2 inline-flex text-xs leading-5 font-semibold rounded-full ' +
                        (ingredient.quantite < 10 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800');
                    quantiteSpan.textContent = ingredient.quantite;
                    cellQuantite.appendChild(quantiteSpan);

                    // Prix unitaire cell
                    const cellPrixUnitaire = document.createElement('td');
                    cellPrixUnitaire.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-500';
                    cellPrixUnitaire.textContent = `${ingredient.prix_unitaire} €`;

                    // Add cells to row
                    row.appendChild(cellImage);
                    row.appendChild(cellNom);
                    row.appendChild(cellQuantite);
                    row.appendChild(cellPrixUnitaire);

                    tbody.appendChild(row);
                });
            })
            .catch(error => console.error('Erreur lors de la récupération des données:', error));
    }

    setInterval(updateStock, 2000);
    updateStock();
</script>

<?php
require_once('../HeaderFooter/Admin/Footer.php');
?>