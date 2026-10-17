/**
 * Éditeur : format « Souligner d'une vague ».
 *
 * Dans le titre principal (bloc Titre de niveau 1), le client sélectionne un
 * ou plusieurs mots et clique sur le bouton de la barre d'outils : les mots
 * sont enveloppés dans <span class="d38-souligne">, que le thème souligne
 * d'un trait en vague (components/_souligne.scss). Un second clic le retire.
 *
 * Écrit sans compilation, avec les bibliothèques que WordPress charge déjà.
 */
( ( { richText, blockEditor, element, data } ) => {
	const NOM = 'debarras38/souligne';
	const h = element.createElement;

	// Pictogramme du bouton : un mot (trois traits) souligné d'une vague.
	const icone = h(
		'svg',
		{ xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', width: 24, height: 24, 'aria-hidden': true },
		h( 'path', { d: 'M6 5v8M12 5v8M18 5v8', fill: 'none', stroke: 'currentColor', strokeWidth: 2.5, strokeLinecap: 'round' } ),
		h( 'path', { d: 'M3 18.5c3-2 6-2 9 0s6 2 9 0', fill: 'none', stroke: 'currentColor', strokeWidth: 2.5, strokeLinecap: 'round' } )
	);

	const Bouton = ( { isActive, value, onChange } ) => {
		// Le bouton n'apparaît que dans un titre de niveau 1.
		const dansLeTitre = data.useSelect( ( select ) => {
			const bloc = select( 'core/block-editor' ).getSelectedBlock();

			return bloc?.name === 'core/heading' && bloc.attributes.level === 1;
		}, [] );

		if ( ! dansLeTitre ) {
			return null;
		}

		return h( blockEditor.RichTextToolbarButton, {
			icon: icone,
			title: 'Souligner d’une vague',
			isActive,
			onClick: () => onChange( richText.toggleFormat( value, { type: NOM } ) ),
		} );
	};

	richText.registerFormatType( NOM, {
		title: 'Souligner d’une vague',
		tagName: 'span',
		className: 'd38-souligne',
		edit: Bouton,
	} );
} )( window.wp );
