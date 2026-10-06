<?php
/**
 * Title: Demande de devis (téléphone et formulaire)
 * Slug: debarras38/contact
 * Categories: debarras38
 * Description: Le numéro en grand à gauche, le formulaire à droite. Les demandes arrivent par e-mail et dans Debarras38 → Demandes.
 */
?>
<!-- wp:group {"tagName":"section","metadata":{"name":"Demande de devis"},"align":"full","className":"d38-section d38-contact d38-bord-vague","backgroundColor":"encre","layout":{"type":"default"}} -->
<section id="devis" class="wp-block-group alignfull d38-section d38-contact d38-bord-vague has-encre-background-color has-background"><!-- wp:group {"className":"d38-contact__texte","layout":{"type":"default"}} -->
<div class="wp-block-group d38-contact__texte"><!-- wp:heading -->
<h2 class="wp-block-heading">Racontez-nous les lieux, nous passons voir.</h2>
<!-- /wp:heading -->

<!-- wp:paragraph {"className":"d38-chapo"} -->
<p class="d38-chapo">Un appel ou un message suffit pour commencer. Vous décrivez la cave, le grenier ou la maison, nous venons sur place et vous recevez un devis écrit.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"className":"d38-numero","metadata":{"bindings":{"content":{"source":"debarras38/coordonnees","args":{"key":"telephone_html"}}}}} -->
<p class="d38-numero">06 79 37 20 77</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"metadata":{"bindings":{"content":{"source":"debarras38/coordonnees","args":{"key":"email_html"}}}}} -->
<p>debarras38@hotmail.fr</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"d38-contact__formulaire","backgroundColor":"creme","layout":{"type":"default"}} -->
<div class="wp-block-group d38-contact__formulaire has-creme-background-color has-background"><!-- wp:shortcode -->
[d38_formulaire]
<!-- /wp:shortcode --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->
