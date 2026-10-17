# Debarras38

Site WordPress de Debarras38, entreprise de débarras et de nettoyage en Isère (Said Ben Fakir, Grenoble). Identité visuelle et site conçus par Fablioo (Alexandre Perez). Ce fichier reprend la charte graphique v1.0 (octobre 2026) : elle fait autorité sur tout ce qui est produit ici.

## Projet

- Installation Local : `http://debarras38.local`, WordPress 7.1, PHP 8.2.
- Thème sur mesure : `wp-content/themes/debarras38/` (thème à blocs, sans extension requise).
- Pas de Node sur la machine. Le CSS est écrit en Sass et compilé avec Dart Sass autonome.

### Structure du thème

```
debarras38/
├── style.css            en-tête du thème uniquement
├── theme.json           palette, polices, tailles : source des réglages de l'éditeur
├── functions.php        charge les fichiers de inc/
├── inc/
│   ├── setup.php        supports, scripts, styles, catégorie de compositions, page d'accueil
│   ├── coordonnees.php  page de réglages « Debarras38 » + liaisons de blocs (téléphone, e-mail…)
│   ├── seo.php          title, meta, Open Graph, JSON-LD (LocalBusiness, FAQPage)
│   ├── motif.php        injecte le dessin du grand visuel (tableau-mare.svg)
│   └── formulaire.php   formulaire de devis : code court, envoi, e-mail, demandes enregistrées
├── templates/           front-page, page, index, 404
├── parts/               header, footer (renvoient vers les compositions du même nom)
├── patterns/            une composition par section de page
└── assets/
    ├── scss/            sources Sass (abstracts, base, layout, components, sections)
    ├── css/main.css     CSS compilé, ne pas modifier à la main
    ├── js/motion.js     bibliothèque Motion (Framer Motion, version sans React), hébergée localement
    ├── js/animations.js toutes les animations du site
    ├── js/entete.js     tiroir de navigation de l’en-tête, sous 960 px
    ├── js/formulaire.js envoi du formulaire sans recharger la page
    ├── js/editeur.js    éditeur : format « Souligner d’une vague » du titre principal
    ├── fonts/           WOFF2
    └── img/             logos fournis, dessins et formes
```

### Structure de la page d'accueil

Elle suit le plan classique d'une page d'atterrissage. Ordre des compositions (`D38_SECTIONS_ACCUEIL` dans `inc/setup.php`) :

