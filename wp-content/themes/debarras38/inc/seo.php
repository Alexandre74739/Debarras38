<?php
/**
 * Référencement : balises de l'en-tête et données structurées.
 *
 * Tout se désactive si une extension SEO est installée, pour ne pas produire
 * de balises en double.
 */

defined( 'ABSPATH' ) || exit;

function d38_seo_actif(): bool {
	return ! defined( 'WPSEO_VERSION' ) && ! defined( 'RANK_MATH_VERSION' ) && ! defined( 'SEOPRESS_VERSION' );
}

/**
 * Description de la page courante : réglage sur l'accueil, extrait ailleurs.
 */
function d38_seo_description(): string {
	if ( is_front_page() ) {
		return d38_coord( 'seo_description' );
	}

	if ( is_singular() && has_excerpt() ) {
		return wp_strip_all_tags( get_the_excerpt() );
	}

	return get_bloginfo( 'description' );
}

add_filter(
	'pre_get_document_title',
	static fn( string $titre ): string => d38_seo_actif() && is_front_page() ? d38_coord( 'seo_titre' ) : $titre
);

add_action(
	'wp_head',
	static function (): void {
		if ( ! d38_seo_actif() ) {
			return;
		}

		$titre       = wp_get_document_title();
		$description = d38_seo_description();
		$url         = is_front_page() ? home_url( '/' ) : (string) get_permalink();

		$balises = array(
			'description'    => $description,
			'geo.region'     => 'FR-38',
			'geo.placename'  => d38_coord( 'ville' ),
			'og:type'        => 'website',
			'og:locale'      => 'fr_FR',
			'og:site_name'   => 'Debarras38',
			'og:title'       => $titre,
			'og:description' => $description,
			'og:url'         => $url,
			'twitter:card'   => 'summary',
		);

		foreach ( $balises as $nom => $contenu ) {
			if ( '' === $contenu ) {
				continue;
			}

			$attribut = str_starts_with( $nom, 'og:' ) ? 'property' : 'name';
			printf( '<meta %s="%s" content="%s">' . "\n", $attribut, esc_attr( $nom ), esc_attr( $contenu ) );
		}

		if ( is_front_page() ) {
			printf( '<link rel="canonical" href="%s">' . "\n", esc_url( $url ) );
		}
	},
	2
);

// Sur la page d'accueil, la balise canonique ci-dessus remplace celle de WordPress.
add_action(
	'wp',
	static function (): void {
		if ( d38_seo_actif() && is_front_page() ) {
			remove_action( 'wp_head', 'rel_canonical' );
		}
	}
);

/**
 * Textes des blocs d'un type donné, cherchés dans toute l'arborescence.
 *
 * @param array    $blocs  Blocs issus de parse_blocks().
 * @param callable $filtre Reçoit un bloc, renvoie vrai s'il faut explorer ses enfants directs.
 * @param string   $type   Nom du bloc enfant à lire.
 */
function d38_seo_textes( array $blocs, callable $filtre, string $type ): array {
	$textes = array();

	foreach ( $blocs as $bloc ) {
		if ( $filtre( $bloc ) ) {
			foreach ( $bloc['innerBlocks'] as $enfant ) {
				if ( $type === $enfant['blockName'] ) {
					$textes[] = trim( wp_strip_all_tags( $enfant['innerHTML'] ) );
				}
			}
			continue;
		}

		$textes = array_merge( $textes, d38_seo_textes( $bloc['innerBlocks'], $filtre, $type ) );
	}

	return array_values( array_filter( $textes ) );
}

/**
 * Blocs de la page d'accueil : c'est elle qui décrit les services et le secteur.
 */
function d38_seo_blocs_accueil(): array {
	static $blocs = null;

	return $blocs ??= parse_blocks( (string) get_post_field( 'post_content', (int) get_option( 'page_on_front' ) ) );
}

/**
 * Données structurées de l'entreprise (schema.org).
 *
 * Les communes et les services sont lus dans la page d'accueil : une commune
 * ou une pièce ajoutée dans l'éditeur est transmise à Google sans autre réglage.
 */
