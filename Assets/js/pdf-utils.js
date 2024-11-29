// Fonction pour convertir un élément HTML en PDF
window.downloadAsPDF = function(element) {
    // Configuration pour html2pdf
    const opt = {
        margin: 10,
        filename: `facture-${new Date().toISOString()}.pdf`,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            letterRendering: true
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait'
        }
    };

    // Masquer temporairement le bouton de téléchargement s'il existe
    const downloadBtn = element.querySelector('button');
    if (downloadBtn) {
        const originalDisplay = downloadBtn.style.display;
        downloadBtn.style.display = 'none';

        // Générer le PDF
        html2pdf()
            .set(opt)
            .from(element)
            .save()
            .then(() => {
                // Restaurer le bouton après la génération
                downloadBtn.style.display = originalDisplay;
            });
    } else {
        // Si pas de bouton, générer directement le PDF
        html2pdf()
            .set(opt)
            .from(element)
            .save();
    }
};
