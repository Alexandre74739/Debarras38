/**
 * Menu de l'en-tête sous 960 px.
 *
 * Pose le bouton de menu dans la carte et déplie le volet qui contient les
 * liens et les boutons. Le script ne connaît que des classes : ce que le client
 * ajoute ou retire dans l'en-tête suit sans rien écrire ici. La mise en forme
 * est dans assets/scss/layout/_entete.scss.
 */
(() => {
  const entete = document.querySelector(".d38-entete");
  const volet = entete?.querySelector(".d38-entete__volet");

  // Lu par le script de l'en-tête du document (inc/setup.php).
  window.d38Entete = true;

  if (!volet) {
    document.documentElement.classList.remove("d38-js");
    return;
  }

  const bascule = document.createElement("button");
  const estOuvert = () => entete.classList.contains("est-ouvert");

  const regler = (ouvert) => {
    entete.classList.toggle("est-ouvert", ouvert);
    bascule.setAttribute("aria-expanded", ouvert);
    bascule.setAttribute(
      "aria-label",
      ouvert ? "Fermer le menu" : "Ouvrir le menu",
    );
  };

  volet.id ||= "d38-menu";
  bascule.type = "button";
  bascule.className = "d38-entete__bascule";
  bascule.setAttribute("aria-controls", volet.id);
  regler(false);
  volet.before(bascule);

  bascule.addEventListener("click", () => regler(!estOuvert()));

  // Un lien suivi referme le menu : la plupart mènent à une section de la page.
  volet.addEventListener("click", (evenement) => {
    if (evenement.target.closest("a")) {
      regler(false);
    }
  });

  document.addEventListener("click", (evenement) => {
    if (estOuvert() && !entete.contains(evenement.target)) {
      regler(false);
    }
  });

  document.addEventListener("keydown", (evenement) => {
    if (evenement.key === "Escape" && estOuvert()) {
      regler(false);
      bascule.focus();
    }
  });
})();
