<?php
/**
 * Customizer control
 */

/**
 * Sorter Control
 */
class PSS_Customize_Control_Sorter extends WP_Customize_Control {

	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-sortable' );
	}

	public function render_content() { ?>
		<div class="pluton-sortable">
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if ( '' != $this->description ) { ?>
					<span class="description customize-control-description"><?php echo $this->description; ?></span>
				<?php } ?>
			</label>
			<?php
			// Get values and choices
			$values  = $this->value();
			$choices = $this->choices;
			// Turn values into array
			if ( ! is_array( $values ) ) {
				$values = explode( ',', $values );
			} ?>
			<ul id="<?php echo $this->id; ?>_sortable">
				<?php
				// Loop through values
				foreach ( $choices as $val => $label ) {
					$hide_sortee = '';
					$hide_icon   = 'fa fa-toggle-on';
					if ( ! in_array( $val, $values ) ) {
						$hide_sortee = ' pluton-hide';
						$hide_icon   = 'fa fa-toggle-on fa-rotate-180';
					} ?>
					<li data-value="<?php echo esc_attr( $val ); ?>" class="pluton-sortable-li<?php echo esc_attr( $hide_sortee ); ?>">
						<?php echo strip_tags( $label ); ?>
						<span class="pluton-hide-sortee dashicons <?php echo esc_attr( $hide_icon ); ?>"></span>
					</li>
				<?php } ?>
			</ul>
		</div><!-- .pluton-sortable -->
		<div class="clear:both"></div>
		<?php
		// Return values as comma seperated string for input
		if ( is_array( $values ) ) {
			$values = array_keys( $values );
			$values = implode( ',', $values );
		} ?>
		<input id="<?php echo $this->id; ?>_input" type='hidden' name="<?php echo $this->id; ?>" value="<?php echo esc_attr( $values ); ?>" <?php echo $this->get_link(); ?> />
		<script>
		jQuery(document).ready( function($) {
			"use strict";
			// Define variables
			var sortableUl = $( '#<?php echo $this->id; ?>_sortable' );

			// Create sortable
			sortableUl.sortable()
			sortableUl.disableSelection();

			// Update values on sortstop
			sortableUl.on( "sortstop", function( event, ui ) {
				pssUpdateSortableVal();
			} );

			// Toggle classes
			sortableUl.find( 'li' ).each( function() {
				$( this ).find( '.pluton-hide-sortee' ).click( function() {
					$( this ).toggleClass( 'fa-rotate-180' ).parents( 'li:eq(0)' ).toggleClass( 'pluton-hide' );
				} );
			})
			// Update Sortable when hidding/showing items
			$( '#<?php echo $this->id; ?>_sortable span.pluton-hide-sortee' ).click( function() {
				pssUpdateSortableVal();
			} );
			// Used to update the sortable input value
			function pssUpdateSortableVal() {
				var values = [];
				sortableUl.find( 'li' ).each( function() {
					if ( ! $( this ).hasClass( 'pluton-hide' ) ) {
						values.push( $( this ).attr( 'data-value' ) );
					}
				} );
				$( '#<?php echo $this->id; ?>_input' ).val(values).trigger( 'change' );
			}
		} );
		</script>
		<?php
	}
}