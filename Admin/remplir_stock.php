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
                <h1 class="text-2xl font-bold">Importation de stock CSV</h1>
            </div>

            <!-- Upload Section -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center gap-4">
                    <input type="file"
                        id="csvFile"
                        accept=".csv"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-[#D84315] file:text-white hover:file:bg-[#BF360C]">
                    <button onclick="uploadCSV()"
                        class="px-6 py-2 bg-gradient-to-br from-[#FF8A65] to-[#FF5722] text-white rounded-lg hover:from-[#FF7043] hover:to-[#F4511E] transition-all duration-300">
                        Upload
                    </button>
                </div>

                <!-- Error Message -->
                <div id="errorMessage" class="mt-4 p-4 bg-red-100 text-red-700 rounded-lg hidden"></div>
            </div>

            <!-- Table Section -->
            <div id="csvTable" class="bg-white rounded-lg shadow-sm overflow-hidden hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prix Unitaire (€)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <!-- Les lignes seront ajoutées dynamiquement -->
                    </tbody>
                </table>

                <!-- Send Button -->
                <div class="p-4 bg-gray-50 flex justify-end">
                    <button id="sendButton"
                        onclick="sendData()"
                        class="px-6 py-2 bg-gradient-to-br from-[#FF8A65] to-[#FF5722] text-white rounded-lg hover:from-[#FF7043] hover:to-[#F4511E] transition-all duration-300 hidden">
                        Envoyer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let csvData = [];

    function uploadCSV() {
        const fileInput = document.getElementById('csvFile');
        const file = fileInput.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(event) {
            const content = event.target.result;
            const lines = content.split('\n').map(line =>
                line.split(';').map(cell => cell.trim().replace('\r', ''))
            );
            const headers = lines[0];

            if (headers[0] !== 'DESIG' || headers[1] !== 'QTE' || headers[2] !== 'PRIX_UNITE') {
                document.getElementById('errorMessage').textContent = 'Erreur: Le fichier CSV doit contenir les colonnes "DESIG;QTE;PRIX_UNITE"';
                document.getElementById('errorMessage').classList.remove('hidden');
                return;
            }

            document.getElementById('errorMessage').classList.add('hidden');
            document.getElementById('csvTable').classList.remove('hidden');
            csvData = lines.slice(1).filter(line => line.length === 3);
            updateTable();
        };

        reader.readAsText(file);
    }

    function updateTable() {
        const tbody = document.querySelector('#csvTable tbody');
        tbody.innerHTML = '';

        csvData.forEach((row, index) => {
            const [nom, quantite, prixUnitaire] = row;
            const isQuantityValid = Number.isInteger(parseInt(quantite));
            const isPriceValid = !isNaN(parseFloat(prixUnitaire));

            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50';

            const tdNom = document.createElement('td');
            tdNom.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900';
            tdNom.textContent = nom;

            const tdQuantite = document.createElement('td');
            tdQuantite.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900';
            tdQuantite.textContent = quantite;

            const tdPrixUnitaire = document.createElement('td');
            tdPrixUnitaire.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900';
            tdPrixUnitaire.textContent = prixUnitaire;

            tr.appendChild(tdNom);
            tr.appendChild(tdQuantite);
            tr.appendChild(tdPrixUnitaire);

            if (!isQuantityValid || !isPriceValid) {
                tr.classList.add('bg-orange-500');
            } else {
                checkIngredient(nom, tr);
            }

            tbody.appendChild(tr);
        });

        document.getElementById('sendButton').classList.remove('hidden');
    }

    function checkIngredient(nom, row) {
        fetch(`../Actions/check_ingredient.php?nom=${nom}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    row.classList.add('bg-green-500');
                } else {
                    row.classList.add('bg-red-500');
                }
            });
    }

    function sendData() {
        const validData = csvData.filter((row, index) => {
            const rowElement = document.querySelector(`#csvTable tbody tr:nth-child(${index + 1})`);
            const isQuantityValid = Number.isInteger(Number(row[1]));
            const isPriceValid = !isNaN(parseFloat(row[2])) && isFinite(row[2]);

            return rowElement.classList.contains('bg-green-500') && isQuantityValid && isPriceValid;
        });

        if (validData.length === 0) {
            alert('Aucun ingrédients trouvés dans le fichier CSV');
            return;
        }

        fetch('../Actions/upload_ingredients.php', {
                method: 'POST',
                body: JSON.stringify(validData),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Données envoyées avec succès!');
                } else {
                    alert('Erreur lors de l\'envoi des données');
                }
            });
    }
</script>

<?php
require_once('../HeaderFooter/Admin/Footer.php');
?>