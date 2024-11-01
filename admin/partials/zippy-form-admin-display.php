<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.zaigoinfotech.com
 * @since      1.0.0
 *
 * @package    Zippy_Form
 * @subpackage Zippy_Form/admin/partials
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>


<div class="wrap">
		        <div id="icon-themes" class="icon32"></div>  
		        <h2><b>Preview Form</b></h2>
            <?php
            $form_id = esc_attr($formID);
    $output = '';  
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
    $getdata = @wp_remote_get($formapi,$args);
    $response_code = @wp_remote_retrieve_response_code($getdata);
    $getbody = @wp_remote_retrieve_body($getdata);
    $datas = @json_decode($getbody, true);
    $alldata = isset($datas['data']) ? $datas['data'] : [];
    $data = isset($alldata['form']['name']) ? $alldata['form']['name'] : '';
    $random_zippyCode = wp_rand(10000, 99999);
    $zfCode = 'zf' . $random_zippyCode;
    $formType = isset($alldata['form']['type']) ? $alldata['form']['type'] : '';

    if ($formType == 'payment_form') {
        $ZippyCheckout = '<div id="zippy-checkout' . esc_attr($zfCode) . '"></div>';
        $ZippySubmit = 'Proceed to Checkout';
    } else {
        $ZippyCheckout = '';
        $ZippySubmit = 'Submit';
    }

    $steps = isset($alldata['steps']) ? $alldata['steps'] : [];

    if ($response_code == 200) {
      echo '<div class="container"><div class="row"><div id="field_form" class="zippy-form col-md-12"><input type="hidden" id="sumission_id" value=""><input type="hidden" value="' . esc_attr(count($steps)) . '" id="totalStep"><form id="regForm" action="">';
        $fcount = count($steps);

        if ($fcount > 1) {
          echo '<div class="progress-container">
            <div class="progress-steps" id="progress-steps"></div>';

            foreach ($steps as $key => $val) {
                $step_count = $key + 1;
                $active_class = ($step_count == 1) ? 'active' : '';
                echo '<div class="step ' . esc_attr($active_class) . '" data-desc="' . esc_html($val['name']) . '">' .esc_html($step_count) . '</div>';
            }

            echo '</div>';
        }

        foreach ($steps as $val => $step) {
            $c = $val + 1;
            $d_class = ($c > 1) ? 'display:none' : '';

            $s_id = isset($step['id']) ? $step['id'] : '';
            $form_api = $formApiUrl . '/dynamic-form/' . $form_id . '/fields/' . $s_id;
            $stepgetdata = wp_remote_get($form_api,$args);
            $step_response_code = wp_remote_retrieve_response_code($stepgetdata);
            $step_getbody = wp_remote_retrieve_body($stepgetdata);
            $step_datas = json_decode($step_getbody, true);
            $step_alldata = isset($step_datas['data']) ? $step_datas['data'] : [];
            $step_data = isset($step_alldata['fields']) ? $step_alldata['fields'] : [];
            $step_name = isset($step['name']) ? $step['name'] : '';

            if ($step_response_code == 200) {
              echo '<div class="tab" id="step' . esc_attr($c) . '" style="' . esc_attr($d_class) . '">';
                echo '<div id="form-id" style="display:none">' . esc_attr($form_id) . '</div>';
                echo '<div id="step-id" style="display:none">' . esc_attr($s_id) . '</div>';
                echo '<div id="step-number" style="display:none">' . esc_attr($c) . '</div>';
                echo '<div class="row">';
                $placeholders = [];
				
			foreach($step_data as $step_datas){
				
				$type = isset($step_datas['field_type']) ? $step_datas['field_type'] : '';

                $placeholders = isset($step_datas['placeholder']) ? esc_attr($step_datas['placeholder']) : '';

                $f_format = isset($step_datas['field_format']['input_group_icon']) ? esc_attr($step_datas['field_format']['input_group_icon']) : '';

                $format_text_start = '';
                $format_text_end = '';

                if (!empty($f_format)) {
                    if (!empty($step_datas['field_format']['input_group_icon_position']) && $step_datas['field_format']['input_group_icon_position'] === 'start') {
                        $format_text_start = '<span class="input-group-text bg-white border-0 pe-1"><i class="fa ' . esc_attr($step_datas['field_format']['input_group_icon']) . '"></i></span>';
                    }

                    if (!empty($step_datas['field_format']['input_group_icon_position']) && $step_datas['field_format']['input_group_icon_position'] === 'end') {
                        $format_text_end = '<span class="input-group-text bg-white border-0 pe-1"><i class="fa ' . esc_attr($step_datas['field_format']['input_group_icon']) . '"></i></span>';
                    }
                }

                // Escape label, class name, and field ID
                $label = isset($step_datas['label']) ? esc_html($step_datas['label']) : '';
                $class_name = isset($step_datas['class_name']) ? esc_attr($step_datas['class_name']) : '';
                $field_id = isset($step_datas['field_id']) ? esc_attr($step_datas['field_id']) : '';

                // Check if field is required
                $required = isset($step_datas['validations']['required']) ? $step_datas['validations']['required'] : '';
                $require = !empty($required) ? 'require' : '';
                $r_text = !empty($required) ? '<span class="require-text">*</span>' : '';
				
				switch ($type) {
                    case "text_box":
                      echo '<div class="' . esc_attr($class_name) . ' form-element"> <label>' . esc_html($label) . wp_kses_post($r_text). '</label> <div class="input-group mb-3 ">' . wp_kses_post($format_text_start ). '<input id="' . esc_attr($field_id) . '" type="text" data-label="' . esc_attr($label) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' form-control"  minlength="' . esc_attr($step_datas['validations']['minlength']) . '" maxlength="' . esc_attr($step_datas['validations']['maxlength']) . '">' . wp_kses_post($format_text_end) . '</div></div>';
                        break;
                    case "short_text_area":
                      echo '<div class="' . esc_attr($class_name) . ' form-element"><label>' . esc_html($label) . wp_kses_post($r_text) . '</label><div class="input-group mb-3 "><textarea id="' . esc_attr($field_id) . '"  class="' . esc_attr($require) . ' ' . esc_attr($class_name) . ' form-control" data-label="' . esc_attr($label) . '" minlength="' . esc_attr($step_datas['validations']['minlength']) . '" maxlength="' . esc_attr($step_datas['validations']['maxlength']) . '" placeholder="' . esc_attr($placeholders) . '"></textarea></div></div>';
                        break;
                    case "text_area":
                      echo '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . wp_kses_post($r_text) . '</label><div class="input-group mb-3 "><textarea id="' . esc_attr($field_id) . '"  class="' . esc_attr($require) . '  form-control" data-label="' . esc_attr($label) . '" minlength="' . esc_attr($step_datas['validations']['minlength']) . '" maxlength="' . esc_attr($step_datas['validations']['maxlength']) . '" placeholder="' . esc_attr($placeholders) . '"></textarea></div></div>';
                        break;
                    case "number":
                        $decimal = $step_datas['validations']['decimal'];
                        $decimalPlace = $step_datas['validations']['decimal_places'];
                        $step = 1;
                        if ($decimal) {
                            if ($decimalPlace > 0) {
                                $step = '0.' . str_repeat('0', $decimalPlace - 1) . '1';
                            }
                        }
                        if ($decimalPlace > 0) {
                          echo '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) .wp_kses_post($r_text) . '</label><div class="input-group mb-3 ">' .wp_kses_post( $format_text_start) . '<input type="text" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '"  placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' zippy-decimal form-control" min="' . esc_attr($step_datas['validations']['min']) . '" max="' . esc_attr($step_datas['validations']['max']) . '" step="' . esc_attr($step) . '" data-decimal="' . esc_attr($decimalPlace) . '" >' . wp_kses_post($format_text_end) . '</div></div>';
                        } else {
                          echo '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . wp_kses_post($r_text) . '</label><div class="input-group mb-3 ">' .wp_kses_post( $format_text_start) . '<input type="number" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" oninput="formatNumber(this, ' . esc_attr($decimalPlace) . ')" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' zippy-number-field form-control" min="' . esc_attr($step_datas['validations']['min']) . '" max="' . esc_attr($step_datas['validations']['max']) . '" step="' . esc_attr($step) . '" data-decimal="' . esc_attr($decimalPlace) . '">' . wp_kses_post($format_text_end ). '</div></div>';
                        }
                        break;
                    case "email":
                      echo '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . wp_kses_post($r_text) . '</label><div class="input-group mb-3 ">' .wp_kses_post( $format_text_start ). '<input required type="email" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' form-control">' . wp_kses_post($format_text_end ). '</div></div>';
                        break;
                    case "phone_number":
                      echo'<div class="' . esc_attr($class_name) . ' form-element"> <label>' . esc_html($label) . wp_kses_post($r_text) . '</label> <div class="input-group mb-3 ">' . wp_kses_post($format_text_start) . '<input id="' . esc_attr($field_id) . '" type="tel" data-label="' . esc_attr($label) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' form-control zippy-phone-number"  minlength="' . esc_attr($step_datas['validations']['minlength']) . '" maxlength="' . esc_attr($step_datas['validations']['maxlength']) . '" pattern="[0-9]+">' .wp_kses_post( $format_text_end) . '</div></div>';
                        break;
                    case "website_url":
                      echo '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) . wp_kses_post($r_text) . '</label><div class="input-group mb-3 ">' . wp_kses_post($format_text_start) . '<input type="url" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' form-control">' . wp_kses_post($format_text_end) . '</div></div>';
                        break;
                    case "dropdown":
                        $option = $step_datas['options'];
                        if ($step_datas['validations']['max_selection'] == 1) {
                          echo '<div class="' . esc_attr($class_name) . ' "><label>' . esc_html($label) . wp_kses_post($r_text) . '</label><select data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" class="form-control single-select" name="single-select">';
                            foreach ($option as $options) {
                              echo '<option value="' . esc_attr($options['value']) . '">' . esc_html($options['label']) . '</option>';
                            }
                    
                            echo '</select></div>';
                        } else {
                            $t = $field_id;
                            echo '<div class="' . esc_attr($class_name) . ' "><label>' . esc_html($label) . wp_kses_post($r_text) . '</label><select  data-label="' . esc_attr($label) . '" placeholder="' . esc_attr($placeholders) . '" id="' . esc_attr($field_id) . '" name="test[]"  class="choices-multiple-remove-button ' . esc_attr($require) . '" multiple  data-max-selected-options="' . esc_attr($step_datas['validations']['max_selection']) . '" ><option value="" >' . esc_html($placeholders) . '</option>';
                            foreach ($option as $options) {
                              echo '<option value="' . esc_attr($options['value']) . '">' . esc_html($options['label']) . '</option>';
                            }
                    
                            echo '</select></div>';
                        }
                        break;
                    case "multiselect_checkbox":
                        $option = $step_datas['options'];
                        $t = $field_id;
                        $maxSelection = isset($step_datas['validations']['max_selection']) ? ' data-max-selected-options="' . esc_attr($step_datas['validations']['max_selection']) . '"' : '';
                        echo '<div class="' . esc_attr($class_name) . '  zippyCheckBoxes-option"><label>' . esc_html($label) . wp_kses_post($r_text). '</label><div class="zippyCheckBoxes" ' . wp_kses_post($maxSelection) . ' data-label="' . esc_attr($label) . '">';
                        foreach ($option as $key => $options) {
                            if ($key == 0) {
                                $re = $require;
                            } else {
                                $re = $require;
                            }
                            echo '<input data-label="' . esc_attr($label) . '" data-id="' . esc_attr($field_id) . '" type="checkbox" id="' . esc_attr($zfCode) . '-' . esc_attr($options['label']) . '" name="' . esc_attr($field_id) . '" value="' . esc_attr($options['value']) . '" class="' . esc_attr($re) . '"><label for="' . esc_attr($zfCode) . '-' . esc_attr($options['label']) . '">' . esc_html($options['label']) . '</label>';
                        }
                    
                        echo '</div></div>';
                        break;
                    case "radio":
                        $option = $step_datas['options'];
                        echo '<div class="' . esc_attr($class_name) . ' radio-options"><label>' . esc_html($label) .wp_kses_post($r_text) . '</label><div class="radioButtons">';
                        foreach ($option as $key => $options) {
                            if (!empty($required)) {
                                if ($key == 0) {
                                    $re = 'require';
                                } else {
                                    $re = '';
                                }
                            } else {
                                $re = '';
                            }
                            echo '<input data-label="' . esc_attr($label) . '" data-id="' . esc_attr($field_id) . '" type="radio" id="' . esc_attr($zfCode) . '-' . esc_attr($options['label']) . '" name="' . esc_attr($field_id) . '" value="' . esc_attr($options['value']) . '" class="' . esc_attr($re) . '"><label for="' . esc_attr($zfCode) . '-' . esc_attr($options['label']) . '">' . esc_html($options['label']) . '</label>';
                        }
                    
                        echo '</div></div>';
                    
                        break;
                    case "date":
                        $date_format = $step_datas['validations']['date_format'];
                        $placeholder = '';
                        if ($date_format == 'm-d-Y') {
                            $placeholder = 'MM-DD-YYYY';
                        } elseif ($date_format == 'd-m-Y') {
                            $placeholder = 'DD-MM-YYYY';
                        }
                        echo '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) .wp_kses_post($r_text) . '</label><div class="input-group mb-3 "><input type="text" data-format="' . esc_attr($step_datas['validations']['date_format']) . '"   data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholder) . '" class="' . esc_attr($require) . ' zippy-date-input form-control"  ></div></div>';
                        break;
                    case "time":
                        $timeFormat = $step_datas['validations']['time_format'];
                        if ($timeFormat == 12) {
                          echo '<div class="' . esc_attr($class_name) . ' "><label>' . esc_html($label) . wp_kses_post($r_text) . '</label><div class="input-group mb-3 " ><input type="text" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' time_12format form-control" data-label="' . esc_attr($label) . '"></div></div>';
                        } else {
                          echo '<div class="' . esc_attr($class_name) . ' "><label>' . esc_html($label) . wp_kses_post($r_text). '</label><div class="input-group mb-3 " ><input type="text" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' time_24format form-control" data-label="' . esc_attr($label) . '"></div></div>';
                        }
                        break;
                    case "file":
                        $filetype = $step_datas['validations']['file_extensions_allowed'];
                        $f_type = implode(",.", $filetype);
                    
                        echo '<div class="' . esc_attr($class_name) . '"><label>' . esc_html($label) .wp_kses_post($r_text) . '</label><div class="input-group mb-3 zippy-file-upload"><div class="file-input"><span class="fake-btn"><img src="' . esc_url(plugin_dir_url(__FILE__)) . '../img/upload.png"></span><span class="file-msg">' . esc_html($placeholders) . '</span></div><input type="file" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($require) . ' zippy-file-input" data-extention=".' . esc_attr($f_type) . '" accept=".' . esc_attr($f_type) . '" data-max="' . esc_attr($step_datas['validations']['file_max_size_mb']) . '"></div></div>';
                        break;
                    case "hidden":
                      echo '<input type="hidden" data-label="' . esc_attr($label) . '" id="' . esc_attr($field_id) . '" placeholder="' . esc_attr($placeholders) . '" class="' . esc_attr($class_name) . ' " value="">';
                        break;
                    case "heading":
                    
                      echo '<div class="' . esc_attr($class_name) . '"><' . esc_attr($step_datas['content_size']) . ' style="text-align:' . esc_attr($step_datas['content_alignment']) . ' !important">' . esc_html($step_datas['content']) . '</' . esc_attr($step_datas['content_size']) . '></div>';
                        break;
                    case "paragraph":
                        $tag = $step_datas['content_size'];
                        echo '<div class="' . esc_attr($class_name) . '"><p style="text-align:' . esc_attr($step_datas['content_alignment']) . ' !important;font-size:' . esc_attr($step_datas['content_size']) . 'px !important">' . esc_html($step_datas['content']) . '</p></div>';
                        break;
                    default:
                    echo "";
				  }
				 
			}
			echo '<div class="mt-4" style="overflow:auto;">
    <div style="justify-content: space-between;">';
if ($fcount == 1) {
  echo '<div style="float:right"><button type="button" disabled id="nextBtn" onclick="submitForm(' . esc_attr($c) . ')">' . esc_html($ZippySubmit) . '</button></div>';
} else {
    if ($c == 1) {
        $step_c = $c + 1;
        echo '<div style="float:right"><button type="button" id="nextBtn" onclick="nextStep(' . esc_attr($step_c) . ')">Next</button></div>';
    } elseif ($c == $fcount) {
        $p_step = $c - 1;
        echo '<div style="display:flex;justify-content: space-between;"><button type="button" id="prevBtn" onclick="previousStep(' . esc_attr($p_step) . ')">Previous</button>
            <button type="button" id="nextBtn" disabled onclick="submitForm(' . esc_attr($c) . ')">' . esc_html($ZippySubmit) . '</button></div>';
    } else {
        $step_c = $c + 1;
        $p_step = $c - 1;
        echo '<div style="display:flex;justify-content: space-between;"><button type="button" id="prevBtn" onclick="previousStep(' . esc_attr($p_step) . ')">Previous</button>
            <button type="button" id="nextBtn" onclick="nextStep(' . esc_attr($step_c) . ')">Next</button></div>';

    }
}
echo '</div>
</div>';

}
echo '</div></div>';
}

echo '</form></div></div></div>';
} else if ($response_code == 400 || $response_code == 404) {
  echo '<p style="text-align:center">No active fields mapped to the form step</p>';
} else if ($response_code == 500) {
    echo '<p style="text-align:center">Internal Server Error. Please Contact Site Admin</p>';
}

?>
  <a id="nextBtn" href="<?php echo esc_url('admin.php?page='.$this->plugin_name); ?>">Back To Forms<a>
</div>


