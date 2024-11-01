<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.zaigoinfotech.com
 * @since             1.0.0
 * @package           Zippy_Form
 *
 * @wordpress-plugin
 * Plugin Name:       Zippy Form
 * Plugin URI:        https://demo.zippyform.io
 * Description:       An interface used for viewing and submitting dynamic forms created on zippy form builder
 * Version:           1.0.1
 * Author:            Zaigo infotech
 * Author URI:        https://www.zaigoinfotech.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       zippy-form
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


define( 'ZIPPY_FORM_VERSION', '1.0.1' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-zippy-form-activator.php
 */
function zippy_activate_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zippy-form-activator.php';
	Zippy_Form_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-zippy-form-deactivator.php
 */
function zippy_deactivate_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-zippy-form-deactivator.php';
	Zippy_Form_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'zippy_activate_form' );
register_deactivation_hook( __FILE__, 'zippy_deactivate_form' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-zippy-form.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function zippy_run_form() {

	$plugin = new Zippy_Form();
	$plugin->run();

}
zippy_run_form();


add_action('wp_enqueue_scripts', 'zippy_scripts', 99999);

function zippy_scripts() {

    wp_enqueue_script("zippy-js", plugin_dir_url(__FILE__) . 'public/js/zippy-form-public.js', array('jquery'), ZIPPY_FORM_VERSION, true);
    wp_enqueue_script('flatpickr-js', plugin_dir_url(__FILE__) . 'public/js/flatpickr.min.js', array('jquery'), ZIPPY_FORM_VERSION, true);
    
    wp_enqueue_style('flatpickr-css', plugin_dir_url(__FILE__) . 'public/css/flatpickr.min.css', array(), ZIPPY_FORM_VERSION);
    
    wp_localize_script('zippy-js', 'zippyApiUrl', array(
        'optionValue' => get_option('zippy_form_base_url'),
    ));

    wp_localize_script('zippy-js', 'my_script_vars', array('ajax_url' => admin_url('admin-ajax.php')));
}

function zippy_form_shortcode_callback( $atts ) {
	$atts = shortcode_atts(
		array(
			'id' => 'no',
			
		), $atts, 'Zippy_Form' );
		$output = '';
$form_id = $atts['id'];
$formApiUrl = esc_url(get_option('zippy_form_base_url'));
$license = esc_attr(get_option("zippy_form_license_key"));
    $args = [
      "headers" => [
          "Content-Type" => "application/json",
          "ZF-SECRET-KEY" => $license,
      ],
  ];
$step_id = '1';
$formapi = $formApiUrl . '/dynamic-form/' . $form_id . '/fields';
$getdata = wp_remote_get($formapi,$args);
$response_code = wp_remote_retrieve_response_code($getdata);
$getbody = wp_remote_retrieve_body($getdata);
$datas = json_decode($getbody, true);
$alldata = isset($datas['data']) ? $datas['data'] : [];
$data = isset($alldata['form']['name']) ? $alldata['form']['name'] : '';
$recaptcha = isset($alldata['form']['recaptcha_enabled']) ? $alldata['form']['recaptcha_enabled'] : '';
$recaptchaKey = isset($alldata['form']['recaptcha_site_key']) ? $alldata['form']['recaptcha_site_key'] : '';
$random_zippyCode = wp_rand(10000, 99999);
$zfCode = 'zf' . $random_zippyCode;
$formType = isset($alldata['form']['type']) ? $alldata['form']['type'] : '';
$zippyBrand = isset($alldata['zippy_form_branding']) ? $alldata['zippy_form_branding'] : '';
if($zippyBrand){
	$image_url = plugins_url('public/img/brandinglabel.svg', __FILE__);
	$ZippyBrandimg = '<div id="zippyBrandimg" style="text-align:center !important;position: relative; z-index: 9999; display: block !important;margin-top:10px"><a href="https://zippyform.io/" style="color: unset !important;text-decoration: none;"><span style="font-size: 14px;margin-right: 10px;vertical-align: middle;">POWERED BY</span><img src="' . esc_url($image_url) . '" alt="Zippy Brand" style="display: inline-block !important;"></a></div>';
} else {
	$ZippyBrandimg = '';
}
if ($formType == 'payment_form') {
	wp_enqueue_script('strip-js', 'https://js.stripe.com/v3/',array(),null,	true);
    $ZippyCheckout = '<div id="zippy-checkout' . $zfCode . '"></div>';
    $ZippySubmit = 'Proceed to Checkout';
} else {
    $ZippyCheckout = '';
    $ZippySubmit = 'Submit';
}

$steps = isset($alldata['steps']) ? $alldata['steps'] : [];
if ($response_code == 200) {
    $output = '<div class="container"><div class="row"><div id="field_form" class="zippy-form col-md-12 "><input type="hidden" id="sumission_id' . esc_attr($form_id) . '" value=""><input type="hidden" value="' . count($steps) . '" id="totalStep' . esc_attr($form_id) . '"><form id="regForm" action="" class="form' . esc_attr($form_id) . ' ' . esc_attr($zfCode) . '">';
    $fcount = count($steps);
    if ($fcount > 1) {
        $output .= '<div class="progress-container"><div class="progress-steps" id="progress-steps"></div>';
        foreach ($steps as $key => $val) {
            $step_count = $key + 1;
            $active_class = ($step_count == 1) ? 'active' : '';
            $output .= '<div class="step  ' . $active_class . '" data-desc="' . esc_attr($val['name']) . '">' . $step_count . '</div>';
        }
        $output .= '</div>';
    }
    foreach ($steps as $val => $step) {
        $c = $val + 1;
        $d_class = ($c > 1) ? 'display:none' : '';
        $s_id = $step['id'];
        $form_api = $formApiUrl . '/dynamic-form/' . $form_id . '/fields/' . $s_id;
        $stepgetdata = wp_remote_get($form_api,$args);
        $step_response_code = wp_remote_retrieve_response_code($stepgetdata);
        $step_getbody = wp_remote_retrieve_body($stepgetdata);
        $step_datas = json_decode($step_getbody, true);
        $step_alldata = isset($step_datas['data']) ? $step_datas['data'] : [];
        $step_data = isset($step_alldata['fields']) ? $step_alldata['fields'] : [];
        $step_name = $step['name'];

        if ($step_response_code == 200) {
            $output .= '<div class="tab" data-step-id="' . esc_attr($s_id) . '" data-form-id="' . esc_attr($form_id) . '" id="step' . esc_attr($c) . '" style="' . esc_attr($d_class) . '">';
            $output .= '<div id="form-id" style="display:none">' . esc_attr($form_id) . '</div>';
            $output .= '<div id="step-id" style="display:none">' . esc_attr($s_id) . '</div>';
            $output .= '<div id="step-number" style="display:none">' . esc_attr($c) . '</div>';
            $output .= '<div class="row step' . esc_attr($s_id) . '">';

            $placeholders = [];
            if ($val === $fcount - 1) {
                if ($recaptcha) {
                    wp_enqueue_script(
                        'google-recaptcha',
                        'https://www.google.com/recaptcha/api.js?render=' . esc_attr($recaptchaKey),
                        array(),
                        null,
                        true
                    );
                    $output .= '<div id="' . esc_attr($zfCode) . '-grecaptcha" data-site-key="' . esc_attr($recaptchaKey) . '"></div>';
                }
            }	
			foreach ($step_data as $step_datas) {
				$type = isset($step_datas['field_type']) ? esc_attr($step_datas['field_type']) : '';
			
				$placeholders = isset($step_datas['placeholder']) ? esc_attr($step_datas['placeholder']) : '';
				
				$f_format = isset($step_datas['field_format']['input_group_icon']) ? esc_attr($step_datas['field_format']['input_group_icon']) : '';
			
				$format_text_start = '';
				$format_text_end = '';
			
				if (!empty($f_format) && !empty($step_datas['field_format']['input_group_icon_position'])) {
					if ($step_datas['field_format']['input_group_icon_position'] === 'start') {
						$format_text_start = '<span class="input-group-text bg-white border-0 pe-1"><i class="fa ' . esc_attr($f_format) . '"></i></span>';
					}
			
					if ($step_datas['field_format']['input_group_icon_position'] === 'end') {
						$format_text_end = '<span class="input-group-text bg-white border-0 pe-1"><i class="fa ' . esc_attr($f_format) . '"></i></span>';
					}
				}
			
				$label = isset($step_datas['label']) ? esc_html($step_datas['label']) : '';
				$class_name = isset($step_datas['class_name']) ? esc_attr($step_datas['class_name']) : '';
				$field_id = isset($step_datas['field_id']) ? esc_attr($step_datas['field_id']) : '';
				$required = isset($step_datas['validations']['required']) ? esc_attr($step_datas['validations']['required']) : '';
			
				$require = (!empty($required)) ? 'require' : '';
				$r_text = (!empty($required)) ? '<span class="require-text">*</span>' : '';
				
				switch ($type) {
					case "text_box":
						$output .= '<div class="' . esc_attr($class_name) . ' form-element"> <label>' . esc_html($label) . $r_text . '</label> <div class="input-group mb-3 ">' . $format_text_start . '<input id="' . esc_attr($field_id) . '" type="text" data-label="' . esc_attr($label) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' form-control"  minlength="' . esc_attr($step_datas['validations']['minlength']) . '" maxlength="' . esc_attr($step_datas['validations']['maxlength']) . '">' . $format_text_end . '</div></div>';
						break;
					case "short_text_area":
						$output .= '<div class="' . esc_attr($class_name) . ' form-element"><label>' . esc_html($label) . $r_text . '</label><div class="input-group mb-3 "><textarea id="' . esc_attr($field_id) . '"  class="' . esc_attr($require) . ' ' . esc_attr($class_name) . ' form-control" data-label="' . esc_attr($label) . '" minlength="' . esc_attr($step_datas['validations']['minlength']) . '" maxlength="' . esc_attr($step_datas['validations']['maxlength']) . '" placeholder="' . esc_attr($placeholders) . '"></textarea></div></div>';
						break;
					case "text_area":
						$output .= '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . $r_text . '</label><div class="input-group mb-3 "><textarea id="' . esc_attr($field_id) . '"  class="' . esc_attr($require) . '  form-control" data-label="' . esc_attr($label) . '" minlength="' . esc_attr($step_datas['validations']['minlength']) . '" maxlength="' . esc_attr($step_datas['validations']['maxlength']) . '" placeholder="' . esc_attr($placeholders) . '"></textarea></div></div>';
						break;
					case "number":
						$decimal = $step_datas['validations']['decimal'];
						$decimalPlace = $step_datas['validations']['decimal_places'];
						if ($decimal) {
							if ($decimalPlace > 0) {
								$step = '0.' . @str_repeat('0', $decimalPlace - 1) . '1';
							} else {
								$step = 1;
							}
						} else {
							$step = 1;
						}
						if ($decimalPlace > 0) {
							$output .= '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . $r_text . '</label><div class="input-group mb-3 ">' . $format_text_start . '<input type="text" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '"  placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' zippy-decimal form-control" min="' . esc_attr($step_datas['validations']['min']) . '" max="' . esc_attr($step_datas['validations']['max']) . '" step="' . esc_attr($step) . '" data-decimal="' . esc_attr($decimalPlace) . '" >' . $format_text_end . '</div></div>';
						} else {
							$output .= '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . $r_text . '</label><div class="input-group mb-3 ">' . $format_text_start . '<input type="number" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" oninput="formatNumber(this, ' . esc_attr($decimalPlace) . ')" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' zippy-number-field form-control" min="' . esc_attr($step_datas['validations']['min']) . '" max="' . esc_attr($step_datas['validations']['max']) . '" step="' . esc_attr($step) . '" data-decimal="' . esc_attr($decimalPlace) . '">' . $format_text_end . '</div></div>';
						}
						break;
					case "email":
						$output .= '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . $r_text . '</label><div class="input-group mb-3 ">' . $format_text_start . ' <input required type="email" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' form-control">' . $format_text_end . '</div></div>';
						break;
					case "phone_number":
						$output .= '<div class="' . esc_attr($class_name) . ' form-element"> <label>' . esc_html($label) . $r_text . '</label> <div class="input-group mb-3 ">' . $format_text_start . '<input id="' . esc_attr($field_id) . '" type="tel" data-label="' . esc_attr($label) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' form-control zippy-phone-number"  minlength="' . esc_attr($step_datas['validations']['minlength']) . '" maxlength="' . esc_attr($step_datas['validations']['maxlength']) . '" pattern="[0-9]+">' . $format_text_end . '</div></div>';
						break;
					case "website_url":
						$output .= '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . $r_text . '</label><div class="input-group mb-3 ">' . $format_text_start . '<input type="url" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' form-control">' . $format_text_end . '</div></div>';
						break;
					case "dropdown":
						$option = $step_datas['options'];
						if ($step_datas['validations']['max_selection'] == 1) {
							$output .= '<div class="' . esc_attr($class_name) . ' "><label>' . esc_html($label) . $r_text . '</label><select data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" class="form-control single-select" name="single-select">';
							foreach ($option as $options) {
								$output .= '<option value="' . esc_attr($options['value']) . '">' . esc_html($options['label']) . '</option>';
							}
					
							$output .= '</select></div>';
						} else {
							$t = $field_id;
							$output .= '<div class="' . esc_attr($class_name) . ' "><label>' . esc_html($label) . $r_text . '</label><select  data-label="' . esc_attr($label) . '" placeholder="' . esc_attr($placeholders) . '" id="' . esc_attr($field_id) . '" name="test[]"  class="choices-multiple-remove-button ' . esc_attr($require) . '" multiple  data-max-selected-options="' . esc_attr($step_datas['validations']['max_selection']) . '" ><option value="" >' . esc_html($placeholders) . '</option>';
							foreach ($option as $options) {
								$output .= '<option value="' . esc_attr($options['value']) . '">' . esc_html($options['label']) . '</option>';
							}
					
							$output .= '</select></div>';
						}
						break;
					case "multiselect_checkbox":
						$option = isset($step_datas['options']) ? $step_datas['options'] : [];
						$maxSelection = isset($step_datas['validations']['max_selection']) ? ' data-max-selected-options="' . esc_attr($step_datas['validations']['max_selection']) . '"' : '';
					
						$output .= '<div class="' . esc_attr($class_name) . ' zippyCheckBoxes-option"><label>' . esc_html($label) . $r_text . '</label><div class="zippyCheckBoxes"' . $maxSelection . ' data-label="' . esc_attr($label) . '">';
					
						foreach ($option as $key => $options) {
							$re = ($key === 0) ? $require : '';
					
							$output .= '<input data-label="' . esc_attr($label) . '" data-id="' . esc_attr($field_id) . '" type="checkbox" id="' . esc_attr($zfCode . '-' . $options['label']) . '" name="' . esc_attr($field_id) . '" value="' . esc_attr($options['value']) . '" class="' . esc_attr($re) . '"><label for="' . esc_attr($zfCode . '-' . $options['label']) . '">' . esc_html($options['label']) . '</label>';
						}
					
						$output .= '</div></div>';
						break;
					
					case "radio":
						$option = isset($step_datas['options']) ? $step_datas['options'] : [];
					
						$output .= '<div class="' . esc_attr($class_name) . ' radio-options"><label>' . esc_html($label) . $r_text . '</label><div class="radioButtons">';
					
						foreach ($option as $key => $options) {
							$re = (!empty($required) && $key === 0) ? 'require' : '';
					
							$output .= '<input data-label="' . esc_attr($label) . '" data-id="' . esc_attr($field_id) . '" type="radio" id="' . esc_attr($zfCode . '-' . $options['label']) . '" name="' . esc_attr($field_id) . '" value="' . esc_attr($options['value']) . '" class="' . esc_attr($re) . '"><label for="' . esc_attr($zfCode . '-' . $options['label']) . '">' . esc_html($options['label']) . '</label>';
						}
					
						$output .= '</div></div>';
						break;
					
					case "date":
						$date_format = isset($step_datas['validations']['date_format']) ? $step_datas['validations']['date_format'] : '';
						$placeholder = '';
					
						if ($date_format == 'm-d-Y') {
							$placeholder = 'MM-DD-YYYY';
						} elseif ($date_format == 'd-m-Y') {
							$placeholder = 'DD-MM-YYYY';
						}
					
						$output .= '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . $r_text . '</label><div class="input-group mb-3 "><input type="text" data-format="' . esc_attr($date_format) . '" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholder) . '" class="' . esc_attr($require) . ' zippy-date-input form-control"  ></div></div>';
						break;
					
					case "time":
						$timeFormat = isset($step_datas['validations']['time_format']) ? $step_datas['validations']['time_format'] : '';
						$placeholders = isset($placeholders) ? $placeholders : '';
					
						if ($timeFormat == 12) {
							$output .= '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . $r_text . '</label><div class="input-group mb-3 " ><input type="text" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' time_12format form-control" data-label="' . esc_attr($label) . '"></div></div>';
						} else {
							$output .= '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . $r_text . '</label><div class="input-group mb-3 " ><input type="text" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' time_24format form-control" data-label="' . esc_attr($label) . '"></div></div>';
						}
						break;
					
					case "file":
						$filetype = isset($step_datas['validations']['file_extensions_allowed']) ? $step_datas['validations']['file_extensions_allowed'] : [];
						$f_type = implode(",.", $filetype);
						$output .= '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . $r_text . '</label><div class="input-group mb-3 zippy-file-upload"><div class="file-input"><span class="fake-btn"><img src="' . esc_url(plugin_dir_url(__FILE__) . 'public/img/upload.png') . '"></span><span class="file-msg">' . esc_html($placeholders) . '</span></div><input type="file" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' zippy-file-input" data-extention=".' . esc_attr($f_type) . '" accept=".' . esc_attr($f_type) . '" data-max="' . esc_attr($step_datas['validations']['file_max_size_mb']) . '"></div></div>';
						break;
					
					case "hidden":
						$output .= '<input type="hidden" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($class_name) . '" value="">';
						break;
					
					case "heading":
						$output .= '<div class="' . esc_attr($class_name) . '"><' . esc_attr($step_datas['content_size']) . ' style="text-align:' . esc_attr($step_datas['content_alignment']) . ' !important">' . esc_html($step_datas['content']) . '</' . esc_attr($step_datas['content_size']) . '></div>';
						break;
					
					case "paragraph":
						$tag = isset($step_datas['content_size']) ? $step_datas['content_size'] : '';
						$output .= '<div class="' . esc_attr($class_name) . '"><p style="text-align:' . esc_attr($step_datas['content_alignment']) . ' !important;font-size:' . esc_attr($step_datas['content_size']) . 'px !important">' . esc_html($step_datas['content']) . '</p></div>';
						break;
					
					default:
						$output .= "";
						break;
				  }

				 
				 
			}
			$output .='<div class="mt-4 zippyBottom" style="overflow:auto;">
			<div style="justify-content: space-between;" class="zippyBottomOuter">';
			if ($fcount == 1) {
				$output .= '<div style="float:right" class="zippyBottomInner"><button type="button" data-step-no="1" data-step-id="' . esc_attr($s_id) . '" data-form-id="' . esc_attr($form_id) . '" id="nextBtn" data-zippy-code="' . esc_attr($zfCode) . '" onclick="submitForm(this)">' . esc_html($ZippySubmit) . '</button></div>';
			} else {
				if ($c == 1) {
					$step_c = $c + 1;
					$output .= '<div style="float:right" class="zippyBottomInner"><button type="button" id="nextBtn" data-step-no="' . esc_attr($c) . '" data-step-id="' . esc_attr($s_id) . '" data-form-id="' . esc_attr($form_id) . '" data-zippy-code="' . esc_attr($zfCode) . '" onclick="nextStep(this)">Next > </button></div>';
				} elseif ($c == $fcount) {
					$p_step = $c - 1;
					$output .= '<div style="display:flex;justify-content: space-between;" class="zippyBottomOuter"><button type="button" id="prevBtn" data-step-no="' . esc_attr($c) . '" data-step-id="' . esc_attr($s_id) . '" data-form-id="' . esc_attr($form_id) . '" data-zippy-code="' . esc_attr($zfCode) . '" onclick="previousStep(this)"> < Previous</button>';
					$output .= '<button type="button" id="nextBtn" data-step-no="' . esc_attr($c) . '" data-step-id="' . esc_attr($s_id) . '" data-form-id="' . esc_attr($form_id) . '" id="nextBtn" data-zippy-code="' . esc_attr($zfCode) . '" onclick="submitForm(this)">' . esc_html($ZippySubmit) . '</button></div>';
				} else {
					$step_c = $c + 1;
					$p_step = $c - 1;
					$output .= '<div style="display:flex;justify-content: space-between;" class="zippyBottomOuter"><button type="button" id="prevBtn" data-step-no="' . esc_attr($c) . '" data-step-id="' . esc_attr($s_id) . '" data-form-id="' . esc_attr($form_id) . '" data-zippy-code="' . esc_attr($zfCode) . '" onclick="previousStep(this)"> < Previous</button>';
					$output .= '<button type="button" id="nextBtn" data-step-no="' . esc_attr($c) . '" data-step-id="' . esc_attr($s_id) . '" data-zippy-code="' . esc_attr($zfCode) . '" data-form-id="' . esc_attr($form_id) . '" onclick="nextStep(this)">Next > </button></div>';
				}
			}
			
			$output.='</div>
		  </div>';

			}
			$output .= $ZippyBrandimg;
			$output .='</div></div>';

		
}
		
	  $output .='</form></div>'.$ZippyCheckout.'</div></div>';
		}
		else if($response_code == 400 || $response_code == 404 ){
			$output ='<p style="text-align:center">No active fields mapped to the form step</p>';
		}else if($response_code == 500  ){
			$output ='<p style="text-align:center">Internal Server Error. Please Contact Site Admin</p>';
		}

	
		return $output;
}
add_shortcode( 'zippy_form', 'zippy_form_shortcode_callback' );

