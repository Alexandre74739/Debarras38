<?php
/**
 * Mise en place du thème : supports, styles, scripts, compositions, page d'accueil.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Sections de la page d'accueil, dans l'ordre d'affichage.
 * Chaque entrée est le nom d'un fichier de patterns/.
 */
const D38_SECTIONS_ACCUEIL = array( 'hero', 'communes', 'services', 'appel', 'cas', 'etapes', 'faq', 'contact' );

add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support( 'editor-styles' );
		add_theme_support( 'responsive-embeds' );
		remove_theme_support( 'core-block-patterns' );

		// L'éditeur charge la même feuille que le site : le client voit le rendu réel.
		add_editor_style( 'assets/css/main.css' );
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		wp_enqueue_style( 'debarras38', D38_URI . '/assets/css/main.css', array(), D38_VERSION );

		wp_enqueue_script( 'motion', D38_URI . '/assets/js/motion.js', array(), '12', array( 'strategy' => 'defer' ) );
		wp_enqueue_script( 'debarras38', D38_URI . '/assets/js/animations.js', array( 'motion' ), D38_VERSION, array( 'strategy' => 'defer' ) );
		wp_enqueue_script( 'debarras38-formulaire', D38_URI . '/assets/js/formulaire.js', array( 'motion' ), D38_VERSION, array( 'strategy' => 'defer' ) );
	}
);

/**
 * En-tête du document : polices préchargées, favicon, et la classe qui prépare
 * les animations. Sans JavaScript, ou si le script tarde, tout reste visible.
 */
add_action(
	'wp_head',
	static function (): void {
		$polices = array( 'gabarito-latin-wght-normal', 'atkinson-hyperlegible-latin-400-normal' );

		foreach ( $polices as $police ) {
			printf( '<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url( D38_URI . "/assets/fonts/$police.woff2" ) );
		}

		if ( ! has_site_icon() ) {
			printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( D38_URI . '/assets/img/favicon.svg' ) );
		}

		echo "<script>(function(d){if(matchMedia('(prefers-reduced-motion: reduce)').matches)return;d.classList.add('d38-anime');setTimeout(function(){window.d38Pret||d.classList.remove('d38-anime')},2500)})(document.documentElement)</script>\n";
	},
	1
);

/**
 * Catégorie des compositions, visible dans l'outil d'insertion de l'éditeur.
 */
add_action(
	'init',
	static function (): void {
		register_block_pattern_category( 'debarras38', array( 'label' => 'Debarras38' ) );
	}
);

/**
 * Allège le front : pas d'emojis en image, pas de balises inutiles.
 */
add_action(
	'init',
	static function (): void {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
	}
);

/**
 * À l'activation, crée la page d'accueil à partir des compositions et la définit
 * comme page de garde. Ne touche à rien si elle existe déjà.
 */
add_action(
	'after_switch_theme',
	static function (): void {
		if ( get_option( 'd38_page_accueil' ) && get_post( (int) get_option( 'd38_page_accueil' ) ) ) {
			return;
		}

		$registre = WP_Block_Patterns_Registry::get_instance();
		$contenu  = '';

		foreach ( D38_SECTIONS_ACCUEIL as $section ) {
			$composition = $registre->get_registered( "debarras38/$section" );
			$contenu    .= trim( $composition['content'] ?? '' ) . "\n\n";
		}

		$page = wp_insert_post(
			array(
				'post_type'    => 'page',
				'post_status'  => 'publish',
				'post_title'   => 'Accueil',
				'post_name'    => 'accueil',
				'post_content' => wp_slash( $contenu ),
			)
		);

		if ( $page && ! is_wp_error( $page ) ) {
			update_option( 'd38_page_accueil', $page );
			update_option( 'show_on_front', 'page' );
			update_option( 'page_on_front', $page );
		}
	}
);
