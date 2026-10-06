<?php
/**
 * Title: En-tête
 * Slug: debarras38/header
 * Inserter: no
 *
 * Le logo principal est gardé à toutes les largeurs : il ne passe pas au logo réduit.
 * Sous 960 px, le groupe « volet » se replie derrière le bouton de menu (assets/js/entete.js).
 * Les liens s'ajoutent dans le bloc Navigation, les boutons dans le bloc Boutons.
 */

$accueil = esc_url( home_url( '/' ) );
$images  = esc_url( D38_URI . '/assets/img' );
?>
<!-- wp:group {"className":"d38-entete","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group d38-entete"><!-- wp:group {"className":"d38-entete__logo","layout":{"type":"default"}} -->
<div class="wp-block-group d38-entete__logo"><!-- wp:image {"linkDestination":"custom","className":"d38-logo d38-logo--principal"} -->
<figure class="wp-block-image d38-logo d38-logo--principal"><a href="<?php echo $accueil; ?>"><img src="<?php echo $images; ?>/logo-primary.svg" alt="Debarras38, débarras et nettoyage à Grenoble : retour à l’accueil"/></a></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"d38-entete__volet","layout":{"type":"default"}} -->
<div class="wp-block-group d38-entete__volet"><!-- wp:group {"className":"d38-entete__actions","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group d38-entete__actions"><!-- wp:navigation {"overlayMenu":"never","className":"d38-menu","layout":{"type":"flex","justifyContent":"right"}} -->
<!-- wp:navigation-link {"label":"Services","url":"<?php echo $accueil; ?>#services","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Déroulement","url":"<?php echo $accueil; ?>#deroulement","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Secteur","url":"<?php echo $accueil; ?>#secteur","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Questions","url":"<?php echo $accueil; ?>#questions","kind":"custom","isTopLevelLink":true} /-->
<!-- /wp:navigation -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"is-style-plein"} -->
<div class="wp-block-button is-style-plein"><a class="wp-block-button__link wp-element-button" href="<?php echo $accueil; ?>#devis">Demander un devis</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
