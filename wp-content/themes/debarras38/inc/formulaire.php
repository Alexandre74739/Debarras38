<?php
/**
 * Formulaire de demande de devis.
 *
 * Le code court [d38_formulaire] affiche le formulaire. Chaque demande est
 * envoyée par e-mail à l'adresse du menu « Debarras38 » et enregistrée dans
 * l'administration (Debarras38 → Demandes), pour qu'aucune ne se perde si un
 * e-mail n'arrive pas.
 *
 * Avec JavaScript, l'envoi se fait sans recharger la page (admin-ajax.php).
 * Sans JavaScript, le formulaire est posté normalement (admin-post.php).
 *
 * Protection contre les robots : un champ piège, un délai minimal de saisie et
 * une limite d'envois par heure. Pas de jeton de sécurité : il expirerait sur
 * une page mise en cache, et le formulaire ne donne accès à rien.
 */

defined( 'ABSPATH' ) || exit;

const D38_DEMANDE          = 'd38_demande';
const D38_ENVOIS_PAR_HEURE = 5;
const D38_DELAI_MINIMAL    = 3; // secondes entre l'affichage et l'envoi

/**
 * Lieux proposés dans la liste déroulante.
 */
function d38_formulaire_lieux(): array {
	return array( 'Cave', 'Grenier', 'Garage', 'Appartement', 'Maison entière', 'Autre' );
}

/**
 * Les demandes reçues, visibles dans l'administration uniquement.
 */
add_action(
	'init',
	static function (): void {
		register_post_type(
			D38_DEMANDE,
			array(
				'labels'              => array(
					'name'          => 'Demandes',
					'singular_name' => 'Demande',
					'menu_name'     => 'Demandes',
					'all_items'     => 'Demandes',
					'edit_item'     => 'Demande reçue',
					'search_items'  => 'Chercher une demande',
					'not_found'     => 'Aucune demande pour le moment.',
				),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'debarras38',
				'supports'            => array( 'title', 'editor' ),
				'capability_type'     => 'page',
				'capabilities'        => array( 'create_posts' => 'do_not_allow' ),
				'map_meta_cap'        => true,
				'exclude_from_search' => true,
			)
		);
	}
);

/**
 * Affichage du formulaire.
 */
