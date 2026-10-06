/**
 * Formulaire de demande de devis : envoi sans recharger la page.
 *
 * Sans ce script, le formulaire est posté normalement (voir inc/formulaire.php).
 */
( () => {
	const ECHEC = 'Votre demande n’a pas pu être envoyée. Réessayez, ou appelez-nous.';

	document.querySelectorAll( '.d38-formulaire' ).forEach( ( formulaire ) => {
		const etat = formulaire.querySelector( '.d38-formulaire__etat' );
		const bouton = formulaire.querySelector( '[type="submit"]' );
		const libelle = bouton.textContent;

		const afficher = ( message, reussite ) => {
			etat.textContent = message;
			etat.classList.toggle( 'est-ok', reussite );
			etat.classList.toggle( 'est-erreur', ! reussite );

			if ( window.Motion && ! matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
				window.Motion.animate( etat, { opacity: [ 0, 1 ], y: [ 8, 0 ] }, { duration: 0.4 } );
			}
		};

		formulaire.addEventListener( 'submit', async ( evenement ) => {
			evenement.preventDefault();

			bouton.disabled = true;
			bouton.textContent = 'Envoi en cours…';

			try {
				const reponse = await fetch( formulaire.dataset.ajax, { method: 'POST', body: new FormData( formulaire ) } );
				const resultat = await reponse.json();

				afficher( resultat.data || ECHEC, Boolean( resultat.success ) );

				if ( resultat.success ) {
					formulaire.reset();
				}
			} catch {
				afficher( ECHEC, false );
			} finally {
				bouton.disabled = false;
				bouton.textContent = libelle;
			}
		} );
	} );
} )();
