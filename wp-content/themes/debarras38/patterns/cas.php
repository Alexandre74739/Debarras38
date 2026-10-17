<?php
/**
 * Title: Avant, pendant, après (trois cartes, une mise en avant)
 * Slug: debarras38/cas
 * Categories: debarras38
 * Description: Un même chantier en trois photos : avant, pendant, après. La carte du milieu est mise en avant (Bleu canard, avec le canard). Cliquez sur une photo, puis sur « Remplacer » pour mettre la vôtre ; sa légende sert d’étiquette.
 */

$images = esc_url( D38_URI . '/assets/img' );
?>
<!-- wp:group {"tagName":"section","metadata":{"name":"Avant, pendant, après"},"align":"full","className":"d38-section d38-bord-vague d38-bord-coulure","backgroundColor":"vert-eau","layout":{"type":"default"}} -->
<section class="wp-block-group alignfull d38-section d38-bord-vague d38-bord-coulure has-vert-eau-background-color has-background"><!-- wp:group {"className":"d38-entete-section","layout":{"type":"default"}} -->
<div class="wp-block-group d38-entete-section"><!-- wp:heading -->
<h2 class="wp-block-heading">Débarrassage de maison en Isère&nbsp;: avant, pendant et après notre passage</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"d38-chapo"} -->
<p class="d38-chapo">Une maison, un appartement, une cave ou un grenier&nbsp;: voici comment se passe un débarrassage avec nous.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"d38-cartes d38-cartes\u002d\u002dtrio","layout":{"type":"default"}} -->
<div class="wp-block-group d38-cartes d38-cartes--trio"><!-- wp:group {"className":"d38-carte","backgroundColor":"creme","layout":{"type":"default"}} -->
<div class="wp-block-group d38-carte has-creme-background-color has-background"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">La visite, avant de chiffrer</h3>
<!-- /wp:heading -->

<!-- wp:image {"sizeSlug":"large","className":"d38-visuel"} -->
<figure class="wp-block-image size-large d38-visuel"><img src="<?php echo $images; ?>/photo-a-venir.svg" alt=""/><figcaption class="wp-element-caption">Avant</figcaption></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Nous faisons le tour avec vous&nbsp;: les pièces, les accès, les étages, et ce que vous souhaitez garder. Vous recevez ensuite un devis écrit, avant que le premier carton ne sorte.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"d38-carte d38-carte\u002d\u002davant","backgroundColor":"bleu-canard","layout":{"type":"default"}} -->
<div class="wp-block-group d38-carte d38-carte--avant has-bleu-canard-background-color has-background"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Le tri et le débarrassage, pièce par pièce</h3>
<!-- /wp:heading -->

<!-- wp:image {"sizeSlug":"large","className":"d38-visuel"} -->
<figure class="wp-block-image size-large d38-visuel"><img src="<?php echo $images; ?>/photo-a-venir.svg" alt=""/><figcaption class="wp-element-caption">Pendant</figcaption></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Nous trions sur place&nbsp;: ce qui peut resservir est mis de côté, le reste part en déchetterie. Puis nous sortons tout, du plus petit carton à l’armoire la plus lourde.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"d38-carte","backgroundColor":"creme","layout":{"type":"default"}} -->
<div class="wp-block-group d38-carte has-creme-background-color has-background"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Un logement vide et propre</h3>
<!-- /wp:heading -->

<!-- wp:image {"sizeSlug":"large","className":"d38-visuel"} -->
<figure class="wp-block-image size-large d38-visuel"><img src="<?php echo $images; ?>/photo-a-venir.svg" alt=""/><figcaption class="wp-element-caption">Après</figcaption></figure>
<!-- /wp:image -->

<!-- wp:paragraph -->
<p>Une fois les pièces vidées, nous balayons, nous dépoussiérons et nous lavons les sols, jusque dans les coins. Vous pouvez rendre les clés pour l’état des lieux ou la vente.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
