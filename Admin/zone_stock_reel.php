<?php
require_once('../HeaderFooter/Admin/Header.php');
?>
<?php
if (!isset($_SESSION['auth']) || $_SESSION['role'] !== 'ZONE STOCK') {
    header("Location: connexion.php");
    exit;
}
?>


<h1>Stock des ingrédients en temps réel</h1>

<table id="ingredientsTable">
    <thead>
        <tr>
            <th>Image</th>
            <th>Nom</th>
            <th>Quantité</th>
            <th>Prix Unitaire (€)</th>
        </tr>
    </thead>
    <tbody>
        <!-- Les données seront insérées ici par AJAX -->
    </tbody>
</table>

<script>
    // Fonction pour mettre à jour la vue
    function updateStock() {
        // Effectuer la requête AJAX pour obtenir les données
        fetch('../Actions/get_stock_real_time.php')
            .then(response => response.json())
            .then(data => {
                // Vérifier si des erreurs ont été renvoyées
                if (data.error) {
                    console.error(data.error);
                    return;
                }

                console.log(data);


                // Récupérer le body du tableau
                const tbody = document.querySelector("#ingredientsTable tbody");
                tbody.innerHTML = ''; // Réinitialiser le contenu

                // Ajouter une ligne pour chaque ingrédient
                data.forEach(ingredient => {
                    const row = document.createElement('tr');

                    // Ajouter la cellule pour l'image
                    const cellImage = document.createElement('td');
                    const img = document.createElement('img');
                    img.src = `../Assets/img/ingredients/${ingredient.image}`; // Remplace par le chemin réel des images
                    img.alt = ingredient.nom;
                    img.style.width = '50px'; // Redimensionner l'image si nécessaire
                    img.style.height = '50px'; // Redimensionner l'image si nécessaire
                    cellImage.appendChild(img);

                    // Créer les cellules pour chaque colonne
                    const cellNom = document.createElement('td');
                    cellNom.textContent = ingredient.nom;

                    const cellQuantite = document.createElement('td');
                    cellQuantite.textContent = ingredient.quantite;

                    const cellPrixUnitaire = document.createElement('td');
                    cellPrixUnitaire.textContent = ingredient.prix_unitaire;


                    // Ajouter les cellules à la ligne
                    row.appendChild(cellImage);
                    row.appendChild(cellNom);
                    row.appendChild(cellQuantite);
                    row.appendChild(cellPrixUnitaire);

                    // Ajouter la ligne au corps du tableau
                    tbody.appendChild(row);
                });
            })
            .catch(error => console.error('Erreur lors de la récupération des données:', error));
    }

    // Mettre à jour toutes les 2 secondes
    setInterval(updateStock, 2000);

    // Initialiser la première mise à jour immédiatement
    updateStock();
</script>

<?php
require_once('../HeaderFooter/Admin/Footer.php');
?>