| Rôle | Composition | Contenu |
|---|---|---|
| Navigation | `header` | Carte flottante : logo principal à toutes les largeurs, menu, bouton « Demander un devis ». Sous 960 px, le menu et les boutons passent dans un tiroir qui glisse depuis la gauche, par-dessus la page assombrie (`entete.js`) ; sans JavaScript, ils restent dépliés dans la carte. Pictogrammes maison (`icone-*.svg`). |
| Accroche + image | `hero` | Premier écran, en pleine hauteur de fenêtre (en-tête compris). À gauche : un très grand titre (jusqu'à 72 px, au-delà des 56 px de la charte, choix d'Alexandre) en Encre avec le lieu en Bleu canard, « Grenoble » souligné d'une vague Jaune caneton qui se trace au chargement, le slogan en sous-titre, une phrase courte, le bouton principal. À droite : une forme ronde (`.d38-blob`) d'où sort une image, sans vagues ; en dessous sous 960 px. Le canard (`canard-vert-eau.svg`), sur une forme Bleu canard, y tient la place des futures photos, de préférence détourées. Les tailles et les espaces suivent aussi la hauteur de l'écran (unités `svh`) pour que tout tienne dans la fenêtre. Tout le texte, titre compris, arrive en cascade au chargement. Le grand visuel de la mare (`.d38-tableau`) n'y est plus, son code reste dans le thème. |
| Bandeau | `atouts` | Bandeau de réassurance (`#atouts`) : une carte Bleu canard qui flotte entre l'accroche et les services, comme la carte de l'en-tête. Elle commence sous le premier écran : rien n'en dépasse sur l'accroche. Quatre atouts en quelques mots, chacun avec un pictogramme maison ; ni titre ni bouton. Une colonne sur téléphone, deux sur tablette, quatre en ligne à partir de 80rem : jamais trois, qui laisserait un atout seul. De l'eau Vert d'eau passe derrière la carte (`.d38-flotte`), refermée par la vague des services. Tient la place d'un « ils nous font confiance », sans rien inventer. La liste des communes n'est plus dans la page. |
| Services | `services` | Trois rangées en quinconce (`.d38-rangee`) : photo (bloc Image `.d38-visuel`), titre, paragraphe, bouton. La rangée cave/grenier/garage n’a pas de bouton. |
| Appel | `appel` | Titre, phrase et bouton principal sur une carte Bleu canard à ombre franche Encre ; deux bandes Vert d'eau sortent par le coin haut droit (`sillage-coin.svg`). Ni vagues ni canard, choix d'Alexandre. À l'arrivée, la carte se soulève (son ombre s'allonge), les bandes entrent par le coin, puis titre mot par mot, phrase, bouton. |
| Avant, pendant, après | `cas` | Section Vert d'eau, vague animée en haut, coulure en bas (la section Déroulement qui suit n'a donc pas de vague). Un même chantier en trois cartes par ligne (`.d38-cartes--trio`) : un titre (`h3`), la photo (bloc Image `.d38-visuel`, `photo-a-venir.svg` en attendant celles du client) avec sa légende « Avant », « Pendant » ou « Après » posée en étiquette dans son coin (pas de surtitre), puis un paragraphe. Elle remplace les situations (succession, déménagement, location), qui redisaient les services. La carte « Pendant », au milieu, est mise en avant (`.d38-carte--avant`) : Bleu canard, plus haute que ses voisines, un canard de sa couleur posé sur son bord haut, qui flotte doucement sur place (`d38-canard-flotte`, `base/_animations.scss`) ; sous 60rem, il se tourne vers la carte. Sous le titre de la section, un paragraphe d'introduction (`.d38-chapo`). Le bec aux deux yeux du A du logo (`bec-canard.svg`, Vert d'eau) est dans son coin haut droit ; elle passe en tête sous 60rem. Arrivée propre : les cartes sont distribuées comme un jeu (la carte en avant se pose, les autres sortent de derrière elle), Alexandre a écarté pour le canard : nager d'un bout à l'autre de la carte (« trop peu naturel »), barboter tête dans la carte (« étrange »). Une carte peut finir par une liste à coches et un lien calé en bas. Chaque carte prend son fond dans l'éditeur. |
| Déroulement | `etapes` | Étapes numérotées, que le canard parcourt au défilement. |
| Questions | `faq` | Blocs Détails, repris dans les données structurées. |
| Appel final | `contact` | Numéro en grand et formulaire de devis (`#devis`). |
| Pied de page | `footer` | Logo secondaire, slogan, coordonnées. |

Le modèle prévoit aussi des logos clients, des témoignages et des chiffres : il n'y en a pas de réels, donc rien n'est inventé. Les communes, les situations et le déroulement tiennent ces places.

### Page introuvable

`templates/404.html` et `sections/_erreur.scss`. Sans cadre : sur un écran Crème, un grand « 404 » Bleu canard, le titre en Encre, la phrase et le bouton, tout centré. La section remplit l'écran sous l'en-tête, le texte centré dedans. Sur les côtés, deux paires de bandes Bleu canard qui entrent par un bord et en ressortent (`sillage-bord.svg`) : en bas à gauche (en miroir) et au milieu du bord droit, sur environ la moitié de la hauteur (`--bandes`). Sous 80rem, le texte prend la largeur : les bandes de la bande d'appel (`sillage-coin.svg`) passent dans les coins bas gauche et haut droit. Le pied de page la referme par sa vague Encre (`.d38-pied-vague` sur la partie de modèle). Alexandre a écarté : la carte flottant sur l'eau, la carte Bleu canard aux bandes de coin, les bandes traversant l'écran, les bandes sur toute la hauteur, la vague du bas avec le canard, les pictogrammes flottants.

### Direction artistique du site

Décisions d'Alexandre Perez, à ne pas rouvrir sans lui :

