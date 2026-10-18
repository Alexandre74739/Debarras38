/**
 * Animations du site, avec Motion (la bibliothèque de Framer Motion, sans React).
 *
 * Tout part de classes CSS, jamais d'un contenu précis : une section ajoutée
 * dans l'éditeur est animée sans rien écrire ici. Les états de départ sont dans
 * assets/scss/base/_animations.scss.
 *
 * Sommaire :
 *   1. Outils        défilement, découpe des titres, création d'éléments
 *   2. Vagues        ondes en haut des sections, sur les bords, dans les encadrés
 *   3. Apparitions   sections, rangées, titres mot par mot
 *   4. Scènes        le grand visuel, les étapes
 *   5. Interactions  boutons, cartes, questions
 */
( () => {
	const racine = document.documentElement;

	if ( ! window.Motion ) {
		racine.classList.remove( 'd38-anime' );
		return;
	}

	// Faux si le visiteur a demandé moins de mouvement : les vagues sont alors
	// posées mais immobiles, et rien d'autre ne bouge.
	const ANIME = racine.classList.contains( 'd38-anime' );

	window.d38Pret = true;

	const { animate, inView, scroll, stagger, hover, press } = window.Motion;

	const DOUX = [ 0.22, 1, 0.36, 1 ];
	const RESSORT = { type: 'spring', stiffness: 420, damping: 28 };
	const SANS_FIN = { repeat: Infinity, ease: 'easeInOut' };
	const EN_BOUCLE = { repeat: Infinity, ease: 'linear' };

	// Blocs dont on anime les enfants un par un plutôt que le bloc entier.
	const GROUPES = '.d38-entete-section, .d38-faq__titre, .d38-contact__texte';

	/* 1. Outils ----------------------------------------------------------- */

	/**
	 * Appelle `rappel` avec l'avancement du défilement sur `cible`, borné de 0 à 1.
	 *
	 * Les déplacements liés au défilement passent par la propriété CSS
	 * `translate`, pour ne pas se mêler aux transformations que Motion anime.
	 */
	const auDefilement = ( cible, offset, rappel ) =>
		scroll( ( avance ) => rappel( Math.min( 1, Math.max( 0, avance ) ) ), { target: cible, offset } );

	const creer = ( classes, parent ) => {
		const element = document.createElement( 'span' );

		element.className = classes;
		element.setAttribute( 'aria-hidden', 'true' );
		parent.append( element );

		return element;
	};

	/**
	 * Enveloppe chaque mot d'un titre dans un <span>, sans toucher à ses balises.
	 */
	const decouperMots = ( titre ) => {
		const textes = [];
		const parcours = document.createTreeWalker( titre, NodeFilter.SHOW_TEXT );

		while ( parcours.nextNode() ) {
			textes.push( parcours.currentNode );
		}

		textes.forEach( ( texte ) => {
			const fragment = document.createDocumentFragment();

			// Les espaces insécables (avant « : », « ? »…) ne séparent pas les
			// mots : la ponctuation reste collée au mot qui la précède.
			texte.textContent.split( /([^\S  ]+)/ ).forEach( ( morceau ) => {
				if ( ! morceau.trim() ) {
					fragment.append( morceau );
					return;
				}

				const mot = document.createElement( 'span' );
				mot.className = 'd38-mot';
				mot.textContent = morceau;
				fragment.append( mot );
			} );

			texte.replaceWith( fragment );
		} );

		return [ ...titre.querySelectorAll( '.d38-mot' ) ];
	};

	/* 2. Vagues ----------------------------------------------------------- */

	/**
	 * Pose une onde dans `hote` et la fait couler sans fin.
	 *
	 * La nappe glisse d'une ondulation exactement : la boucle est sans couture.
	 * Le mouvement est recalculé quand la taille de l'onde change.
	 *
	 * @param {Element} hote     Élément qui reçoit l'onde.
	 * @param {string}  position « haut » (modificateur .d38-onde--haut).
	 * @param {number}  vitesse  En pixels par seconde. Négative : sens inverse.
	 */
	const poserOnde = ( hote, position, vitesse ) => {
		const onde = creer( `d38-onde d38-onde--${ position }`, hote );
		const nappe = creer( 'd38-onde__nappe', onde );
		let coulee;

		if ( ! ANIME ) {
			return onde;
		}

		new ResizeObserver( () => {
			const pas = nappe.offsetWidth - onde.offsetWidth;

			if ( pas <= 0 ) {
				return;
			}

			coulee?.stop();
			coulee = animate( nappe, { x: vitesse > 0 ? [ 0, -pas ] : [ -pas, 0 ] }, { duration: pas / Math.abs( vitesse ), ...EN_BOUCLE } );
		} ).observe( onde );

		return onde;
	};

	/**
	 * Bord supérieur d'une section : la vague avance doucement.
	 */
	const ondulerBords = () => {
		document.querySelectorAll( '.d38-bord-vague' ).forEach( ( section, rang ) => {
			poserOnde( section, 'haut', rang % 2 ? -22 : 22 );
			section.classList.add( 'est-ondule' );
		} );

	};

	/**
	 * Bord inférieur en coulure : les gouttes coulent quand le bas de la
	 * section arrive à l'écran.
	 */
	const coulerBords = () => {
		document.querySelectorAll( '.d38-bord-coulure' ).forEach( ( section ) => {
			const coulure = creer( 'd38-coulure', section );

			section.classList.add( 'a-coule' );

			if ( ! ANIME ) {
				return;
			}

			coulure.style.transform = 'scaleY(0.3)';
			inView(
				coulure,
				() => {
					animate( coulure, { scaleY: [ 0.3, 1 ] }, { duration: 2.2, ease: DOUX } );
				},
				{ margin: '0px 0px -10% 0px' }
			);
		} );
	};

	/**
	 * Encadrés : deux nappes d'eau qui ondulent en sens contraires.
	 */
	const remplirEncadres = () => {
		document.querySelectorAll( '.d38-vagues' ).forEach( ( encadre ) => {
			poserOnde( creer( 'd38-vague d38-vague--fond', encadre ), 'haut', 30 );
			poserOnde( creer( 'd38-vague d38-vague--devant', encadre ), 'haut', -44 );
		} );

		// Une seule nappe sous ce qui flotte (.d38-flotte) : la carte du bandeau.
		document.querySelectorAll( '.d38-flotte' ).forEach( ( section ) => poserOnde( creer( 'd38-vague d38-vague--fond', section ), 'haut', 26 ) );	};

	/* 3. Apparitions ------------------------------------------------------ */

	/**
	 * Éléments à révéler dans une section, dans l'ordre de lecture.
	 */
	const elementsDe = ( section ) =>
		[ ...section.children ]
			.filter( ( element ) => ! element.matches( '.d38-onde, .d38-vague, .d38-coulure, .d38-rangees, .d38-sillage' ) )
			.flatMap( ( element ) => ( element.matches( GROUPES ) ? [ ...element.children ] : [ element ] ) );

	/**
	 * Fait apparaître des éléments en cascade, chacun à sa manière (arriver).
	 */
	const reveler = ( elements, pas = 0.08, debut = 0 ) => {
		elements.forEach( ( element, rang ) => arriver( element, debut + rang * pas ) );
	};

	/**
	 * Arrivée d'un élément. Un bloc qui a la sienne (ARRIVEES) la joue ; sinon
	 * un titre arrive mot par mot, un texte en montant légèrement.
	 */
	const arriver = ( element, depart ) => {
		const propre = Object.keys( ARRIVEES ).find( ( selecteur ) => element.matches( selecteur ) );

		if ( propre ) {
			ARRIVEES[ propre ]( element, depart );
			return;
		}

		if ( element.matches( 'h1, h2, h3' ) ) {
			const mots = decouperMots( element );

			// Le titre a pu être masqué en entier en attendant (rangées) :
			// ce sont désormais ses mots qui portent l'apparition.
			mots.forEach( ( mot ) => ( mot.style.opacity = 0 ) );
			element.style.removeProperty( 'opacity' );
			animate( mots, { opacity: 1, y: [ '0.7em', '0em' ], rotate: [ 4, 0 ] }, { duration: 0.75, ease: DOUX, delay: stagger( 0.045, { startDelay: depart } ) } );
			return;
		}

		element.style.opacity = 0;
		animate( element, { opacity: 1, y: [ 32, 0 ] }, { duration: 0.85, ease: DOUX, delay: depart } );
	};

	/**
	 * Dévoile un élément depuis un bord, par une découpe qui s'ouvre. La
	 * découpe finit 1rem au-delà de l'élément, pour ne jamais couper son
	 * contour de focus, puis elle est retirée. Motion réécrit la valeur finale
	 * juste après la fin de l'animation : on attend l'image suivante.
	 */
	const devoiler = ( element, depart, cache, duree = 1 ) => {
		element.style.clipPath = cache;

		// Renvoie une promesse tenue une fois la découpe retirée.
		return animate( element, { clipPath: [ cache, 'inset(-1rem -1rem -1rem -1rem)' ] }, { duration: duree, ease: DOUX, delay: depart } ).finished.then(
			() => new Promise( ( fin ) => requestAnimationFrame( () => {
				element.style.removeProperty( 'clip-path' );
				fin();
			} ) )
		);
	};

	const DEPUIS_LA_GAUCHE = 'inset(-1rem 100% -1rem -1rem)';
	const DEPUIS_LE_HAUT = 'inset(-1rem -1rem 100% -1rem)';

	/**
	 * Arrivées propres à un rôle, par classe. Une section ajoutée dans
	 * l'éditeur qui reprend ces blocs les reçoit sans rien coder.
	 */
	const ARRIVEES = {
		// Le bandeau des atouts remonte à la surface de l'eau, en se redressant ;
		// ses atouts glissent ensuite un à un.
		'.d38-bandeau__carte': ( carte, depart ) => {
			const atouts = carte.querySelectorAll( 'li' );

			carte.style.opacity = 0;
			atouts.forEach( ( atout ) => ( atout.style.opacity = 0 ) );
			animate( carte, { opacity: 1, y: [ 56, 0 ], rotate: [ -1.5, 0 ] }, { duration: 1, ease: DOUX, delay: depart } );
			animate( atouts, { opacity: 1, x: [ -16, 0 ] }, { duration: 0.6, ease: DOUX, delay: stagger( 0.1, { startDelay: depart + 0.45 } ) } );
		},

		// Page introuvable : le « 404 » remonte en grandissant un peu, avant
		// le titre, la phrase et le bouton.
		'.d38-erreur__code': ( code, depart ) => {
			code.style.opacity = 0;
			animate( code, { opacity: 1, y: [ 48, 0 ], scale: [ 0.9, 1 ] }, { duration: 1, ease: DOUX, delay: depart } );
		},

		// La bande d'appel remonte en grandissant un peu. Ses bandes entrent
		// par le coin (CSS, .est-vu), puis le titre, la phrase et le bouton.
		'.d38-appel': ( carte, depart ) => {
			carte.style.opacity = 0;
			animate( carte, { opacity: 1, y: [ 32, 0 ], scale: [ 0.97, 1 ] }, { duration: 0.9, ease: DOUX, delay: depart } );
			reveler( [ ...carte.querySelectorAll( '.d38-appel__texte > *, .wp-block-button' ) ], 0.15, depart + 0.35 );
		},

		// Situations : les cartes sont distribuées comme un jeu. La carte mise
		// en avant se pose la première ; les autres sortent de derrière elle,
		// inclinées, et filent à leur place. Le canard flotte sur son bord
		// (CSS, base/_animations.scss).
		'.d38-cartes--trio': ( cartes, depart ) => {
			const avant = cartes.querySelector( '.d38-carte--avant' ) ?? cartes.firstElementChild;
			const centre = ( carte ) => {
				const cadre = carte.getBoundingClientRect();

				return [ cadre.left + cadre.width / 2, cadre.top + cadre.height / 2 ];
			};
			const [ x, y ] = centre( avant );

			avant.style.opacity = 0;
			animate( avant, { opacity: 1 }, { duration: 0.3, delay: depart } );
			animate( avant, { y: [ 64, 0 ], scale: [ 0.88, 1 ] }, { type: 'spring', stiffness: 220, damping: 20, delay: depart } );

			[ ...cartes.querySelectorAll( '.d38-carte' ) ]
				.filter( ( carte ) => carte !== avant )
				.forEach( ( carte, rang ) => {
					const [ cx, cy ] = centre( carte );
					const cote = Math.sign( cx - x ) || ( rang % 2 ? 1 : -1 );
					const delai = depart + 0.35 + rang * 0.1;

					carte.style.opacity = 0;
					animate( carte, { opacity: 1 }, { duration: 0.25, delay: delai } );
					animate( carte, { x: [ x - cx, 0 ], y: [ y - cy, 0 ], rotate: [ -10 * cote, 0 ], scale: [ 0.85, 1 ] }, { type: 'spring', stiffness: 160, damping: 19, delay: delai } );
				} );
		},

		// Autres cartes : chacune se déplie depuis la gauche, l'une après l'autre.
		'.d38-cartes': ( cartes, depart ) => {
			cartes.querySelectorAll( '.d38-carte' ).forEach( ( carte, rang ) => devoiler( carte, depart + rang * 0.15, DEPUIS_LA_GAUCHE, 0.9 ) );
		},

		// Déroulement : rien ne monte. Le fil se trace (CSS), et chaque étape
		// s'allume quand le canard arrive à sa hauteur (suivreEtapes).
		'.d38-etapes': ( etapes, depart ) => {
			etapes.style.opacity = 0;
			animate( etapes, { opacity: 1 }, { duration: 0.6, delay: depart } );
		},

		// Questions : elles tombent d'en haut l'une après l'autre et
		// s'empilent, avec un léger rebond à l'arrivée.
		'.d38-questions': ( questions, depart ) => {
			const cartes = [ ...questions.children ];

			cartes.forEach( ( carte ) => ( carte.style.opacity = 0 ) );
			animate( cartes, { opacity: 1 }, { duration: 0.3, delay: stagger( 0.12, { startDelay: depart } ) } );
			animate( cartes, { y: [ -48, 0 ] }, { type: 'spring', stiffness: 320, damping: 20, delay: stagger( 0.12, { startDelay: depart } ) } );
		},

		// Demande de devis : le numéro s'écrit comme sur la bâche, le
		// formulaire se déroule comme une feuille.
		'.d38-numero': ( numero, depart ) => devoiler( numero, depart, DEPUIS_LA_GAUCHE, 0.9 ),
		// Le canard, posé au-dessus de la carte, serait coupé par la découpe :
		// il s'y pose une fois la carte déroulée (« est-pose »).
		'.d38-contact__formulaire': ( formulaire, depart ) =>
			devoiler( formulaire, depart + 0.1, DEPUIS_LE_HAUT, 1.1 ).then( () => formulaire.classList.add( 'est-pose' ) ),
	};

	/**
	 * Sections : jouées une fois, à l'entrée dans l'écran.
	 */
	const animerSection = ( section ) => {
		inView(
			section,
			() => {
				reveler( elementsDe( section ) );
				section.classList.add( 'est-vu' );
			},
			{ margin: '0px 0px -15% 0px' }
		);
	};

	/**
	 * Rangées en quinconce : le visuel se dévoile depuis son bord, le texte suit.
	 */
	const animerRangees = () => {
		document.querySelectorAll( '.d38-rangee' ).forEach( ( rangee, rang ) => {
			const visuel = rangee.querySelector( '.d38-visuel' );
			const textes = [ ...rangee.querySelectorAll( '.d38-rangee__texte > *' ) ];
			const depuisLaDroite = rang % 2 === 1 && matchMedia( '(min-width: 60rem)' ).matches;
			const cache = depuisLaDroite ? 'inset(0% 0% 0% 100% round 20px)' : 'inset(0% 100% 0% 0% round 20px)';

			textes.forEach( ( element ) => ( element.style.opacity = 0 ) );

			if ( visuel ) {
				visuel.style.clipPath = cache;
			}

			inView(
				rangee,
				() => {
					if ( visuel ) {
						animate( visuel, { clipPath: [ cache, 'inset(0% 0% 0% 0% round 20px)' ] }, { duration: 1.1, ease: DOUX } );
						auDefilement( rangee, [ 'start end', 'end start' ], ( avance ) => ( visuel.style.translate = `0 ${ 28 - 56 * avance }px` ) );
					}

					reveler( textes, 0.1 );
				},
				{ margin: '0px 0px -22% 0px' }
			);
		} );
	};

	/* 4. Scènes ----------------------------------------------------------- */

	/**
	 * Le grand visuel : l'eau coule sur deux plans, le canard se balance, les
	 * bandes se tracent. Au défilement, le canard file et les bandes glissent.
	 */
	const animerTableau = ( hero ) => {
		const tableau = hero.querySelector( '.d38-tableau' );
		const canard = hero.querySelector( '.d38-tableau__canard' );

		if ( ! tableau || ! canard ) {
			return;
		}

		const nage = hero.querySelector( '.d38-tableau__nage' );
		const bandes = hero.querySelector( '.d38-tableau__bandes' );
		const apparu = 'inset(0% 0% 0% 0% round 20px)';

		tableau.style.opacity = 0;
		animate( tableau, { opacity: 1, clipPath: [ 'inset(100% 0% 0% 0% round 20px)', apparu ] }, { duration: 1.2, ease: DOUX, delay: 0.35 } );

		// Une période du dessin de l'eau mesure 720 unités : la boucle est sans couture.
		animate( '.d38-tableau__eau--fond', { x: [ 0, -720 ] }, { duration: 18, ...EN_BOUCLE } );
		animate( '.d38-tableau__eau--devant', { x: [ -720, 0 ] }, { duration: 11, ...EN_BOUCLE } );

		animate( canard, { y: [ 0, -12, 0 ], rotate: [ -2.5, 2.5, -2.5 ] }, { duration: 3.6, ...SANS_FIN } );
		animate( '.d38-tableau .d38-trace', { strokeDashoffset: [ 1, 0 ] }, { duration: 1.8, ease: DOUX, delay: stagger( 0.2, { startDelay: 1 } ) } );

		auDefilement( tableau, [ 'start 30%', 'end start' ], ( avance ) => {
			nage.style.translate = `${ -260 * avance }px 0`;
			bandes.style.translate = `0 ${ -60 * avance }px`;
		} );
	};

	/**
	 * Haut de page : joué au chargement. Le titre principal arrive mot par mot,
	 * le reste du texte à sa suite. L'image arrive en tournant légèrement, puis
	 * remonte un peu au défilement.
	 */
	const animerHero = ( hero ) => {
		const visuel = hero.querySelector( '.d38-hero__visuel' );

		if ( visuel ) {
			visuel.style.opacity = 0;
		}

		reveler( [ ...hero.querySelectorAll( '.d38-hero__texte > *' ) ], 0.12 );
		animerTableau( hero );
		hero.classList.add( 'est-vu' );

		if ( visuel ) {
			animate( visuel, { opacity: 1, scale: [ 0.9, 1 ], rotate: [ -6, 0 ] }, { duration: 1.1, ease: DOUX, delay: 0.25 } );
			// L'image monte du fond de la forme : son masque la découpe au ras du contour.
			animate( visuel.querySelectorAll( '.d38-blob img' ), { y: [ '55%', '0%' ] }, { duration: 1.4, ease: DOUX, delay: 0.45 } );
			auDefilement( hero, [ 'start start', 'end start' ], ( avance ) => ( visuel.style.translate = `0 ${ -40 * avance }px` ) );
		}
	};

	/**
	 * Déroulement : le canard avance le long du fil pendant le défilement, et
	 * chaque étape s'allume quand il arrive à sa hauteur.
	 */
	const suivreEtapes = () => {
		document.querySelectorAll( '.d38-etapes' ).forEach( ( etapes ) => {
			const liste = [ ...etapes.children ];

			auDefilement( etapes, [ 'start 75%', 'end 55%' ], ( avance ) => {
				etapes.style.setProperty( '--avance', avance );
				liste.forEach( ( etape, rang ) => etape.classList.toggle( 'est-passee', avance >= rang / liste.length - 0.02 ) );
			} );
		} );
	};

	/* 5. Interactions ----------------------------------------------------- */

	const animerInteractions = () => {
		press( '.wp-block-button__link, .d38-formulaire button', ( bouton ) => {
			animate( bouton, { scale: 0.96 }, RESSORT );
			return () => animate( bouton, { scale: 1 }, RESSORT );
		} );

		hover( '.d38-carte', ( carte ) => {
			animate( carte, { y: -6 }, RESSORT );
			return () => animate( carte, { y: 0 }, RESSORT );
		} );

		// Un atout se soulève ; ses couleurs s'échangent en CSS (components/_atouts.scss).
		hover( '.d38-atouts li', ( atout ) => {
			animate( atout, { y: -4 }, RESSORT );
			return () => animate( atout, { y: 0 }, RESSORT );
		} );

		document.querySelectorAll( '.d38-questions details' ).forEach( ( question ) => {
			question.addEventListener( 'toggle', () => {
				if ( question.open ) {
					animate( question.querySelectorAll( ':scope > :not(summary)' ), { opacity: [ 0, 1 ], y: [ -8, 0 ] }, { duration: 0.35, ease: DOUX } );
				}
			} );
		} );
	};

	/**
	 * Sillages : des paires de bandes Bleu canard (motif de la charte) qui
	 * entrent par un bord de la section et en ressortent. Une section en reçoit
	 * une par classe « d38-sillage-<placement> » ; la position et la taille
	 * sont dans components/_sillages.scss. Chaque bande se trace depuis son
	 * bord quand la section arrive à l'écran (base/_animations.scss).
	 *
	 * Les secondes bandes sont la première décalée d'une distance constante :
	 * les deux restent parallèles, d'épaisseur constante.
	 */
	const FORMES = {
		// Entre par le bord haut, ressort par le bord droit.
		coin: {
			vue: [ 240, 120 ],
			traces: [ 'M60 -10C80 34 124 56 176 58S236 76 250 90', 'M100 -24C120 12 160 30 208 32S260 46 274 56' ],
		},
		// Le coin en trois fois plus grand, avec le même écart entre les
		// bandes : pour un grand coin, sans écarter les bandes.
		vaste: {
			vue: [ 720, 360 ],
			traces: [
				'M180 -30C240 102 372 168 528 174C684 180 708 228 750 270',
				'M216 -47C223 -36 240 -3 255 16C270 35 288 51 307 65C326 79 348 91 371 101C394 111 419 118 446 124C472 130 501 131 530 134C558 137 590 138 614 143C639 148 659 154 677 162C695 169 710 179 723 188C736 197 745 208 755 217C764 226 774 238 778 242',
			],
		},
		// Entre par le bord droit, fait une boucle et y ressort.
		bord: {
			vue: [ 200, 300 ],
			traces: [ 'M215 18C95 40 55 125 115 158S205 235 215 285', 'M215 62C130 78 100 132 150 158S210 222 215 245' ],
		},
		// Entre par le bord droit, s'en écarte en un large arc et y ressort.
		anse: {
			vue: [ 200, 360 ],
			traces: [ 'M215 0C90 70 80 270 215 360', 'M231 28C222 36 193 56 180 74C167 92 158 115 153 138C149 160 148 186 152 209C156 233 165 257 178 278C192 298 224 324 233 333' ],
		},
		// Entre par le bord gauche, descend en pente douce, ressort par le bas.
		longue: {
			vue: [ 300, 180 ],
			traces: [
				'M-15 30C70 25 120 60 150 95C180 130 230 140 260 195',
				'M-13 62C-6 62 15 62 28 63C40 65 51 68 61 71C71 74 79 79 87 83C95 88 102 93 109 99C115 104 119 109 126 116C133 123 142 132 151 139C159 146 168 151 175 156C183 162 191 166 198 172C204 177 211 182 216 188C222 195 229 207 232 210',
			],
		},
		// Sort du bord bas en crête de vague et y replonge.
		bosse: {
			vue: [ 310, 130 ],
			traces: [
				'M10 140C50 50 130 25 185 45C235 63 265 105 300 140',
				'M39 153C43 146 54 125 63 114C72 103 81 95 91 89C100 82 111 78 120 75C130 72 140 70 149 71C158 71 166 73 174 75C182 78 189 81 196 86C203 90 210 95 216 101C223 106 229 112 236 119C243 126 249 133 256 140C263 147 274 159 277 163',
			],
		},
	};

	// Le premier mot du placement nomme la forme : « anse-gauche-bas » pose
	// une anse. Les placements sont décrits dans components/_sillages.scss.
	const poserSillages = () => {
		const SVG = 'http://www.w3.org/2000/svg';

		document.querySelectorAll( '.d38-section[class*="d38-sillage-"]' ).forEach( ( section ) => {
			[ ...section.classList ].reverse().forEach( ( classe ) => {
				const placement = classe.replace( 'd38-sillage-', '' );
				const forme = FORMES[ placement.split( '-' )[ 0 ] ];

				if ( ! classe.startsWith( 'd38-sillage-' ) || ! forme ) {
					return;
				}

				const dessin = document.createElementNS( SVG, 'svg' );

				dessin.setAttribute( 'class', `d38-sillage d38-sillage--${ placement }` );
				dessin.setAttribute( 'viewBox', `0 0 ${ forme.vue.join( ' ' ) }` );
				dessin.setAttribute( 'aria-hidden', 'true' );
				dessin.setAttribute( 'focusable', 'false' );
				dessin.style.setProperty( '--largeur', forme.vue[ 0 ] );

				forme.traces.forEach( ( trace ) => {
					const bande = document.createElementNS( SVG, 'path' );

					bande.setAttribute( 'd', trace );
					bande.setAttribute( 'pathLength', '1' );
					dessin.append( bande );
				} );

				section.prepend( dessin );

				// Le tracé part quand la paire elle-même arrive à l'écran, pas
				// sa section : une paire du bas d'une longue section attend.
				if ( ANIME ) {
					inView( dessin, () => dessin.classList.add( 'est-trace' ), { margin: '0px 0px -10% 0px' } );
				}
			} );
		} );
	};

	/**
	 * Questions : une seule ouverte à la fois dans une même liste.
	 */
	const fermerLesAutres = () => {
		document.querySelectorAll( '.d38-questions details' ).forEach( ( question ) => {
			question.addEventListener( 'toggle', () => {
				if ( ! question.open ) {
					return;
				}

				question.closest( '.d38-questions' ).querySelectorAll( 'details[open]' ).forEach( ( autre ) => {
					if ( autre !== question ) {
						autre.open = false;
					}
				} );
			} );
		} );
	};

	/* Mise en route ------------------------------------------------------- */

	fermerLesAutres();
	poserSillages();
	ondulerBords();
	coulerBords();
	remplirEncadres();

	if ( ! ANIME ) {
		return;
	}

	animerRangees();

	document.querySelectorAll( '.d38-section' ).forEach( ( section ) => {
		if ( section.classList.contains( 'd38-hero' ) ) {
			animerHero( section );
		} else {
			animerSection( section );
		}
	} );

	suivreEtapes();
	animerInteractions();
} )();
