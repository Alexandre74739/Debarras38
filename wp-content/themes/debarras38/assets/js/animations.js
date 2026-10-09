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
 *   4. Scènes        le grand visuel, le bandeau des communes, les étapes
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
	const GROUPES = '.d38-entete-section, .d38-cartes, .d38-etapes, .d38-questions, .d38-faq__titre, .d38-contact__texte, .d38-appel';

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

			texte.textContent.split( /(\s+)/ ).forEach( ( morceau ) => {
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
	 * @param {string}  position « haut », « gauche » ou « droite ».
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
	 * Bords de page : une onde descend à gauche, une autre remonte à droite.
	 */
	const ondulerRives = () => {
		document.querySelectorAll( '.d38-rives' ).forEach( ( section ) => {
			poserOnde( section, 'gauche', -26 );
			poserOnde( section, 'droite', -34 );

			new ResizeObserver( () => section.style.setProperty( '--longueur', `${ section.offsetHeight }px` ) ).observe( section );
		} );
	};

	/**
	 * Encadrés : deux nappes d'eau qui ondulent en sens contraires.
	 */
	const remplirEncadres = () => {
		document.querySelectorAll( '.d38-vagues, .d38-visuel--vagues' ).forEach( ( encadre ) => {
			poserOnde( creer( 'd38-vague d38-vague--fond', encadre ), 'haut', 30 );
			poserOnde( creer( 'd38-vague d38-vague--devant', encadre ), 'haut', -44 );
		} );
	};

	/* 3. Apparitions ------------------------------------------------------ */

	/**
	 * Éléments à révéler dans une section, dans l'ordre de lecture.
	 */
	const elementsDe = ( section ) =>
		[ ...section.children ]
			.filter( ( element ) => ! element.matches( '.d38-onde, .d38-rangees' ) )
			.flatMap( ( element ) => ( element.matches( GROUPES ) ? [ ...element.children ].filter( ( enfant ) => ! enfant.matches( '.d38-vague' ) ) : [ element ] ) );

	/**
	 * Fait apparaître des éléments en cascade. Un titre arrive mot par mot,
	 * le reste en montant légèrement.
	 */
	const reveler = ( elements, pas = 0.08 ) => {
		elements.forEach( ( element, rang ) => {
			const depart = rang * pas;

			if ( element.matches( 'h1, h2, h3' ) ) {
				const mots = decouperMots( element );

				mots.forEach( ( mot ) => ( mot.style.opacity = 0 ) );
				animate( mots, { opacity: 1, y: [ '0.7em', '0em' ], rotate: [ 4, 0 ] }, { duration: 0.75, ease: DOUX, delay: stagger( 0.045, { startDelay: depart } ) } );
				return;
			}

			element.style.opacity = 0;
			animate( element, { opacity: 1, y: [ 32, 0 ] }, { duration: 0.85, ease: DOUX, delay: depart } );
		} );
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
	 * Communes : la liste est doublée, puis défile sans fin. Elle ralentit au survol.
	 */
	const defilerCommunes = () => {
		document.querySelectorAll( '.d38-secteur .d38-communes' ).forEach( ( liste ) => {
			const cadre = document.createElement( 'div' );
			const piste = document.createElement( 'div' );

			cadre.className = 'd38-defile';
			piste.className = 'd38-defile__piste';
			liste.replaceWith( cadre );
			cadre.append( piste );
			piste.append( liste );

			// Assez de copies pour couvrir deux fois la largeur de l'écran.
			do {
				const copie = liste.cloneNode( true );

				copie.setAttribute( 'aria-hidden', 'true' );
				piste.append( copie );
			} while ( piste.scrollWidth < window.innerWidth * 2 );

			const defile = animate( piste, { x: [ '0%', `${ -100 / piste.children.length }%` ] }, { duration: liste.offsetWidth / 45, ...EN_BOUCLE } );

			hover( cadre, () => {
				defile.speed = 0.35;
				return () => ( defile.speed = 1 );
			} );
		} );
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

		document.querySelectorAll( '.d38-questions details' ).forEach( ( question ) => {
			question.addEventListener( 'toggle', () => {
				if ( question.open ) {
					animate( question.querySelectorAll( ':scope > :not(summary)' ), { opacity: [ 0, 1 ], y: [ -8, 0 ] }, { duration: 0.35, ease: DOUX } );
				}
			} );
		} );
	};

	/* Mise en route ------------------------------------------------------- */

	ondulerBords();
	ondulerRives();
	remplirEncadres();

	if ( ! ANIME ) {
		return;
	}

	animerRangees();

	document.querySelectorAll( '.d38-section' ).forEach( ( section ) => {
		if ( section.classList.contains( 'd38-hero' ) ) {
			animerHero( section );
		} else if ( ! section.classList.contains( 'd38-secteur' ) ) {
			animerSection( section );
		}
	} );

	defilerCommunes();
	suivreEtapes();
	animerInteractions();
} )();
