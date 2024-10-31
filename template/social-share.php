<?php
/**
 * Social Share Buttons Output
 *
 * @package    Pluton
 * @author     Nick
 * @copyright  Copyright (c) 2015, Nick
 * @license    http://www.gnu.org/licenses/gpl-2.0.html
 * @since      1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Disabled if post is password protected or if disabled
if ( post_password_required() ) {
	return;
}

// Get sharing sites
$sites = pss_social_share_sites();

// Return if there aren't any sites enabled
if ( empty( $sites ) ) {
	return;
}

// Declare main vars
$heading  = pss_social_share_heading();
$post_id  = get_the_ID();
$url      = apply_filters( 'pss_social_share_url', get_permalink( $post_id ) );
$title    = get_the_title();

// Get and encode summary
$summary = pluton_get_excerpt( array(
	'length'          => '40',
	'echo'            => false,
	'ignore_more_tag' => true,
) ); ?>

<div class="entry-share clr">

	<?php
	// Display heading 
	pluton_heading( array(
		'content'		=> $heading,
		'tag'			=> 'h2',
		'classes'		=> array( 'social-share-title' ),
		'apply_filters'	=> 'social_share',
	) ); ?>

	<ul class="pss-social-share clr">

		<?php
		// Loop through sites
		foreach ( $sites as $site ) :

			// Twitter
			if ( 'twitter' == $site ) {

				// Get SEO meta and use instead if they exist
				if ( defined( 'WPSEO_VERSION' ) ) {
					if ( $meta = get_post_meta( $post_id, '_yoast_wpseo_twitter-title', true ) ) {
						$title = $meta;
					}
					if ( $meta = get_post_meta( $post_id, '_yoast_wpseo_twitter-description', true ) ) {
						$title = $title .': '. $meta;
						$title = rawurlencode( $title );
					}
				}

				// Get twitter handle
				$handle = get_theme_mod( 'pss_social_share_twitter_handle' ); ?>

				<li class="twitter">
					<a href="http://twitter.com/share?text=<?php echo rawurlencode( $title ); ?>&amp;url=<?php echo rawurlencode( esc_url( $url ) ); ?><?php if ( $handle ) echo '&amp;via='. esc_attr( $handle ); ?>" title="<?php esc_html_e( 'Share on Twitter', 'pluton-social-sharing' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<span class="fa fa-twitter"></span>
					</a>
				</li>

			<?php }
			// Facebook
			elseif ( 'facebook' == $site ) { ?>

				<li class="facebook">
					<a href="http://www.facebook.com/share.php?u=<?php echo rawurlencode( esc_url( $url ) ); ?>" title="<?php esc_html_e( 'Share on Facebook', 'pluton-social-sharing' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<span class="fa fa-facebook"></span>
					</a>
				</li>

			<?php }
			// Google+
			elseif ( 'google_plus' == $site ) { ?>

				<li class="googleplus">
					<a href="https://plus.google.com/share?url=<?php echo rawurlencode( esc_url( $url ) ); ?>" title="<?php esc_html_e( 'Share on Google+', 'pluton-social-sharing' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<span class="fa fa-google-plus"></span>
					</a>
				</li>

			<?php }
			// Pinterest
			elseif ( 'pinterest' == $site ) { ?>

				<li class="pinterest">
					<a href="https://www.pinterest.com/pin/create/button/?url=<?php echo rawurlencode( esc_url( $url ) ); ?>&amp;media=<?php echo wp_get_attachment_url( get_post_thumbnail_id( $post_id ) ); ?>&amp;description=<?php echo rawurlencode( $summary ); ?>" title="<?php esc_html_e( 'Share on Pinterest', 'pluton-social-sharing' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<span class="fa fa-pinterest-p"></span>
					</a>
				</li>

			<?php }
			// LinkedIn
			elseif ( 'linkedin' == $site ) { ?>

				<li class="linkedin">
					<a href="http://www.linkedin.com/shareArticle?mini=true&amp;url=<?php echo rawurlencode( esc_url( $url ) ); ?>&amp;title=<?php echo rawurlencode( $title ); ?>&amp;summary=<?php echo rawurlencode( $summary ); ?>&amp;source=<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php esc_html_e( 'Share on LinkedIn', 'pluton-social-sharing' ); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;">
						<span class="fa fa-linkedin"></span>
					</a>
				</li>

			<?php } ?>

		<?php endforeach; ?>

	</ul>

</div><!-- .entry-share -->