add_shortcode(
	'd38_formulaire',
	static function (): string {
		$retour = sanitize_key( $_GET['demande'] ?? '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$etats  = array(
			'ok'     => 'Merci, votre demande est bien partie. Nous vous rappelons.',
			'erreur' => 'Votre demande n’a pas pu être envoyée. Vérifiez les champs, ou appelez-nous au ' . d38_coord( 'telephone' ) . '.',
		);

		ob_start();
		?>
		<form class="d38-formulaire" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-ajax="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>">
			<input type="hidden" name="action" value="d38_contact">
			<input type="hidden" name="d38_affiche" value="<?php echo esc_attr( (string) time() ); ?>">

			<p class="d38-formulaire__piege" aria-hidden="true">
				<label for="d38-site-web">Ne remplissez pas ce champ</label>
				<input id="d38-site-web" type="text" name="site_web" tabindex="-1" autocomplete="off">
			</p>

			<p class="d38-champ">
				<label for="d38-nom">Votre nom</label>
				<input id="d38-nom" type="text" name="nom" autocomplete="name" required>
			</p>

			<p class="d38-champ">
				<label for="d38-telephone">Votre téléphone</label>
				<input id="d38-telephone" type="tel" name="telephone" autocomplete="tel" required>
			</p>

			<p class="d38-champ">
				<label for="d38-email">Votre e-mail <span class="d38-champ__option">(facultatif)</span></label>
				<input id="d38-email" type="email" name="email" autocomplete="email">
			</p>

			<p class="d38-champ">
				<label for="d38-commune">Commune</label>
				<input id="d38-commune" type="text" name="commune" autocomplete="address-level2" required>
			</p>

			<p class="d38-champ d38-champ--large">
				<label for="d38-lieu">Que faut-il vider ?</label>
				<select id="d38-lieu" name="lieu">
					<?php foreach ( d38_formulaire_lieux() as $lieu ) : ?>
						<option><?php echo esc_html( $lieu ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p class="d38-champ d38-champ--large">
				<label for="d38-message">Décrivez les lieux</label>
				<textarea id="d38-message" name="message" rows="4" required></textarea>
			</p>

			<p class="d38-formulaire__envoi">
				<button type="submit" class="wp-element-button">Envoyer ma demande</button>
			</p>

			<p class="d38-formulaire__etat<?php echo isset( $etats[ $retour ] ) ? ' est-' . esc_attr( $retour ) : ''; ?>" role="status" aria-live="polite"><?php echo esc_html( $etats[ $retour ] ?? '' ); ?></p>

			<p class="d38-formulaire__mention">Vos coordonnées servent uniquement à vous répondre.</p>
		</form>
		<?php
		// Sur une seule ligne : WordPress ajouterait des retours à la ligne entre les champs.
		return (string) preg_replace( "/>\s+</", "><", trim( (string) ob_get_clean() ) );
	}
);

/**
 * Lit et vérifie les champs envoyés.
 *
 * @return array{0: array<string, string>, 1: string} Les champs nettoyés et le premier message d'erreur (vide si tout va bien).
 */
function d38_formulaire_lire(): array {
	// phpcs:disable WordPress.Security.NonceVerification.Missing
	$champs = array(
		'nom'       => sanitize_text_field( wp_unslash( $_POST['nom'] ?? '' ) ),
		'telephone' => sanitize_text_field( wp_unslash( $_POST['telephone'] ?? '' ) ),
		'email'     => sanitize_email( wp_unslash( $_POST['email'] ?? '' ) ),
		'commune'   => sanitize_text_field( wp_unslash( $_POST['commune'] ?? '' ) ),
		'lieu'      => sanitize_text_field( wp_unslash( $_POST['lieu'] ?? '' ) ),
		'message'   => sanitize_textarea_field( wp_unslash( $_POST['message'] ?? '' ) ),
	);

	$piege   = (string) ( $_POST['site_web'] ?? '' );
	$affiche = (int) ( $_POST['d38_affiche'] ?? 0 );
	// phpcs:enable

	if ( ! in_array( $champs['lieu'], d38_formulaire_lieux(), true ) ) {
		$champs['lieu'] = 'Autre';
	}

	$erreur = match ( true ) {
		'' !== $piege, time() - $affiche < D38_DELAI_MINIMAL => 'Votre demande n’a pas pu être envoyée. Réessayez dans un instant.',
		'' === $champs['nom']                                => 'Indiquez votre nom.',
		strlen( preg_replace( '/\D/', '', $champs['telephone'] ) ) < 9 => 'Indiquez un numéro de téléphone où vous joindre.',
		'' === $champs['commune']                            => 'Indiquez la commune.',
		'' === $champs['message']                            => 'Décrivez les lieux en quelques mots.',
		default                                              => '',
	};

	return array( $champs, $erreur );
}

/**
 * Vrai si cette adresse a déjà trop envoyé de demandes dans l'heure.
 */
function d38_formulaire_trop_d_envois(): bool {
	$cle    = 'd38_envois_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
	$envois = (int) get_transient( $cle );

	if ( $envois >= D38_ENVOIS_PAR_HEURE ) {
		return true;
	}

	set_transient( $cle, $envois + 1, HOUR_IN_SECONDS );

	return false;
}

/**
 * Enregistre la demande et l'envoie par e-mail.
 *
 * @param array<string, string> $champs Champs nettoyés.
 */
function d38_formulaire_enregistrer( array $champs ): bool {
	$lignes = array(
		'Nom : ' . $champs['nom'],
		'Téléphone : ' . $champs['telephone'],
		'E-mail : ' . ( $champs['email'] ?: 'non indiqué' ),
		'Commune : ' . $champs['commune'],
		'À vider : ' . $champs['lieu'],
		'',
		$champs['message'],
	);

	$titre = sprintf( '%s, %s à %s', $champs['nom'], mb_strtolower( $champs['lieu'] ), $champs['commune'] );
	$texte = implode( "\n", $lignes );

	$demande = wp_insert_post(
		array(
			'post_type'    => D38_DEMANDE,
			'post_status'  => 'private',
			'post_title'   => $titre,
			'post_content' => $texte,
		)
	);

	$entetes = $champs['email'] ? array( 'Reply-To: ' . $champs['nom'] . ' <' . $champs['email'] . '>' ) : array();
	$envoye  = wp_mail( d38_coord( 'email' ), 'Demande de devis : ' . $titre, $texte, $entetes );

	// La demande compte comme reçue dès qu'elle est enregistrée ou envoyée.
	return $envoye || ( $demande && ! is_wp_error( $demande ) );
}

/**
 * Traite un envoi et renvoie [ réussite, message ].
 *
 * @return array{0: bool, 1: string}
 */
function d38_formulaire_traiter(): array {
	list( $champs, $erreur ) = d38_formulaire_lire();

	if ( $erreur ) {
		return array( false, $erreur );
	}

	if ( d38_formulaire_trop_d_envois() ) {
		return array( false, 'Vous avez déjà envoyé plusieurs demandes. Appelez-nous au ' . d38_coord( 'telephone' ) . '.' );
	}

	if ( ! d38_formulaire_enregistrer( $champs ) ) {
		return array( false, 'Votre demande n’a pas pu être envoyée. Appelez-nous au ' . d38_coord( 'telephone' ) . '.' );
	}

	return array( true, 'Merci, votre demande est bien partie. Nous vous rappelons.' );
}

/**
 * Envoi sans rechargement de la page.
 */
function d38_formulaire_ajax(): void {
	list( $reussite, $message ) = d38_formulaire_traiter();

	$reussite ? wp_send_json_success( $message ) : wp_send_json_error( $message );
}
add_action( 'wp_ajax_d38_contact', 'd38_formulaire_ajax' );
add_action( 'wp_ajax_nopriv_d38_contact', 'd38_formulaire_ajax' );

/**
 * Envoi classique, sans JavaScript : retour sur la page avec le résultat.
 */
function d38_formulaire_post(): void {
	list( $reussite ) = d38_formulaire_traiter();

	$retour = add_query_arg( 'demande', $reussite ? 'ok' : 'erreur', wp_get_referer() ?: home_url( '/' ) );

	wp_safe_redirect( $retour . '#devis' );
	exit;
}
add_action( 'admin_post_d38_contact', 'd38_formulaire_post' );
add_action( 'admin_post_nopriv_d38_contact', 'd38_formulaire_post' );
