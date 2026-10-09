<?php
/**
 * Title: Accroche et image
 * Slug: debarras38/hero
 * Categories: debarras38
 * Description: Le premier écran : le titre principal, une phrase et un bouton, à côté d'une image qui sort de sa forme. Remplacez le canard par une photo, de préférence détourée (PNG sans fond).
 *
 * Le bouton est un bloc ordinaire, que le client modifie dans l'éditeur.
 */

$images = esc_url( D38_URI . '/assets/img' );
?>
<!-- wp:group {"tagName":"section","metadata":{"name":"Accroche"},"align":"full","className":"d38-section d38-hero","backgroundColor":"creme","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull d38-section d38-hero has-creme-background-color has-background"><!-- wp:group {"className":"d38-hero__texte","layout":{"type":"default"}} -->
<div class="wp-block-group d38-hero__texte"><!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Débarrassage et nettoyage <mark style="background-color:rgba(0, 0, 0, 0)" class="has-inline-color has-bleu-canard-color">à Grenoble</mark></h1>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"d38-hero__slogan"} -->
<p class="d38-hero__slogan">On passe dans tous les coins.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"d38-chapo"} -->
<p class="d38-chapo">Succession, déménagement, logement à rendre : vous nous confiez les clés, nous vidons, nous trions, nous nettoyons. De la cave au grenier, partout en Isère.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="#devis">Recevoir mon devis</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"d38-hero__visuel","layout":{"type":"default"}} -->
<div class="wp-block-group d38-hero__visuel"><!-- wp:group {"className":"d38-blob","backgroundColor":"bleu-canard","layout":{"type":"default"}} -->
<div class="wp-block-group d38-blob has-bleu-canard-background-color has-background"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
<figure class="wp-block-image size-full"><img src="<?php echo $images; ?>/canard-vert-eau.svg" alt="Le canard de Debarras38, entreprise de débarras et de nettoyage à Grenoble"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
