<?php
require_once('../HeaderFooter/Admin/Header.php');
?>

<div class="min-h-screen bg-gray-100 w-full">
    <div class="bg-[#D84315] text-white p-4">
        <h1 class="text-2xl font-bold">Zone Cuisine</h1>
    </div>

    <div class="container mx-auto p-6">
        <div class="space-y-4">
            <!-- Order Item -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center justify-between p-4">
                    <div class="flex-1">
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <span class="text-gray-500 text-sm">Commande</span>
                                <p class="font-medium">#12345</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Client</span>
                                <p class="font-medium">John Doe</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Articles</span>
                                <p class="font-medium">
                                    1x Burger<br>
                                    1x Frites
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">État</span>
                                <p class="font-medium text-yellow-600">En préparation</p>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4">
                        <button class="px-6 py-2 bg-[#D84315] text-white rounded hover:bg-[#BF360C] transition-colors">
                            Terminer
                        </button>
                    </div>
                </div>
            </div>

            <!-- Order Item -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="flex items-center justify-between p-4">
                    <div class="flex-1">
                        <div class="grid grid-cols-4 gap-4">
                            <div>
                                <span class="text-gray-500 text-sm">Commande</span>
                                <p class="font-medium">#12346</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Client</span>
                                <p class="font-medium">Jane Smith</p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Articles</span>
                                <p class="font-medium">
                                    2x Burger<br>
                                    2x Frites<br>
                                    2x Coca
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">État</span>
                                <p class="font-medium text-yellow-600">En attente</p>
                            </div>
                        </div>
                    </div>
                    <div class="ml-4">
                        <button class="px-6 py-2 bg-[#D84315] text-white rounded hover:bg-[#BF360C] transition-colors">
                            Préparer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