function d38_seo_entreprise(): array {
	$a_la_classe = static fn( string $classe ): callable => static fn( array $bloc ): bool => str_contains( $bloc['attrs']['className'] ?? '', $classe );

	$communes = d38_seo_textes( d38_seo_blocs_accueil(), $a_la_classe( 'd38-communes' ), 'core/list-item' );
	$services = d38_seo_textes( d38_seo_blocs_accueil(), $a_la_classe( 'd38-rangee__texte' ), 'core/heading' );
	$zones    = array( array( '@type' => 'AdministrativeArea', 'name' => 'Isère' ) );

	foreach ( $communes ?: array( 'Grenoble' ) as $commune ) {
		$zones[] = array( '@type' => 'City', 'name' => $commune );
	}

	$offres = array();

	foreach ( $services as $service ) {
		$offres[] = array(
			'@type'       => 'Offer',
			'itemOffered' => array(
				'@type'      => 'Service',
				'name'       => $service,
				'areaServed' => 'Isère',
			),
		);
	}

	$entreprise = array(
		'@type'       => 'LocalBusiness',
		'@id'         => home_url( '/#entreprise' ),
		'name'        => 'Debarras38',
		'description' => d38_coord( 'seo_description' ),
		'slogan'      => 'On passe dans tous les coins.',
		'url'         => home_url( '/' ),
		'logo'        => D38_URI . '/assets/img/logo-primary.svg',
		'image'       => D38_URI . '/assets/img/logo-primary.svg',
		'telephone'   => str_replace( 'tel:', '', d38_coord( 'telephone_lien' ) ),
		'email'       => d38_coord( 'email' ),
		'address'     => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => d38_coord( 'rue' ),
			'postalCode'      => d38_coord( 'code_postal' ),
			'addressLocality' => d38_coord( 'ville' ),
			'addressRegion'   => 'Isère',
			'addressCountry'  => 'FR',
		),
		'areaServed'  => $zones,
		'makesOffer'  => $offres,
	);

	return array_filter( $entreprise );
}

/**
 * Questions et réponses lues dans les blocs Détails de la page.
 *
 * @param array $blocs Blocs issus de parse_blocks().
 */
function d38_seo_questions( array $blocs ): array {
	$questions = array();

	foreach ( $blocs as $bloc ) {
		if ( 'core/details' === $bloc['blockName'] && preg_match( '#<summary[^>]*>(.*?)</summary>#s', $bloc['innerHTML'], $resume ) ) {
			$reponse = implode( ' ', array_map( 'render_block', $bloc['innerBlocks'] ) );

			$questions[] = array(
				'@type'          => 'Question',
				'name'           => wp_strip_all_tags( $resume[1] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $reponse ) ) ),
				),
			);
			continue;
		}

		if ( $bloc['innerBlocks'] ) {
			$questions = array_merge( $questions, d38_seo_questions( $bloc['innerBlocks'] ) );
		}
	}

	return $questions;
}

add_action(
	'wp_head',
	static function (): void {
		if ( ! d38_seo_actif() ) {
			return;
		}

		$graphe = array(
			d38_seo_entreprise(),
			array(
				'@type'      => 'WebSite',
				'@id'        => home_url( '/#site' ),
				'url'        => home_url( '/' ),
				'name'       => 'Debarras38',
				'inLanguage' => 'fr-FR',
				'publisher'  => array( '@id' => home_url( '/#entreprise' ) ),
			),
		);

		if ( is_singular() ) {
			$questions = d38_seo_questions( parse_blocks( (string) get_post_field( 'post_content', get_queried_object_id() ) ) );

			if ( $questions ) {
				$graphe[] = array(
					'@type'      => 'FAQPage',
					'@id'        => get_permalink() . '#faq',
					'mainEntity' => $questions,
				);
			}
		}

		$donnees = array(
			'@context' => 'https://schema.org',
			'@graph'   => $graphe,
		);

		echo '<script type="application/ld+json">' . wp_json_encode( $donnees, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "</script>\n";
	},
	3
);
