<?php
/**
 * Coordonnées de l'entreprise.
 *
 * Une seule source pour le téléphone, l'e-mail, l'adresse et les textes SEO :
 * la page de réglages « Debarras38 ». Les compositions s'y branchent avec la
 * liaison de blocs « debarras38/coordonnees », le PHP avec d38_coord().
 */

defined( 'ABSPATH' ) || exit;

const D38_OPTION = 'd38_coordonnees';

/**
 * Champs de la page de réglages : clé => [ libellé, type, valeur par défaut, aide ].
 */
function d38_coord_champs(): array {
	return array(
		'telephone'       => array( 'Téléphone', 'text', '06 79 37 20 77', '' ),
		'email'           => array( 'E-mail', 'email', 'debarras38@hotmail.fr', '' ),
		'rue'             => array( 'Adresse', 'text', '9 rue du Trident', '' ),
		'code_postal'     => array( 'Code postal', 'text', '38100', '' ),
		'ville'           => array( 'Ville', 'text', 'Grenoble', '' ),
		'horaires'        => array( 'Horaires', 'text', '', 'Exemple : « Du lundi au samedi, de 8 h à 19 h ». Laissez vide pour ne rien afficher.' ),
		'seo_titre'       => array( 'Titre dans Google', 'text', 'Débarras Grenoble (38) : cave, grenier, maison | Debarras38', '60 caractères au plus.' ),
		'seo_description' => array( 'Description dans Google', 'textarea', 'Entreprise de débarras et de nettoyage à Grenoble et en Isère. Cave, grenier, garage, appartement, maison entière : nous vidons, nous trions, nous nettoyons.', '155 caractères au plus.' ),
	);
}

/**
 * Renvoie une coordonnée, saisie ou calculée.
 *
 * Clés calculées : telephone_lien, telephone_html, appeler, email_lien, email_html, adresse.
 */
function d38_coord( string $cle ): string {
	static $valeurs = null;

	if ( null === $valeurs ) {
		$defauts = wp_list_pluck( d38_coord_champs(), 2 );
		$valeurs = wp_parse_args( array_filter( (array) get_option( D38_OPTION, array() ), 'strlen' ), $defauts );
	}

	switch ( $cle ) {
		case 'telephone_lien':
			$chiffres = preg_replace( '/\D/', '', $valeurs['telephone'] );
			return 'tel:' . ( str_starts_with( $chiffres, '0' ) ? '+33' . substr( $chiffres, 1 ) : '+' . $chiffres );
		case 'appeler':
			return 'Appelez le ' . $valeurs['telephone'];
		case 'email_lien':
			return 'mailto:' . $valeurs['email'];
		case 'telephone_html':
			return sprintf( '<a href="%s">%s</a>', esc_attr( d38_coord( 'telephone_lien' ) ), esc_html( $valeurs['telephone'] ) );
		case 'email_html':
			return sprintf( '<a href="%s">%s</a>', esc_attr( d38_coord( 'email_lien' ) ), esc_html( $valeurs['email'] ) );
		case 'adresse':
			return sprintf( '%s, %s %s', $valeurs['rue'], $valeurs['code_postal'], $valeurs['ville'] );
	}

	return $valeurs[ $cle ] ?? '';
}

/**
 * Liaison de blocs : un bouton ou un paragraphe peut afficher une coordonnée.
 */
add_action(
	'init',
	static function (): void {
		register_block_bindings_source(
			'debarras38/coordonnees',
			array(
				'label'              => 'Coordonnées Debarras38',
				'get_value_callback' => static fn( array $args ): string => d38_coord( $args['key'] ?? '' ),
			)
		);
	}
);

/**
 * Page de réglages.
 */
add_action(
	'admin_menu',
	static function (): void {
		add_menu_page( 'Debarras38', 'Debarras38', 'manage_options', 'debarras38', 'd38_coord_page', 'dashicons-phone', 3 );
		add_submenu_page( 'debarras38', 'Coordonnées', 'Coordonnées', 'manage_options', 'debarras38', 'd38_coord_page' );
	},
	// Avant l'ajout des demandes au menu (inc/formulaire.php), pour que le menu ouvre les coordonnées.
	9
);

add_action(
	'admin_init',
	static function (): void {
		register_setting( 'debarras38', D38_OPTION, array( 'sanitize_callback' => 'd38_coord_nettoyer' ) );
	}
);

function d38_coord_nettoyer( $saisie ): array {
	$propre = array();

	foreach ( d38_coord_champs() as $cle => $champ ) {
		$valeur = $saisie[ $cle ] ?? '';

		$propre[ $cle ] = match ( $champ[1] ) {
			'email'    => sanitize_email( $valeur ),
			'textarea' => sanitize_textarea_field( $valeur ),
			default    => sanitize_text_field( $valeur ),
		};
	}

	return $propre;
}

function d38_coord_page(): void {
	?>
	<div class="wrap">
		<h1>Debarras38</h1>
		<p>Ces informations s’affichent dans l’en-tête, le pied de page et les boutons du site. Elles sont aussi transmises à Google.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'debarras38' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( d38_coord_champs() as $cle => list( $libelle, $type, , $aide ) ) : ?>
					<?php
					$id     = 'd38-' . $cle;
					$nom    = D38_OPTION . '[' . $cle . ']';
					$valeur = d38_coord( $cle );
					?>
					<tr>
						<th scope="row"><label for="<?php echo esc_attr( $id ); ?>"><?php echo esc_html( $libelle ); ?></label></th>
						<td>
							<?php if ( 'textarea' === $type ) : ?>
								<textarea id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $nom ); ?>" rows="3" class="large-text"><?php echo esc_textarea( $valeur ); ?></textarea>
							<?php else : ?>
								<input id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $nom ); ?>" type="<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( $valeur ); ?>" class="regular-text">
							<?php endif; ?>
							<?php if ( $aide ) : ?>
								<p class="description"><?php echo esc_html( $aide ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</table>
			<?php submit_button(); ?>
		</form>
	</div>
	<?php
}
