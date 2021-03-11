<?php
/**
 * Datepicker field.
 * 
 * @package WP_Travel_Engine
 */
class WP_Travel_Engine_Form_Field_Date extends WP_Travel_Engine_Form_Field_Text {
	protected $field;
	protected $field_type = 'text';
	function init( $field ) {
		$this->field = $field;
		$this->field['attributes']['autocomplete'] = 'off';
		return $this;
	}

	function render( $display = true ) {
		$output = parent::render( false );

		$max_today = isset( $this->field['attributes'] ) && isset( $this->field['attributes']['data-max-today'] ) ? $this->field['attributes']['data-max-today'] : '';
		$output   .= '<script>';
		$output   .= 'jQuery(function($){ ';
		$output   .= '$("#' . $this->field['id'] . '").datepicker({';
		$output   .= "dateFormat: 'yy-mm-dd',";
		if ( '' !== $max_today && true == $max_today ) {
			$output .= 'maxDate: new Date(),';
		} else if( '' !== $max_today && false == $max_today ) {
			$output .= 'minDate: new Date(),';
		}

		$output .= '});';
		$output .= '} );';
		$output .= '</script>';

		if ( ! $display ) {
			return $output;
		}

		echo $output;
	}

}
