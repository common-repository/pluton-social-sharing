<?php
/**
 * Returns social sharing template part
 */
function pss_social_share_sites() {
    $sites = get_theme_mod( 'pss_social_share_sites', array( 'twitter', 'facebook', 'google_plus', 'pinterest', 'linkedin' ) );
    $sites = apply_filters( 'pss_social_share_sites_filter', $sites );
    if ( $sites && ! is_array( $sites ) ) {
        $sites = explode( ',', $sites );
    }
    return $sites;
}

/**
 * Returns the social share heading
 */
function pss_social_share_heading() {
    $heading = get_theme_mod( 'pss_social_share_heading' );
    $heading = $heading ? $heading : esc_html__( 'Please Share This', 'pluton-social-sharing' );
    return apply_filters( 'pss_social_share_heading', $heading );
}