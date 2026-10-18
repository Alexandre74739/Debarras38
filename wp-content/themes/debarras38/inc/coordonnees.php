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
 * Champs de la page de réglages : clé => [ libellé, type, valeur par défaut, aide, intertitre ].
 * L'intertitre, facultatif, ouvre un nouveau groupe de champs.
 */
function d38_coord_champs(): array {
	return array(
		'telephone'       => array( 'Téléphone', 'text', '06 07 31 30 41', '', 'Coordonnées' ),
		'email'           => array( 'E-mail', 'email', 'debarras38@hotmail.fr', '' ),
		'rue'             => array( 'Adresse', 'text', '9 rue du Trident', '' ),
		'code_postal'     => array( 'Code postal', 'text', '38100', '' ),
		'ville'           => array( 'Ville', 'text', 'Grenoble', '' ),
		'horaires'        => array( 'Horaires', 'text', '', 'Exemple : « Du lundi au samedi, de 8 h à 19 h ». Laissez vide pour ne rien afficher.' ),
		'seo_titre'       => array( 'Titre dans Google', 'text', 'Débarrassage Grenoble (38) : cave, grenier, maison | Debarras38', '60 caractères au plus.', 'Référencement' ),
		'seo_description' => array( 'Description dans Google', 'textarea', 'Débarrassage et nettoyage à Grenoble et en Isère : maison, appartement, cave, grenier, garage. Visite sur place, devis écrit. On passe dans tous les coins.', '155 caractères au plus.' ),
		'responsable'     => array( 'Responsable', 'text', 'Said Ben Fakir', 'Le dirigeant, aussi directeur de la publication du site.', 'Mentions légales' ),
		'forme_juridique' => array( 'Forme juridique', 'text', '', 'Exemple : « Entrepreneur individuel (micro-entreprise) » ou « SARL au capital de 1 000 € ».' ),
		'siret'           => array( 'SIRET', 'text', '', '14 chiffres, sur l’avis de situation INSEE.' ),
		'immatriculation' => array( 'Immatriculation', 'text', '', 'Exemple : « Registre national des entreprises (RNE) » ou « RCS de Grenoble ».' ),
		'tva'             => array( 'TVA intracommunautaire', 'text', '', 'Laissez vide en franchise en base : le site affichera « TVA non applicable, article 293 B du CGI ».' ),
		'hebergeur'       => array( 'Hébergeur du site', 'textarea', '', 'Nom, adresse et téléphone de l’hébergeur.' ),
		'mediateur'       => array( 'Médiateur de la consommation', 'textarea', '', 'Nom, adresse et site du médiateur auquel l’entreprise adhère. Obligatoire quand on travaille pour des particuliers.' ),
	);
}

/**
 * Champs des mentions légales encore vides.
 *
 * @return string[] Leurs libellés.
 */
function d38_coord_manquantes(): array {
	$obligatoires = array( 'responsable', 'forme_juridique', 'siret', 'immatriculation', 'hebergeur', 'mediateur' );
	$champs       = d38_coord_champs();

	return array_map(
		static fn( string $cle ): string => $champs[ $cle ][0],
		array_values( array_filter( $obligatoires, static fn( string $cle ): bool => '' === d38_coord( $cle ) ) )
	);
}

/**
 * Renvoie une coordonnée, saisie ou calculée.
 *
 * Clés calculées : telephone_lien, telephone_html, appeler, email_lien, email_html, adresse,
 * tva_mention, messagerie.
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
		case 'tva_mention':
			return $valeurs['tva'] ? 'TVA intracommunautaire : ' . $valeurs['tva'] : 'TVA non applicable, article 293 B du CGI';
		case 'messagerie':
			// Qui héberge la boîte e-mail où arrivent les demandes : la politique de confidentialité le dit.
			$domaine = strtolower( substr( (string) strrchr( $valeurs['email'], '@' ), 1 ) );
			return match ( true ) {
				(bool) preg_match( '/^(hotmail|outlook|live|msn)\./', $domaine ) => 'Microsoft (Outlook.com), qui peut les conserver hors de l’Union européenne au titre du cadre de protection des données UE–États-Unis (Data Privacy Framework)',
				(bool) preg_match( '/^(gmail|googlemail)\./', $domaine )         => 'Google (Gmail), qui peut les conserver hors de l’Union européenne au titre du cadre de protection des données UE–États-Unis (Data Privacy Framework)',
				default                                                         => 'notre fournisseur de messagerie',
			};
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
		<p>Ces informations s’affichent dans l’en-tête, le pied de page, les boutons et les mentions légales du site. Elles sont aussi transmises à Google.</p>
		<form method="post" action="options.php">
			<?php settings_fields( 'debarras38' ); ?>
			<table class="form-table" role="presentation">
				<?php foreach ( d38_coord_champs() as $cle => $champ ) : ?>
					<?php
					list( $libelle, $type, , $aide ) = $champ;

					$id     = 'd38-' . $cle;
					$nom    = D38_OPTION . '[' . $cle . ']';
					$valeur = d38_coord( $cle );
					?>
					<?php if ( ! empty( $champ[4] ) ) : ?>
						<tr><th colspan="2" scope="rowgroup"><h2><?php echo esc_html( $champ[4] ); ?></h2></th></tr>
					<?php endif; ?>
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
