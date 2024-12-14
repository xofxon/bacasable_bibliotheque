var filterInputs;
document.addEventListener("DOMContentLoaded", function() {
    const h2Element = document.querySelector("h2");
    const buttonContainer = document.querySelector(".button-container");

    function adjustButtonContainerPosition() {
        if (buttonContainer){
            const h2Height = h2Element.offsetHeight;
            buttonContainer.style.top = h2Height + "px";
        }    
    }

    // Ajuster la position des boutons au chargement de la page
    adjustButtonContainerPosition();

    // Réajuster la position lors du redimensionnement de la fenêtre
    window.addEventListener("resize", adjustButtonContainerPosition);
});
