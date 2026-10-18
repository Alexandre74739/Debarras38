/**
 * Bandeau de cookies (inc/mentions.php).
 *
 * Le choix du visiteur est gardé six mois dans le navigateur, durée conseillée
 * par la CNIL, puis le bandeau revient. Il est lisible dans
 * window.d38Consentement (« accepte », « refuse » ou null) et annoncé par
 * l'événement « d38:consentement » : un script de statistiques ajouté plus
 * tard ne se charge qu'après « accepte ».
 */
( () => {
	const CLE = 'd38-cookies';
	const DUREE = 182 * 24 * 60 * 60 * 1000;
	const calme = window.matchMedia( '(prefers-reduced-motion: reduce)' );

	const lire = () => {
		try {
			const valeur = JSON.parse( localStorage.getItem( CLE ) );
			return valeur && Date.now() - valeur.date < DUREE ? valeur.choix : null;
		} catch ( erreur ) {
			return null;
		}
	};

	const ecrire = ( choix ) => {
		try {
			localStorage.setItem( CLE, JSON.stringify( { choix, date: Date.now() } ) );
		} catch ( erreur ) {
			// Stockage bloqué (navigation privée…) : le bandeau reviendra à la page suivante.
		}
	};

	window.d38Consentement = lire();

	const bandeau = document.querySelector( '.d38-cookies' );

	if ( ! bandeau ) {
		return;
	}

	// Le bandeau glisse depuis le bord gauche ; il est retiré à la fin de sa sortie.
	const montrer = ( focaliser ) => {
		bandeau.hidden = false;
		requestAnimationFrame( () => requestAnimationFrame( () => bandeau.classList.add( 'est-visible' ) ) );

		if ( focaliser ) {
			bandeau.querySelector( 'button' ).focus( { preventScroll: true } );
		}
	};

	const cacher = () => {
		bandeau.classList.remove( 'est-visible' );

		if ( calme.matches ) {
			bandeau.hidden = true;
			return;
		}

		bandeau.addEventListener( 'transitionend', () => {
			bandeau.hidden = ! bandeau.classList.contains( 'est-visible' );
		}, { once: true } );
	};

	bandeau.addEventListener( 'click', ( evenement ) => {
		const bouton = evenement.target.closest( '[data-choix]' );

		if ( ! bouton ) {
			return;
		}

		ecrire( bouton.dataset.choix );
		window.d38Consentement = bouton.dataset.choix;
		document.dispatchEvent( new CustomEvent( 'd38:consentement', { detail: bouton.dataset.choix } ) );
		cacher();
	} );

	// « Gérer mes cookies », sur la page Cookies : rouvre le bandeau.
	document.addEventListener( 'click', ( evenement ) => {
		if ( evenement.target.closest( 'a[href$="#gerer-cookies"]' ) ) {
			evenement.preventDefault();
			montrer( true );
		}
	} );

	if ( ! window.d38Consentement ) {
		montrer( false );
	}
} )();
