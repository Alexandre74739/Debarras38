<?php
/**
 * Title: En-tête
 * Slug: debarras38/header
 * Inserter: no
 *
 * Logo principal dès qu'il y a de la largeur, logo réduit sur téléphone (charte, chapitre 2).
 */

$accueil = esc_url( home_url( '/' ) );
$images  = esc_url( D38_URI . '/assets/img' );
?>
<!-- wp:group {"className":"d38-entete","layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"}} -->
<div class="wp-block-group d38-entete"><!-- wp:group {"className":"d38-entete__logo","layout":{"type":"default"}} -->
<div class="wp-block-group d38-entete__logo"><!-- wp:image {"linkDestination":"custom","className":"d38-logo d38-logo--principal"} -->
<figure class="wp-block-image d38-logo d38-logo--principal"><a href="<?php echo $accueil; ?>"><img src="<?php echo $images; ?>/logo-primary.svg" alt="Debarras38, débarras et nettoyage à Grenoble : retour à l’accueil"/></a></figure>
<!-- /wp:image -->

<!-- wp:image {"linkDestination":"custom","className":"d38-logo d38-logo--reduit"} -->
<figure class="wp-block-image d38-logo d38-logo--reduit"><a href="<?php echo $accueil; ?>"><img src="<?php echo $images; ?>/reduce-logo.svg" alt="Debarras38, débarras et nettoyage à Grenoble : retour à l’accueil"/></a></figure>
<!-- /wp:image --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"d38-entete__actions","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group d38-entete__actions"><!-- wp:navigation {"overlayMenu":"mobile","className":"d38-menu","layout":{"type":"flex","justifyContent":"right"}} -->
<!-- wp:navigation-link {"label":"Services","url":"<?php echo $accueil; ?>#services","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Déroulement","url":"<?php echo $accueil; ?>#deroulement","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Secteur","url":"<?php echo $accueil; ?>#secteur","kind":"custom","isTopLevelLink":true} /-->

<!-- wp:navigation-link {"label":"Questions","url":"<?php echo $accueil; ?>#questions","kind":"custom","isTopLevelLink":true} /-->
<!-- /wp:navigation -->

<!-- wp:buttons {"className":"d38-entete__appel"} -->
<div class="wp-block-buttons d38-entete__appel"><!-- wp:button {"className":"is-style-outline d38-bouton--telephone","metadata":{"bindings":{"url":{"source":"debarras38/coordonnees","args":{"key":"telephone_lien"}},"text":{"source":"debarras38/coordonnees","args":{"key":"telephone"}}}}} -->
<div class="wp-block-button is-style-outline d38-bouton--telephone"><a class="wp-block-button__link wp-element-button" href="tel:+33679372077">06 79 37 20 77</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