- Pas de surtitres au-dessus des titres, pas de petites barres ni d'équerres décoratives.
- Pas de formes ajoutées à la charte (soleil, nuages, cercles) : elles font « IA » et bon marché. Seuls servent le canard, la vague, les deux bandes et le D-canard. Deux exceptions, voulues par Alexandre : la forme qui découpe l'image de l'accroche (`blob.svg`) et la coulure en bas de la section Avant, pendant, après (`.d38-bord-coulure`, `coulure.svg` dessiné par Alexandre, `components/_coulure.scss` : la couleur de la section déborde sur la suivante en un bord ondulé d'où tombe une grosse goutte, qui coule à son arrivée).
- Les vagues bougent : en haut des sections (`.d38-bord-vague`), dans les encadrés (`.d38-vagues`). Une seule mécanique, `poserOnde()` dans `animations.js` et `components/_ondes.scss`.
- Les visuels des rangées sont de vraies photos, posées par le client (bloc Image `.d38-visuel`, cadre 4/3 recadré automatiquement). En attendant, ils affichent `photo-a-venir.svg` (D-canard sur Vert d’eau).
- Le Jaune caneton ne sert qu'au bouton principal. Une exception, voulue par Alexandre : le soulignement en vague du titre principal (`.d38-souligne`).
- Pictogrammes dessinés pour le site, jamais Lucide ni une autre bibliothèque. Grille de 24, aplats pleins avec découpes (l'œil du canard est un trou, comme dans le D-canard) et bandes d'épaisseur constante (trait de 2,25 à 2,75, bouts ronds) ; l'ondulation du sillage remplace certaines droites (repère, devis, seau, menu : trois vagues de même longueur). Posés en masque CSS, ils prennent la couleur voulue.
- Au survol, une carte (`.d38-carte`) se soulève et pose une ombre douce et floue, jamais franche.
- Ombre franche Encre (décalée, sans flou) : seulement sur la bande d'appel. Étendue aux autres cartes, elle tire le site vers le néo-brutalisme, un style tout fait qui n'est pas celui de la marque.
- Le site écrit « débarrassage », jamais « débarras » (choix d'Alexandre, octobre 2026), h1, titres, textes et SEO compris. Seul le nom Debarras38 garde « debarras ».

### Couleurs du contenu

Un bloc qui reçoit une couleur de fond règle seul la couleur de ce qu'il contient, par les variables `--fond`, `--texte`, `--titre` et `--lien` (`base/_racine.scss`). Le fond le plus proche l'emporte. Ne pas colorer un titre ou un lien à la main : changer le fond du bloc.

### Formulaire de devis

- Code court `[d38_formulaire]`, placé dans la composition `contact`.
- Chaque demande est envoyée à l'e-mail du menu « Debarras38 » et enregistrée dans Debarras38 → Demandes.
- Anti-robots : champ piège, délai minimal de 3 s, 5 envois par heure et par adresse. Pas de jeton : il expirerait sur une page mise en cache.
- En local, les e-mails sont captés par Mailpit (onglet « Tools » de Local). En production, prévoir un envoi SMTP : sans lui, les e-mails d'un WordPress arrivent souvent en indésirables, surtout vers Hotmail.

### Pièges connus

- WordPress met en cache la liste des compositions. Après avoir ajouté un fichier dans `patterns/` : `wp eval 'wp_get_theme()->delete_pattern_cache();'`.
- La page d'accueil est créée une seule fois, à l'activation du thème. Modifier une composition ne modifie pas la page déjà créée.
- Les déplacements liés au défilement passent par `auDefilement()` dans `animations.js` (propriété CSS `translate`), pas par `scroll( animate() )`, qui jouait l'animation dès le chargement.
- Motion réécrit la valeur finale d'une animation juste après `.finished` : retirer un style animé à l'image suivante (`requestAnimationFrame`), comme dans `devoiler()`.
- WordPress 7.1 donne la classe `wp-block-paragraph` à chaque paragraphe : un sélecteur `p:not([class])` ne cible plus rien (c'est le cas de la règle de largeur dans `layout/_section.scss`).
- `focus()` sur un bouton de l'en-tête fait remonter la page (il est sous la marge de `scroll-padding-top`) : `focus({ preventScroll: true })`.

### Commandes

```
sass wp-content/themes/debarras38/assets/scss/main.scss wp-content/themes/debarras38/assets/css/main.css --style=compressed --no-source-map
```

Ajouter `--watch` pendant le développement. Installer Sass : binaire autonome (github.com/sass/dart-sass/releases) ou `npm i -g sass`.

### Conventions de code

- Préfixe `d38-` pour les classes CSS, `d38_` pour les fonctions PHP, `debarras38/` pour les compositions.
- Nommage en français, comme dans la charte (`--bleu-canard`, `.d38-carte`, `d38_coord()`).
- Sass : un fichier partiel par composant, `@use` uniquement, aucune couleur en dur hors de `abstracts/_tokens.scss`.
- Une section de page = un bloc Groupe `section.d38-section` + une composition dans `patterns/`. Le fond se règle avec la couleur de fond native du bloc ; le texte s'adapte tout seul.
- Les animations ciblent des classes, jamais un contenu précis : une section ajoutée par le client est animée sans rien coder. Chaque rôle a son arrivée (`ARRIVEES` dans `animations.js`) : la carte des atouts remonte de l'eau, la bande d'appel se soulève (son ombre s'allonge), les cartes et les questions se dévoilent depuis un bord, les étapes s'allument au passage du canard, le numéro s'écrit, le formulaire se déroule. Les titres arrivent mot par mot ; le fondu montant ne reste qu'au texte courant. Si `prefers-reduced-motion` est actif, les vagues sont posées mais immobiles et rien d'autre ne bouge. Le contenu reste visible sans JavaScript.
- Boutons : trois styles du bloc Bouton, au choix dans l'éditeur (principal, contour, plein). Un bloc ne recolore pas ses boutons ; il peut seulement les resserrer avec `--bouton-hauteur`, `--bouton-marge-bloc` et `--bouton-marge-ligne`, comme l'en-tête.
- Les coordonnées (téléphone, e-mail, adresse) ne s'écrivent jamais en dur : elles viennent de `d38_coord()` ou de la liaison de blocs `debarras38/coordonnees`.- Les textes du site suivent le chapitre « Ton » ci-dessous. Aucune promesse chiffrée (délai, tarif, gratuité) sans validation du client.

### Ce que le client peut faire seul

- Modifier un texte : Pages → Accueil, cliquer dans le texte.
- Souligner des mots du titre principal d'une vague : les sélectionner, puis flèche de la barre d'outils → « Souligner d’une vague ». Le bouton n'apparaît que dans un titre de niveau 1 ; un second clic retire le soulignement.
- Ajouter une section : bouton « + » → Compositions → « Debarras38 ».
- Supprimer ou déplacer une section : vue en liste de l'éditeur.
- Changer téléphone, e-mail, adresse, horaires, titre et description SEO : menu « Debarras38 » de l'administration.
- Lire les demandes de devis reçues : Debarras38 → Demandes.

---

## Charte graphique

### 1. La marque

Debarras38 vide et nettoie des caves, des greniers et des maisons entières en Isère. Toute l'image tient en un animal et une phrase : un canard, et « On passe dans tous les coins. »

- **L'idée.** Le canard fait « coin-coin », l'entreprise passe dans tous les coins. Double sens : le travail (rien n'est laissé derrière un meuble ou au fond d'une cave) et la proximité (l'entreprise du coin ; « 38 » est le numéro du département).
- Le canard est dans les lettres : le D abrite un canard de profil, le A porte un bec et deux yeux, le S final est un canard debout.
- **Personnalité**, deux qualités à égalité :
  - *Chaleureuse* : le canard, les formes rondes, le jaune, le sourire du slogan. On s'adresse à des particuliers, souvent dans un moment chargé (déménagement, succession, maison à rendre).
  - *Sérieuse* : le bleu canard profond, des textes sobres et très lisibles, des informations concrètes. Le client confie ses clés : inspirer confiance avant de faire sourire.
- **Le nom** s'écrit **Debarras38** : un seul mot, majuscule initiale, sans accent, sans espace. Jamais « Débarras 38 », « DEBARRAS38 » ni « Debarras 38 ». Les capitales sont réservées au logo. (Le nom commun « débarras » garde son accent.)

### 2. Le logo

Quatre versions. On ne le redessine pas, on ne le retape pas au clavier, on ne change pas ses couleurs. Toujours partir des fichiers fournis.

| Version | Quand | Fichier (dans `assets/img/`) | Largeur min. écran | Largeur min. imprimé | En dessous |
|---|---|---|---|---|---|
| Logo principal | Dès qu'il y a de la largeur : en-tête du site, véhicule, bâche, devis, factures | `logo-primary.png` (lettres Bleu canard, « 38 » Encre ; PNG transparent, 1000 px) | 120 px | 35 mm | logo réduit |
| Logo secondaire | Formats carrés ou en hauteur : textile, sac, autocollant, photo de profil | `logo-secondary.svg` (lettres Crème, « 38 » Jaune caneton) | 90 px | 25 mm | logo réduit |
| Logo réduit | Petits espaces : en-tête du site sur téléphone, signature d'e-mail, coin de bâche | `reduce-logo.svg` (D-canard + « 38 ») | 48 px | 15 mm | favicon |
| Favicon | Très petits formats : onglet, icône d'application, pastille | `d-canard.svg` (D-canard seul) | 16 px | 6 mm | ne pas descendre |

- **Zone de protection** : marge vide égale au quart de la hauteur du logo (X), sur les quatre côtés, pour les quatre versions. Ni texte, ni image, ni bord de page dedans. Seule exception : sur un objet, le logo secondaire peut sortir du format si le canard du D reste entier.
- **Couleurs selon le fond** :

| Fond | Lettres | « 38 » |
|---|---|---|
| Plume ou Crème | Bleu canard | Encre |
| Vert d'eau | Bleu canard | Encre |
| Bleu canard | Crème | Jaune caneton |
| Encre | Crème | Jaune caneton |
| Clair, une seule couleur | Encre | Encre |
| Sombre, une seule couleur | Crème | Crème |

- **Interdits** : étirer ou écraser ; couleurs hors palette ; logo jaune sur fond clair ; logo bleu sur fond sombre ; incliner ; ombre, contour ou effet. Les lettres du logo ne servent jamais à écrire autre chose.

### 3. Les couleurs

Six couleurs, pas une de plus.

| Nom | Hex | RVB | CMJN (indicatif) | Rôle |
|---|---|---|---|---|
| Bleu canard | `#0B5D66` | 11 · 93 · 102 | 89 · 9 · 0 · 60 | Couleur de la marque. Logo, titres, grands aplats. |
| Jaune caneton | `#FFC53D` | 255 · 197 · 61 | 0 · 23 · 76 · 0 | Accent. « 38 » sur fond sombre, boutons, un seul élément à faire ressortir par support. |
| Plume | `#F6F1E4` | 246 · 241 · 228 | 0 · 2 · 7 · 4 | Fond chaud des imprimés et des sections du site. |
| Encre | `#0B2A30` | 11 · 42 · 48 | 77 · 13 · 0 · 81 | Textes courants, « 38 » sur fond clair, fonds sombres. |
| Vert d'eau | `#BFE3DD` | 191 · 227 · 221 | 16 · 0 · 3 · 11 | Fonds secondaires, encadrés, textes discrets sur fond sombre. |
| Crème | `#FDFFF7` | 253 · 255 · 247 | 1 · 0 · 3 · 0 | Fond principal du site et du papier, lettres sur fond sombre. |

**Proportions** : fonds clairs (Crème, Plume, Vert d'eau) 60 %, Bleu canard 30 %, Jaune caneton 10 %. Le jaune reste rare.

**Associations texte / fond** (contraste WCAG 2.1 ; 4,5 minimum pour un texte courant, 3 pour un gros titre) :

| Association | Contraste | Usage |
|---|---|---|
| Encre sur Crème | 15,0 | Tous les textes |
| Encre sur Plume | 13,4 | Tous les textes |
| Bleu canard sur Crème | 7,5 | Tous les textes |
| Bleu canard sur Plume | 6,7 | Textes courants et titres |
| Encre sur Vert d'eau | 11,0 | Tous les textes |
| Bleu canard sur Vert d'eau | 5,5 | Textes courants et titres |
| Crème sur Bleu canard | 7,5 | Tous les textes |
| Jaune caneton sur Bleu canard | 4,8 | Textes courants et titres |
| Vert d'eau sur Bleu canard | 5,5 | Textes courants et titres |
| Crème sur Encre | 15,0 | Tous les textes |
| Jaune caneton sur Encre | 9,6 | Tous les textes |
| Encre sur Jaune caneton | 9,6 | Tous les textes |
| Jaune caneton sur Plume | 1,4 | **Interdit** |

Le Jaune caneton ne sert jamais à écrire sur un fond clair, et on ne compose jamais un paragraphe en jaune, même sur fond sombre.

### 4. Les typographies

Deux polices libres (SIL OFL, Google Fonts), hébergées avec le site en WOFF2, `font-display: swap`.

- **Gabarito** : titres, slogans, boutons. Regular, SemiBold, Bold, ExtraBold. Repli : Trebuchet MS.
- **Atkinson Hyperlegible** : textes courants, légendes, formulaires. Regular, Italique, Bold, Bold italique. Repli : Verdana. Choisie pour sa lisibilité (clientèle en partie âgée).

| Niveau | Police et graisse | Imprimé | Écran |
|---|---|---|---|
| Titre principal | Gabarito ExtraBold | 28 à 36 pt | 40 à 56 px |
| Titre de section | Gabarito Bold | 16 à 20 pt | 28 à 36 px |
| Sous-titre | Gabarito SemiBold | 12 à 14 pt | 20 à 24 px |
| Slogan | Gabarito Bold, capitales | 11 à 14 pt | 16 à 20 px |
| Texte courant | Atkinson Hyperlegible Regular | 10,5 à 11 pt | 18 px |
| Mise en avant | Atkinson Hyperlegible Bold | 10,5 à 11 pt | 18 px |
| Légende, mention | Atkinson Hyperlegible Regular | 8,5 à 9 pt | 15 px |

**Composition** : texte aligné à gauche, jamais justifié. Capitales réservées au slogan et à quelques mots très courts. 55 à 75 caractères par ligne de texte courant.

### 5. Le motif : le sillage du canard

Deux formes : la **vague** (aplat au bord ondulé qui coupe le support en deux zones de couleur) et les **bandes** (rubans d'épaisseur constante, toujours par deux, qui suivent le mouvement de la vague et sortent du format).

- Deux couleurs de la palette, jamais trois. Jamais de Jaune caneton dans le motif.
- Combinaisons : Bleu canard sur Crème (référence), Bleu canard sur Encre (fond sombre), Vert d'eau sur Bleu canard (plus doux).
- La vague part d'un bord et sort par un autre. Elle ne flotte pas au milieu.
- Les bandes vont par deux, épaisseur constante d'un bout à l'autre.
- Le motif garde ses proportions : on ne l'étire pas.
- Il couvre au plus la moitié du support et reste hors de la zone de protection du logo.
- Un texte se pose sur un aplat uni, à côté des bandes, **jamais par-dessus**.

### 6. Le ton et les slogans

Debarras38 parle comme un artisan qu'on recommande à ses voisins : simplement, avec le sourire, sans promettre ce qu'il ne tiendra pas. L'humour est dans le slogan, le reste est clair et concret.

- **Slogan** : « On passe dans tous les coins. » Toujours en entier, sans variante. Sous le logo : capitales, Gabarito Bold (`ON PASSE DANS TOUS LES COINS`). En titre : minuscules avec point final.
- **Accroches secondaires** : une seule par support, jamais à côté du slogan.

| Accroche | Ce qu'elle dit | Où |
|---|---|---|
| Le débarras du coin. | La proximité | Véhicule, annuaire, page d'accueil |
| Un débarras sans y laisser de plumes. | Un prix honnête | Devis, page des tarifs |
| Aucun recoin ne nous échappe. | Le soin du nettoyage | Page du service de nettoyage |
| Mare du bazar ? | Le soulagement du client | Affiche, prospectus |

- **La voix** : on vouvoie, phrases courtes, on nomme les choses (« cave », « grenier », pas « espaces à désencombrer »).

| On écrit | On évite |
|---|---|
| Nous vidons, nous trions, nous nettoyons. | Nous proposons des solutions globales de désencombrement. |
| Cave, grenier, garage, maison entière. | Tous types de locaux et d'espaces. |
| Appelez le 06 79 37 20 77. | N'hésitez pas à nous contacter pour toute demande d'information. |
| Nous intervenons à Grenoble et en Isère. | Nous bénéficions d'une large couverture territoriale. |

- **Deux précautions** :
  - Un débarras suit souvent un décès ou un départ en maison de retraite : dans un échange direct avec une famille, pas de jeux de mots, ton simple et prévenant.
  - Une promesse chiffrée (délai, tarif, gratuité d'un devis) ne figure sur un support qu'après validation par l'entreprise.

### 7. Les applications

- **Carte de visite** (85 × 55 mm). Recto : logo réduit et slogan en capitales sur fond Crème ; en bas sur aplat Bleu canard, adresse, téléphone, e-mail en Crème, puis QR code vers le site (à générer quand l'adresse du site sera fixée). Verso : logo principal inversé sur Bleu canard, encadré par les bandes.
- **Bâche et grands formats** : trois niveaux d'information au plus (logo réduit + slogan, lieux d'intervention, information pratique). Le téléphone y figure toujours, en Gabarito Bold. La vague sépare la zone du logo de celle du message.
- **Textile et objets** : tenue de travail (logo principal inversé sur la poitrine, bandes sur les manches) ; sac (logo secondaire en très grand, coupé par les bords, canard entier) ; autocollant (D-canard seul, Crème sur Encre).

### 8. Le site web

```css
:root {
  --bleu-canard:   #0B5D66;
  --jaune-caneton: #FFC53D;
  --plume:         #F6F1E4;
  --encre:         #0B2A30;
  --vert-eau:      #BFE3DD;
  --creme:         #FDFFF7;

  --police-titres: "Gabarito", "Trebuchet MS", sans-serif;
  --police-textes: "Atkinson Hyperlegible", Verdana, sans-serif;
}

body {
  background: var(--creme);
  color: var(--encre);
  font: 400 1.125rem/1.6 var(--police-textes);
}
```

- **Texte** : 18 px, interligne 1,6. Titres : colonne « Écran » du chapitre 4. Sur téléphone, le titre principal descend à 40 px, le texte courant ne change pas.
- **Bouton principal** : fond Jaune caneton, texte Encre, Gabarito Bold 18 px. **Un seul par écran** : l'action attendue (demande de devis ou appel).
- **Bouton secondaire** : contour 2 px et texte Bleu canard sur fond clair, Crème sur fond sombre.
- **Forme** : coins de 12 px, hauteur d'au moins 48 px.
- **Survol** : le principal fonce légèrement ; le secondaire se remplit de sa couleur de contour.
- **Navigation au clavier** : contour 3 px décalé de 3 px, Encre sur fond clair, Crème sur fond sombre.
- **Lien dans un texte** : Bleu canard, souligné, le soulignement ne disparaît jamais.
- **Cartes et images** : coins de 20 px.
- **Contrastes** : seules les associations ≥ 4,5 servent aux textes courants. Aucune information transmise par la couleur seule (un message d'erreur porte aussi un mot ou un pictogramme).
- **Favicon** : D-canard en Crème sur un carré Bleu canard aux coins arrondis. En dessous de 32 px, l'œil du canard est agrandi.

| Fichier | Taille | Rôle |
|---|---|---|
| `favicon.svg` | vectoriel | Onglet des navigateurs récents |
| `favicon.ico` | 32 px | Navigateurs plus anciens |
| `apple-touch-icon.png` | 180 px | Écran d'accueil iPhone |
| `icon-192.png`, `icon-512.png` | 192 et 512 px | Android, résultats de recherche |

### 9. Contacts

- **L'entreprise** : Debarras38, Said Ben Fakir, 9 rue du Trident, 38100 Grenoble, 06 79 37 20 77, debarras38@hotmail.fr
- **La conception** : Fablioo, Alexandre Perez, identité visuelle et site web, fablioo.fr

---

## SEO

Cible : particuliers de Grenoble et de l'Isère qui cherchent un débarras ou un nettoyage.

- Requêtes principales : débarras Grenoble, entreprise de débarras Grenoble, entreprise de nettoyage Grenoble, débarras Isère (38). Le site écrit « débarrassage » (voir Direction artistique) : ces requêtes ne figurent plus mot pour mot dans les titres.
- Requêtes de service : débarras maison, débarras appartement, débarras cave, débarras grenier, débarras garage, vide maison, débarras succession, nettoyage après débarras, nettoyage fin de bail, devis débarras.
- Requêtes locales : débarras + commune (Échirolles, Saint-Martin-d'Hères, Fontaine, Meylan, Voiron, Vizille…).
- Un seul `h1` par page, des `h2` qui portent un service et un lieu, sans bourrage : la voix de la charte passe avant la densité de mots-clés.
- Chaque rangée de services et chaque carte de situation a son `h3` : une requête, un titre.
- Données structurées générées par `inc/seo.php` : l'entreprise (réglages « Debarras38 »), les communes desservies et les services (lus dans la page d'accueil), les questions (blocs Détails). Une commune ou une rangée ajoutée dans l'éditeur y entre sans autre réglage. Tout se désactive si Yoast, Rank Math ou SEOPress est installé.
