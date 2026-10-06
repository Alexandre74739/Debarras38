<?php
/**
 * Les décors dessinés.
 *
 * Un bloc Groupe qui porte l'une des classes ci-dessous reçoit le SVG
 * correspondant. Le dessin est injecté ici plutôt que collé dans la page : le
 * client ne voit jamais de code dans l'éditeur, et chaque dessin n'existe
 * qu'à un seul endroit (assets/img/).
 */

defined( 'ABSPATH' ) || exit;

const D38_DECORS = array(
	'd38-tableau' => 'tableau-mare.svg',
);

add_filter(
	'render_block_core/group',
	static function ( string $html, array $bloc ): string {
		$classes = $bloc['attrs']['className'] ?? '';

		foreach ( D38_DECORS as $classe => $fichier ) {
			if ( ! preg_match( '/(^|\s)' . preg_quote( $classe, '/' ) . '(\s|$)/', $classes ) ) {
				continue;
			}

			$svg      = (string) file_get_contents( D38_DIR . '/assets/img/' . $fichier );
			$position = strrpos( $html, '</' );

			return false === $position ? $html : substr_replace( $html, $svg, $position, 0 );
		}

		return $html;
	},
	10,
	2
);