function zippyform_mail_function() {
    if (isset($_POST['form_id']) && isset($_POST['formsubmitId'])) {
        $form_id = sanitize_text_field($_POST['form_id']);
        $formsubmitId = sanitize_text_field($_POST['formsubmitId']);

        $formApiUrl = esc_url(get_option('zippy_form_base_url'));
        $license = esc_attr(get_option('zippy_form_license_key'));

        $args = [
            'headers' => [
                'Content-Type' => 'application/json',
                'ZF-SECRET-KEY' => $license,
            ],
        ];
        
        $formapi = $formApiUrl . '/builder/' . $form_id . '/submission_detail/' . $formsubmitId;

        $getdata = wp_remote_get($formapi, $args);
        $response_code = wp_remote_retrieve_response_code($getdata);
        $getbody = wp_remote_retrieve_body($getdata);
        $data = json_decode($getbody, true);

        if (is_wp_error($getdata)) {
            return false; // Failure
        } else if ($response_code == 200 && isset($data['status']) && $data['status'] === 'success') {
            $subject = 'Form Submission Details';
            $to = get_option('admin_email');
            $headers = array('Content-Type: text/html; charset=UTF-8');

            $skipFirst = true; 
            $content = '';

            foreach ($data['data'] as $item) {
                if ($skipFirst) {
                    $skipFirst = false;
                    continue; 
                }
                
                $content .= '<strong>' . esc_html($item['key']) . ':</strong> ' . esc_html($item['value']) . '<br>';
            }

            // The email message is now just the content of key-value pairs
            $email_message = '
                <div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; color: #555;">
                    <p><strong>Form Submission Details:</strong></p>
                    <p>' . $content . '</p>
                </div>';

            // Send the email
            if (wp_mail($to, $subject, $email_message, $headers)) {
                return true; // Success
            } else {
                return false; // Failure
            }
        } else {
            return false; // Failure
        }
    } else {
        return false; // Failure
    }
}

function zippy_ajax_action() {
    $form_id = isset( $_POST['form_id'] ) ? sanitize_text_field( $_POST['form_id'] ) : '';
    if ( empty( $form_id ) ) {
        wp_send_json_error( 'Form ID is empty' );
    }

    do_action( 'zippy_mail_hook' );

    $hook_name = 'zippy_custom_hook_' . $form_id;
    do_action( $hook_name );

    wp_die();
}

add_action('zippy_mail_hook', 'zippyform_mail_function');

add_action('wp_ajax_zippy_ajax_action', 'zippy_ajax_action');
add_action('wp_ajax_nopriv_zippy_ajax_action', 'zippy_ajax_action');
