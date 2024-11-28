<?php
require('../HeaderFooter/Client/Header.php');
?>

<div class="p-8">
    <div class="bg-[#D84315] text-white p-6 -mx-8 -mt-8 mb-8 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Mode de paiement</h1>
        <span id="cart-total" class="text-2xl font-bold">0,00 €</span>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm mb-4 min-h-[500px]">
        <div class="flex justify-center items-center gap-8 h-full">
            <div onclick="setPaymentMethod('CB')"
                 class="bg-white rounded-2xl shadow-md p-8 w-64 h-64 flex flex-col items-center justify-center cursor-pointer hover:shadow-lg transition-shadow">
                <div class="w-24 h-24 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-full h-full text-[#D84315]">
                        <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"/>
                    </svg>
                </div>
                <span class="text-xl font-medium text-center">Carte bancaire</span>
            </div>

            <div onclick="setPaymentMethod('ESPECE')"
                 class="bg-white rounded-2xl shadow-md p-8 w-64 h-64 flex flex-col items-center justify-center cursor-pointer hover:shadow-lg transition-shadow">
                <div class="w-24 h-24 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-full h-full text-[#D84315]">
                        <path d="M11.8 10.9c-2.27-.59-3-1.2-3-2.15 0-1.09 1.01-1.85 2.7-1.85 1.78 0 2.44.85 2.5 2.1h2.21c-.07-1.72-1.12-3.3-3.21-3.81V3h-3v2.16c-1.94.42-3.5 1.68-3.5 3.61 0 2.31 1.91 3.46 4.7 4.13 2.5.6 3 1.48 3 2.41 0 .69-.49 1.79-2.7 1.79-2.06 0-2.87-.92-2.98-2.1h-2.2c.12 2.19 1.76 3.42 3.68 3.83V21h3v-2.15c1.95-.37 3.5-1.5 3.5-3.55 0-2.84-2.43-3.81-4.7-4.4z"/>
                    </svg>
                </div>
                <span class="text-xl font-medium text-center">Espèces</span>
            </div>
        </div>
    </div>

    <div class="flex justify-center">
        <a href="panier.php">
            <button class="px-12 py-3 bg-white text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                Voir mon panier
            </button>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const cart = getCart();
    if (cart.paymentMethod) {
        const selectedDiv = document.querySelector(`[onclick="setPaymentMethod('${cart.paymentMethod}')"]`);
        if (selectedDiv) {
            selectedDiv.classList.add('ring-2', 'ring-[#D84315]');
        }
    }
});
</script>

<?php
require('../HeaderFooter/Client/Footer.php');
?>
