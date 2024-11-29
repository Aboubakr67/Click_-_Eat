<?php
require('../HeaderFooter/Admin/Header.php');

if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE STOCK') {
    header("Location: connexion.php");
    exit;
}
?>

<div class="flex">
    <!-- Main Content -->
    <div class="w-[calc(100%-200px)]">
        <div class="p-8">
            <h1 class="text-2xl font-bold mb-8">Ingrédients utilisés</h1>

            <!-- Date Selection Form -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
                <form id="dateForm" class="flex items-center gap-6">
                    <div>
                        <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1">Date de début</label>
                        <input type="date" id="startDate" name="startDate" 
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                    </div>
                    <div>
                        <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1">Date de fin</label>
                        <input type="date" id="endDate" name="endDate"
                               class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#D84315] focus:border-transparent">
                    </div>
                    <button type="submit" 
                            class="mt-6 px-6 py-2 bg-gradient-to-br from-[#FF8A65] to-[#FF5722] text-white rounded-lg hover:from-[#FF7043] hover:to-[#F4511E] transition-all duration-300">
                        Rechercher
                    </button>
                    <a href="#" id="downloadCsv" 
                       class="mt-6 px-6 py-2 border border-[#D84315] text-[#D84315] rounded-lg hover:bg-[#D84315] hover:text-white transition-colors">
                        Télécharger le CSV
                    </a>
                </form>
            </div>

            <!-- Results Table -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom Ingrédient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité utilisé</th>
                        </tr>
                    </thead>
                    <tbody id="ingredientsTable" class="divide-y divide-gray-200">
                        <!-- Les données seront insérées ici par JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('dateForm');
    const startDate = document.getElementById('startDate');
    const endDate = document.getElementById('endDate');
    const downloadBtn = document.getElementById('downloadCsv');

    // Set default dates
    const today = new Date();
    startDate.value = today.toISOString().split('T')[0];
    endDate.value = today.toISOString().split('T')[0];

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        fetchData();
    });

    // Handle CSV download
    downloadBtn.addEventListener('click', function(e) {
        e.preventDefault();
        downloadCsv();
    });

    function fetchData() {
        const start = startDate.value;
        const end = endDate.value;

        // Determine if it's a single day or period
        const isSingleDay = start === end;
        const endpoint = isSingleDay ? 'get_daily_usage.php' : 'get_period_usage.php';

        fetch(`../Actions/${endpoint}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                startDate: start,
                endDate: end
            })
        })
        .then(response => response.json())
        .then(data => updateTable(data))
        .catch(error => console.error('Error:', error));
    }

    function updateTable(data) {
        const tbody = document.getElementById('ingredientsTable');
        tbody.innerHTML = '';

        data.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50';

            const nameCell = document.createElement('td');
            nameCell.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900';
            nameCell.textContent = item.nom;

            const quantityCell = document.createElement('td');
            quantityCell.className = 'px-6 py-4 whitespace-nowrap text-sm text-gray-900';
            quantityCell.textContent = item.quantite;

            row.appendChild(nameCell);
            row.appendChild(quantityCell);
            tbody.appendChild(row);
        });
    }

    function downloadCsv() {
        const start = startDate.value;
        const end = endDate.value;
        const isSingleDay = start === end;
        const endpoint = isSingleDay ? 'get_daily_usage_csv.php' : 'get_period_usage_csv.php';

        window.location.href = `../Actions/${endpoint}?startDate=${start}&endDate=${end}`;
    }

    // Initial data load
    fetchData();
});
</script>

<?php require('../HeaderFooter/Admin/Footer.php'); ?>
