<?php
/**
 * Mise en place du thème : supports, styles, scripts, compositions, page d'accueil.
 */

defined('ABSPATH') || exit;

/**
 * Sections de la page d'accueil, dans l'ordre d'affichage.
 * Chaque entrée est le nom d'un fichier de patterns/.
 */
const D38_SECTIONS_ACCUEIL = array('hero', 'atouts', 'services', 'appel', 'cas', 'etapes', 'faq', 'contact');

add_action(
	'after_setup_theme',
	static function (): void {
		add_theme_support('editor-styles');
		add_theme_support('responsive-embeds');
		remove_theme_support('core-block-patterns');

		// L'éditeur charge la même feuille que le site : le client voit le rendu réel.
		add_editor_style('assets/css/main.css');
	}
);

add_action(
	'wp_enqueue_scripts',
	static function (): void {
		// Chaque fichier porte la date de sa dernière modification : le
		// navigateur recharge un fichier dès qu'il change, sans vider le cache.
		$version = static fn(string $fichier): string => (string) filemtime(D38_DIR . $fichier);

		wp_enqueue_style('debarras38', D38_URI . '/assets/css/main.css', array(), $version('/assets/css/main.css'));

		wp_enqueue_script('motion', D38_URI . '/assets/js/motion.js', array(), '12', array('strategy' => 'defer'));
		wp_enqueue_script('debarras38', D38_URI . '/assets/js/animations.js', array('motion'), $version('/assets/js/animations.js'), array('strategy' => 'defer'));
		wp_enqueue_script('debarras38-entete', D38_URI . '/assets/js/entete.js', array(), $version('/assets/js/entete.js'), array('strategy' => 'defer'));
		wp_enqueue_script('debarras38-formulaire', D38_URI . '/assets/js/formulaire.js', array('motion'), $version('/assets/js/formulaire.js'), array('strategy' => 'defer'));
		wp_enqueue_script('debarras38-cookies', D38_URI . '/assets/js/cookies.js', array(), $version('/assets/js/cookies.js'), array('strategy' => 'defer'));
	}
);

// Éditeur : le format « Souligner d'une vague » du titre principal.
add_action(
	'enqueue_block_editor_assets',
	static function (): void {
		wp_enqueue_script(
			'debarras38-editeur',
			D38_URI . '/assets/js/editeur.js',
			array('wp-rich-text', 'wp-block-editor', 'wp-element', 'wp-data'),
			(string) filemtime(D38_DIR . '/assets/js/editeur.js'),
			true
		);
	}
);

/**
 * En-tête du document : polices préchargées, favicon, et les classes qui
 * préparent le menu replié (d38-js) et les animations (d38-anime). Sans
 * JavaScript, ou si un script tarde, tout reste visible.
 */
add_action(
	'wp_head',
	static function (): void {
		$polices = array('gabarito-latin-wght-normal', 'atkinson-hyperlegible-latin-400-normal');

		foreach ($polices as $police) {
			printf('<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n", esc_url(D38_URI . "/assets/fonts/$police.woff2"));
		}

		// Icônes de la charte (chapitre 8), sauf si le client en choisit une dans l'administration.
		if (!has_site_icon()) {
			$icones = array(
				'<link rel="icon" href="%1$s/img/favicon.ico" sizes="32x32">',
				'<link rel="icon" href="%1$s/img/favicon.svg" type="image/svg+xml">',
				'<link rel="apple-touch-icon" href="%1$s/img/apple-touch-icon.png">',
				'<link rel="manifest" href="%1$s/site.webmanifest">',
			);

			foreach ($icones as $icone) {
				printf($icone . "\n", esc_url(D38_URI . '/assets'));
			}
		}

		echo '<meta name="theme-color" content="#0B5D66">' . "\n";

		echo "<script>(function(d){d.classList.add('d38-js');setTimeout(function(){window.d38Entete||d.classList.remove('d38-js')},2500);if(matchMedia('(prefers-reduced-motion: reduce)').matches)return;d.classList.add('d38-anime');setTimeout(function(){window.d38Pret||d.classList.remove('d38-anime')},2500)})(document.documentElement)</script>\n";
	},
	1
);

/**
 * /favicon.ico, demandé d'office par les navigateurs : WordPress y renvoie
 * son propre logo. On renvoie celui de la charte.
 */
add_action(
	'do_favicon',
	static function (): void {
		if (!has_site_icon()) {
			wp_redirect(D38_URI . '/assets/img/favicon.ico', 301);
			exit;
		}
	},
	1
);

/**
 * Catégorie des compositions, visible dans l'outil d'insertion de l'éditeur.
 */
add_action(
	'init',
	static function (): void {
		register_block_pattern_category('debarras38', array('label' => 'Debarras38'));
	}
);

/**
 * Style « Plein » du bloc Bouton, à côté du bouton principal et du contour.
 */
add_action(
	'init',
	static function (): void {
		register_block_style(
			'core/button',
			array(
				'name' => 'plein',
				'label' => 'Plein',
			)
		);
	}
);

/**
 * Allège le front : pas d'emojis en image, pas de balises inutiles.
 */
add_action(
	'init',
	static function (): void {
		remove_action('wp_head', 'print_emoji_detection_script', 7);
		remove_action('wp_print_styles', 'print_emoji_styles');
		remove_action('wp_head', 'wp_generator');
		remove_action('wp_head', 'rsd_link');
		remove_action('wp_head', 'wlwmanifest_link');
	}
);

/**
 * À l'activation, crée la page d'accueil à partir des compositions et la définit
 * comme page de garde. Ne touche à rien si elle existe déjà.
 */
add_action(
	'after_switch_theme',
	static function (): void {
		if (get_option('d38_page_accueil') && get_post((int) get_option('d38_page_accueil'))) {
			return;
		}

		$registre = WP_Block_Patterns_Registry::get_instance();
		$contenu = '';

		foreach (D38_SECTIONS_ACCUEIL as $section) {
			$composition = $registre->get_registered("debarras38/$section");
			$contenu .= trim($composition['content'] ?? '') . "\n\n";
		}

		$page = wp_insert_post(
			array(
				'post_type' => 'page',
				'post_status' => 'publish',
				'post_title' => 'Accueil',
				'post_name' => 'accueil',
				'post_content' => wp_slash($contenu),
			)
		);

		if ($page && !is_wp_error($page)) {
			update_option('d38_page_accueil', $page);
			update_option('show_on_front', 'page');
			update_option('page_on_front', $page);
		}
	}
);
