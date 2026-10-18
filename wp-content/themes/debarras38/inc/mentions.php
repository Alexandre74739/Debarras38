<?php
/**
 * Mentions légales et politique de confidentialité.
 *
 * Les deux pages sont créées à partir des compositions du même nom. Leurs
 * informations variables (SIRET, hébergeur, médiateur…) viennent du menu
 * « Debarras38 » par le code court [d38_coord cle="siret"] : on les change
 * à un seul endroit.
 *
 * Le site ne dépose aucun cookie de suivi. Le bandeau de cookies, sur le côté,
 * recueille quand même un choix : il est gardé six mois dans le navigateur
 * (assets/js/cookies.js), et un script ajouté plus tard (statistiques, carte,
 * vidéo) ne doit se charger que si window.d38Consentement vaut « accepte »
 * (ou à l'événement « d38:consentement »).
 */

defined( 'ABSPATH' ) || exit;

/**
 * Une coordonnée dans le texte d'une page. Vide, elle s'affiche « à compléter ».
 */
add_shortcode(
	'd38_coord',
	static function ( $attributs ): string {
		$cle    = sanitize_key( $attributs['cle'] ?? '' );
		$valeur = d38_coord( $cle );

		if ( '' === $valeur ) {
			return '<mark class="d38-a-completer">à compléter</mark>';
		}

		// Les clés en _html sont déjà des liens échappés.
		return str_ends_with( $cle, '_html' ) ? $valeur : nl2br( esc_html( $valeur ) );
	}
);

/**
 * Les pages légales : clé => [ option qui garde son identifiant, titre, adresse, composition ].
 * La politique de confidentialité reprend la page que WordPress crée à l'installation.
 */
function d38_pages_legales(): array {
	return array(
		'mentions'        => array( 'd38_page_mentions', 'Mentions légales', 'mentions-legales', 'debarras38/mentions-legales' ),
		'confidentialite' => array( 'wp_page_for_privacy_policy', 'Politique de confidentialité', 'politique-de-confidentialite', 'debarras38/confidentialite' ),
		'cookies'         => array( 'd38_page_cookies', 'Cookies', 'cookies', 'debarras38/cookies' ),
	);
}

/**
 * Adresse d'une page légale, pour le pied de page, le formulaire et le bandeau.
 *
 * @param string $page « mentions », « confidentialite » ou « cookies ».
 */
function d38_page_legale( string $page ): string {
	list( $option, , $slug ) = d38_pages_legales()[ $page ];

	$id = (int) get_option( $option );

	if ( $id && 'publish' === get_post_status( $id ) ) {
		return (string) get_permalink( $id );
	}

	return home_url( "/$slug/" );
}

/**
 * Crée les pages légales qui n'existent pas encore.
 */
function d38_creer_pages_legales(): void {
	$registre = WP_Block_Patterns_Registry::get_instance();

	foreach ( d38_pages_legales() as list( $option, $titre, $slug, $composition ) ) {
		$id      = (int) get_option( $option );
		$contenu = wp_slash( trim( $registre->get_registered( $composition )['content'] ?? '' ) );

		// Une page déjà publiée a peut-être été retouchée : on n'y touche pas.
		if ( $id && 'publish' === get_post_status( $id ) ) {
			continue;
		}

		$page = array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => $titre,
			'post_name'    => $slug,
			'post_content' => $contenu,
		);

		if ( $id && get_post( $id ) ) {
			$page['ID'] = $id;
		}

		$id = wp_insert_post( $page );

		if ( $id && ! is_wp_error( $id ) ) {
			update_option( $option, $id );
		}
	}
}
add_action( 'after_switch_theme', 'd38_creer_pages_legales' );

/**
 * Rappel dans l'administration tant que des mentions obligatoires manquent.
 */
add_action(
	'admin_notices',
	static function (): void {
		$ecran = get_current_screen();

		if ( ! current_user_can( 'manage_options' ) || ! $ecran || ! in_array( $ecran->id, array( 'dashboard', 'toplevel_page_debarras38' ), true ) ) {
			return;
		}

		$manquantes = d38_coord_manquantes();

		if ( ! $manquantes ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p><strong>Mentions légales incomplètes.</strong> À remplir dans <a href="%s">Debarras38</a> avant la mise en ligne : %s.</p></div>',
			esc_url( admin_url( 'admin.php?page=debarras38' ) ),
			esc_html( implode( ', ', $manquantes ) )
		);
	}
);

/**
 * Bandeau de cookies, posé sur le côté de l'écran. Caché par défaut : le
 * script ne le montre qu'aux visiteurs qui n'ont pas encore choisi. Un lien
 * vers « #gerer-cookies » le rouvre.
 */
add_action(
	'wp_footer',
	static function (): void {
		?>
		<section class="d38-cookies" aria-labelledby="d38-cookies-titre" hidden>
			<p class="d38-cookies__titre" id="d38-cookies-titre">Cookies</p>
			<p>Nous ne déposons aucun cookie publicitaire ni de suivi. Si nous ajoutons un jour des statistiques de visite, elles ne fonctionneront qu’avec votre accord.</p>
			<p><a href="<?php echo esc_url( d38_page_legale( 'cookies' ) ); ?>">En savoir plus sur les cookies</a></p>
			<div class="wp-block-buttons d38-cookies__choix">
				<div class="wp-block-button is-style-plein"><button type="button" class="wp-block-button__link wp-element-button" data-choix="refuse">Refuser</button></div>
				<div class="wp-block-button is-style-plein"><button type="button" class="wp-block-button__link wp-element-button" data-choix="accepte">Accepter</button></div>
			</div>
		</section>
		<?php
	}
);
