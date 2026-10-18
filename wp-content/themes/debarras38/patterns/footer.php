<?php
/**
 * Title: Pied de page
 * Slug: debarras38/footer
 * Inserter: no
 *
 * Fond Encre : logo secondaire (lettres Crème, « 38 » Jaune caneton) et slogan en capitales.
 */

$accueil = esc_url( home_url( '/' ) );
$images  = esc_url( D38_URI . '/assets/img' );
?>
<!-- wp:group {"className":"d38-pied","backgroundColor":"encre","layout":{"type":"default"}} -->
<div class="wp-block-group d38-pied has-encre-background-color has-background"><!-- wp:group {"className":"d38-pied__grille","layout":{"type":"default"}} -->
<div class="wp-block-group d38-pied__grille"><!-- wp:group {"className":"d38-pied__marque","layout":{"type":"default"}} -->
<div class="wp-block-group d38-pied__marque"><!-- wp:image {"className":"d38-logo d38-logo--secondaire"} -->
<figure class="wp-block-image d38-logo d38-logo--secondaire"><img src="<?php echo $images; ?>/logo-secondary.svg" alt="Debarras38"/></figure>
<!-- /wp:image -->

<!-- wp:paragraph {"className":"d38-slogan"} -->
<p class="d38-slogan">On passe dans tous les coins</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"d38-pied__bloc","layout":{"type":"default"}} -->
<div class="wp-block-group d38-pied__bloc"><!-- wp:heading {"level":2,"className":"d38-pied__titre"} -->
<h2 class="wp-block-heading d38-pied__titre">Nous joindre</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"debarras38/coordonnees","args":{"key":"telephone_html"}}}}} -->
<p>06 07 31 30 41</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"debarras38/coordonnees","args":{"key":"email_html"}}}}} -->
<p>debarras38@hotmail.fr</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"debarras38/coordonnees","args":{"key":"adresse"}}}}} -->
<p>9 rue du Trident, 38100 Grenoble</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"debarras38/coordonnees","args":{"key":"horaires"}}}}} -->
<p></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"d38-pied__bloc","layout":{"type":"default"}} -->
<div class="wp-block-group d38-pied__bloc"><!-- wp:heading {"level":2,"className":"d38-pied__titre"} -->
<h2 class="wp-block-heading d38-pied__titre">Débarrassage et nettoyage</h2>
<!-- /wp:heading -->

<!-- wp:list {"className":"d38-pied__liens"} -->
<ul class="wp-block-list d38-pied__liens"><!-- wp:list-item -->
<li><a href="<?php echo $accueil; ?>#services">Débarrassage de maison et d’appartement</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $accueil; ?>#services">Débarrassage de cave, grenier et garage</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $accueil; ?>#services">Nettoyage après débarrassage</a></li>
<!-- /wp:list-item -->

<!-- wp:list-item -->
<li><a href="<?php echo $accueil; ?>#questions">Grenoble et toute l’Isère</a></li>
<!-- /wp:list-item --></ul>
<!-- /wp:list --></div>
<!-- /wp:group --></div>
<!-- /wp:group -->

<!-- wp:paragraph {"className":"d38-pied__mention"} -->
<p class="d38-pied__mention">© <?php echo esc_html( gmdate( 'Y' ) ); ?> Debarras38, débarrassage et nettoyage à Grenoble et en Isère. Site conçu par <a href="https://fablioo.com">Fablioo</a>.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->
