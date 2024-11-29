<?php
require_once('../HeaderFooter/Client/Header.php');
?>

<div class="p-8">
    <div class="bg-[#D84315] text-white p-6 -mx-8 -mt-8 mb-8 flex justify-between items-center">
        <h1 class="text-2xl font-bold">Type de commande</h1>
        <span id="cart-total" class="text-2xl font-bold">0,00 €</span>
    </div>

    <div class="bg-white rounded-2xl p-8 shadow-sm mb-4 min-h-[500px]">
        <div class="flex justify-center items-center gap-8 h-full">
            <div onclick="setOrderTypeAndContinue('SUR_PLACE')"
                class="bg-white rounded-2xl shadow-md p-8 w-64 h-64 flex flex-col items-center justify-center cursor-pointer hover:shadow-lg transition-shadow">
                <div class="w-24 h-24 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-full h-full text-[#D84315]">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-5-9h10v2H7z"/>
                    </svg>
                </div>
                <span class="text-xl font-medium text-center">Sur place</span>
            </div>

            <div onclick="setOrderTypeAndContinue('A_EMPORTER')"
                class="bg-white rounded-2xl shadow-md p-8 w-64 h-64 flex flex-col items-center justify-center cursor-pointer hover:shadow-lg transition-shadow">
                <div class="w-24 h-24 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-full h-full text-[#D84315]">
                        <path d="M18 6h-2c0-2.21-1.79-4-4-4S8 3.79 8 6H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V8c0-1.1-.9-2-2-2zm-8 4c0 .55-.45 1-1 1s-1-.45-1-1V8h2v2zm3-4c0-1.1.9-2 2-2s2 .9 2 2v2h-4V6z"/>
                    </svg>
                </div>
                <span class="text-xl font-medium text-center">A emporter</span>
            </div>
        </div>

        <div class="flex justify-center gap-4 mt-12">
            <button onclick="window.location.href='panier.php'"
                class="px-12 py-3 border-2 border-[#D84315] text-[#D84315] rounded-lg text-lg font-medium hover:bg-gray-50 transition-colors">
                Retour
            </button>
        </div>
    </div>

    <!-- Include mini cart -->
    <?php require_once('panier-mini.php'); ?>
</div>

<script>
    // Debug function to log cart state
    function logCartState() {
        const cart = getCart();
        console.log('Current cart state:', cart);
        if (cart.items && cart.items.length > 0) {
            console.log('Current item:', cart.items[cart.items.length - 1]);
        }
    }

    // Function to set order type and continue
    function setOrderTypeAndContinue(type) {
        const cart = getCart();

        // Set order type for menu items
        if (cart.items) {
            cart.items.forEach(item => {
                item.orderType = type;
            });
        }

        // Set order type for standalone items
        cart.orderType = type;
        cart.step = 'payment';
        saveCart(cart);

        // Proceed to payment
        window.location.href = 'choix_paiment.php';
    }

    document.addEventListener('DOMContentLoaded', () => {
        console.log('DOM loaded, checking cart state');
        logCartState();

        const cart = getCart();
        if (cart.orderType) {
            const selectedDiv = document.querySelector(`[onclick="setOrderTypeAndContinue('${cart.orderType}')"]`);
            if (selectedDiv) {
                selectedDiv.classList.add('ring-2', 'ring-[#D84315]');
            }
        }
    });
</script>

<?php
require_once('../HeaderFooter/Client/Footer.php');
?>
