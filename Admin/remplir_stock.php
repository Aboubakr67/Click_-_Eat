<?php
require('../HeaderFooter/Admin/Header.php');
?>
<?php
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE STOCK') {
    header("Location: connexion.php");
    exit;
}
?>

<h1>Importation de stock CSV</h1>
<!-- Formulaire d'upload -->
<input type="file" id="csvFile" accept=".csv">
<button id="uploadButton" onclick="uploadCSV()">Upload</button>

<!-- Message d'erreur (affiché si le fichier CSV n'est pas valide) -->
<p id="errorMessage" style="color: red; display: none;"></p>

<!-- Tableau pour afficher le contenu du CSV -->
<table id="csvTable" style="display:none;">
    <thead>
        <tr>
            <th>Nom</th>
            <th>Quantité</th>
            <th>Prix Unitaire (€)</th>
        </tr>
    </thead>
    <tbody>
        <!-- Les lignes seront ajoutées dynamiquement -->
    </tbody>
</table>

<!-- Bouton pour envoyer les données à la base de données -->
<button id="sendButton" onclick="sendData()" style="display:none;">Envoyer</button>

<script>
    let csvData = []; // Pour stocker les données du fichier CSV

    // Fonction pour vérifier et uploader le CSV
    function uploadCSV() {
        const fileInput = document.getElementById('csvFile');
        const file = fileInput.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(event) {
            const content = event.target.result;

            // Diviser les lignes et nettoyer chaque ligne
            const lines = content.split('\n').map(line =>
                line.split(';').map(cell => cell.trim().replace('\r', '')) // Nettoyer chaque cellule
            );

            // Récupérer et nettoyer les en-têtes
            const headers = lines[0];

            // Vérifier que les noms des colonnes sont corrects
            if (headers[0] !== 'DESIG' || headers[1] !== 'QTE' || headers[2] !== 'PRIX_UNITE') {
                document.getElementById('errorMessage').textContent = 'Erreur: Le fichier CSV doit contenir les colonnes "DESIG;QTE;PRIX_UNITE"';
                document.getElementById('errorMessage').style.display = 'block';
                return;
            }

            // Initialiser le tableau CSV et masquer les erreurs
            document.getElementById('errorMessage').style.display = 'none';
            document.getElementById('csvTable').style.display = 'table';

            // Nettoyer et exclure la première ligne (les headers)
            csvData = lines.slice(1).filter(line => line.length === 3); // Exclure les lignes incomplètes

            updateTable();
        };

        reader.readAsText(file);
    }

    // Fonction pour mettre à jour le tableau
    function updateTable() {
        const tbody = document.querySelector('#csvTable tbody');
        tbody.innerHTML = ''; // Réinitialiser le contenu du tableau

        csvData.forEach((row, index) => {
            const [nom, quantite, prixUnitaire] = row;

            // Validation des données
            const isQuantityValid = Number.isInteger(parseInt(quantite));
            const isPriceValid = !isNaN(parseFloat(prixUnitaire));

            const tr = document.createElement('tr');

            // Ajouter les cellules
            const tdNom = document.createElement('td');
            tdNom.textContent = nom;

            const tdQuantite = document.createElement('td');
            tdQuantite.textContent = quantite;

            const tdPrixUnitaire = document.createElement('td');
            tdPrixUnitaire.textContent = prixUnitaire;

            tr.appendChild(tdNom);
            tr.appendChild(tdQuantite);
            tr.appendChild(tdPrixUnitaire);

            // Vérifier si l'ingrédient est reconnu et si les formats sont valides
            if (!isQuantityValid || !isPriceValid) {
                tr.style.backgroundColor = 'orange'; // Format incorrect
            } else {
                checkIngredient(nom, tr);
            }

            tbody.appendChild(tr);
        });

        document.getElementById('sendButton').style.display = 'block';
    }

    // Fonction pour vérifier si un ingrédient est dans la base de données
    function checkIngredient(nom, row) {
        fetch(`../Actions/check_ingredient.php?nom=${nom}`)
            .then(response => response.json())
            .then(data => {
                if (data.exists) {
                    row.style.backgroundColor = 'lightgreen'; // Ingrédient reconnu
                } else {
                    row.style.backgroundColor = 'lightcoral'; // Ingrédient non reconnu
                }
            });
    }

    // Fonction pour envoyer les données valides à la base de données
    function sendData() {
        // Filtrer les lignes valides en fonction des critères
        const validData = csvData.filter((row, index) => {
            const rowElement = document.querySelector(`#csvTable tbody tr:nth-child(${index + 1})`);

            // Vérifier la couleur de fond et valider si la quantité est un entier et le prix unitaire est un nombre valide
            const isQuantityValid = Number.isInteger(Number(row[1])); // Vérifie que la quantité est un entier
            const isPriceValid = !isNaN(parseFloat(row[2])) && isFinite(row[2]); // Vérifie que le prix est un nombre valide

            // Vérifie si la ligne est en vert (valide) et si les données sont valides
            if (rowElement.style.backgroundColor === 'lightgreen' && isQuantityValid && isPriceValid) {
                return true; // Ingrédient valide
            } else {
                //rowElement.style.backgroundColor = 'orange'; // Ingrédient invalide
                return false; // Ingrédient invalide
            }
        });

        // Vérifier si validData est vide
        if (validData.length === 0) {
            // Afficher un message si aucun ingrédient valide n'est trouvé
            alert('Aucun ingrédients trouvés dans le fichier CSV');
            return; // Ne pas envoyer la requête
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
require('../HeaderFooter/Admin/Footer.php');
?>