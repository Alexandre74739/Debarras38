/**
 * Menu de l'en-tête sous 960 px : un tiroir qui glisse depuis la gauche.
 *
 * Pose le bouton de menu dans la carte, le haut du tiroir (logo et bouton de
 * fermeture) et le voile qui assombrit la page. Le script ne connaît que des
 * classes : ce que le client ajoute ou retire dans l'en-tête suit sans rien
 * écrire ici. La mise en forme est dans assets/scss/layout/_entete.scss.
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

  const etroit = matchMedia("(max-width: 59.99rem)");
  const creer = (balise, classe) => Object.assign(document.createElement(balise), { className: classe });

  const bascule = creer("button", "d38-entete__bascule");
  const fermer = creer("button", "d38-entete__fermer");
  const haut = creer("div", "d38-entete__tiroir-haut");
  const voile = creer("div", "d38-entete__voile");
  const estOuvert = () => entete.classList.contains("est-ouvert");

  // Le logo de la carte, repris en haut du tiroir. La copie est décorative :
  // le lecteur d'écran et le clavier ne la rencontrent pas deux fois.
  const logo = entete.querySelector(".d38-logo")?.cloneNode(true);

  if (logo) {
    logo.setAttribute("aria-hidden", "true");
    logo.querySelectorAll("a").forEach((lien) => (lien.tabIndex = -1));
    haut.append(logo);
  }

  const regler = (ouvert) => {
    entete.classList.toggle("est-ouvert", ouvert);
    document.documentElement.classList.toggle("d38-menu-ouvert", ouvert);
    bascule.setAttribute("aria-expanded", ouvert);
  };

  const ouvrir = () => {
    regler(true);
    fermer.focus();
  };

  const refermer = (rendreLaMain = true) => {
    regler(false);

    if (rendreLaMain) {
      bascule.focus();
    }
  };

  volet.id ||= "d38-menu";
  volet.setAttribute("aria-label", "Menu");

  bascule.type = fermer.type = "button";
  bascule.setAttribute("aria-label", "Ouvrir le menu");
  bascule.setAttribute("aria-controls", volet.id);
  fermer.setAttribute("aria-label", "Fermer le menu");
  regler(false);

  haut.append(fermer);
  volet.prepend(haut);
  volet.before(bascule);
  volet.after(voile);

  bascule.addEventListener("click", ouvrir);
  fermer.addEventListener("click", () => refermer());
  voile.addEventListener("click", () => refermer());

  // Un lien suivi referme le tiroir : la plupart mènent à une section de la page.
  volet.addEventListener("click", (evenement) => {
    if (evenement.target.closest("a") && estOuvert()) {
      refermer(false);
    }
  });

  document.addEventListener("keydown", (evenement) => {
    if (!estOuvert()) {
      return;
    }

    if (evenement.key === "Escape") {
      refermer();
      return;
    }

    // Le clavier reste dans le tiroir tant qu'il est ouvert.
    if (evenement.key === "Tab") {
      const focalisables = [...volet.querySelectorAll("a[href], button")].filter((element) => element.tabIndex >= 0);
      const premier = focalisables[0];
      const dernier = focalisables[focalisables.length - 1];

      if (evenement.shiftKey && document.activeElement === premier) {
        evenement.preventDefault();
        dernier.focus();
      } else if (!evenement.shiftKey && document.activeElement === dernier) {
        evenement.preventDefault();
        premier.focus();
      }
    }
  });

  // Au-delà de 960 px, le tiroir n'existe plus : on ne le laisse pas ouvert.
  etroit.addEventListener("change", () => etroit.matches || regler(false));
})();
