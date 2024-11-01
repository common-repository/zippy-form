<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.zaigoinfotech.com
 * @since      1.0.0
 *
 * @package    Zippy_Form
 * @subpackage Zippy_Form/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Zippy_Form
 * @subpackage Zippy_Form/admin
 * @author     Zaigo infotech <sales@zaigoinfotech.com>
 */
class Zippy_Form_Admin
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $version    The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string    $plugin_name       The name of this plugin.
     * @param      string    $version    The version of this plugin.
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        add_action("admin_menu", [$this, "addPluginAdminMenu"], 9);
        add_action("admin_init", [$this, "registerAndBuildFields"]);
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Zippy_Form_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Zippy_Form_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . "css/zippy-form-admin.css",
            [],
            $this->version,
            "all"
        );
        $screen = get_current_screen();
        if ( 'dashboard_page_zippy-form-preview' === $screen->base && $_GET['page'] ==='zippy-form-preview' )
 {
    wp_enqueue_style('bootstrap', plugin_dir_url( __DIR__ ) . 'public/css/bootstrap.min.css',array(),$this->version);
    wp_enqueue_style('fa-icon', plugin_dir_url( __DIR__ ) . 'public/css/all.min.css',array(),$this->version);
	wp_enqueue_style('choices-css', plugin_dir_url( __DIR__ ) . 'public/css/choices.min.css',array(),$this->version );
    wp_enqueue_style('flatpickr-css', plugin_dir_url( __DIR__ ) . 'public/css/flatpickr.min.css',array(),$this->version);
	
	} 
       
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Zippy_Form_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Zippy_Form_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script(
            $this->plugin_name,
            plugin_dir_url(__FILE__) . "js/zippy-form-admin.js",
            ["jquery"],
            $this->version,
            false
        );
        $screen = get_current_screen();
        if ( 'dashboard_page_zippy-form-preview' === $screen->base && $_GET['page'] ==='zippy-form-preview' )
 {
    wp_enqueue_script("zippy-multiselect-js", plugin_dir_url( __DIR__ ) . 'public/js/zippy-form-multiselect-public.js', array('jquery'), $this->version, true);
    wp_enqueue_script('choices', plugin_dir_url( __DIR__ ) . 'public/js/choices.min.js',array('jquery'), $this->version, true);
    wp_enqueue_script('bootstrap-js', plugin_dir_url( __DIR__ ) . 'public/js/bootstrap.min.js',array('jquery'), $this->version, true);
	wp_enqueue_script('flatpickr-js', plugin_dir_url( __DIR__ ) . 'public/js/flatpickr.min.js',array('jquery'), $this->version, true);
	
	}
}

    public function addPluginAdminMenu()
    {
        //add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
        add_menu_page(
            $this->plugin_name,
            "Zippy Form",
            "manage_options",
            $this->plugin_name,
            [$this, "displayPluginAdminDashboard"],
            "dashicons-chart-area",
            26
        );

        //add_submenu_page( '$parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
        add_submenu_page(
            $this->plugin_name,
            "Zippy Form Settings",
            "Settings",
            "manage_options",
            $this->plugin_name . "-settings",
            [$this, "displayPluginAdminSettings"]
        );
        add_submenu_page(
            null,  // Parent menu slug (Settings)
            'Zippy Form Preview',      // Page title
            'Zippy Form Preview',      // Menu title
            'manage_options',       // Capability required to access
            $this->plugin_name . "-preview", // Sub-menu slug
            [$this, "previewListFormItem"] // Callback function to display content
        );
      
    }
    
    public function displayPluginAdminDashboard()
{
    //require_once 'partials/'.$this->plugin_name.'-admin-display.php';

    $myListTable = new Zippy_List_Table();
    $myListTable->prepare_items();

    echo '<div class="wrap">';
    echo "<h2>Zippy Forms</h2>";
    $formApiUrl = esc_url(get_option("zippy_form_base_url")); 
    $license = esc_attr(get_option("zippy_form_license_key")); 
    $args = [
        "headers" => [
            "Content-Type" => "application/json",
            "ZF-SECRET-KEY" => $license,
        ],
    ];
    $formUrl = wp_remote_get("$formApiUrl/dynamic-form/list?type=standard_form", $args);

    if (is_wp_error($formUrl)) {
        echo "<h3><strong>Please update License Key on settings page</strong></h3>";
    } else {
        $response_code = wp_remote_retrieve_response_code($formUrl);
        $formData = wp_remote_retrieve_body($formUrl);
        $datas = json_decode($formData, true);

        if ($response_code == 403 || $response_code == 400) {
            echo "<h3><strong>" . esc_html($datas["status"]) ." - " . esc_html($datas["detail"]) . "</strong></h3>";
        } elseif ($response_code == 500) {
            echo "<h3>Something went wrong</h3>";
        } else {
            // Display the List Table
            echo '<form id="posts-filter" method="post">';
            $myListTable->display();
            echo "</form>";
        }
    }
    echo "</div>";
}


    public function displayPluginAdminSettings()
    {
        $valid_tabs = array( 'general', 'another_tab', 'yet_another_tab' );
        $active_tab = isset( $_GET["tab"] ) && in_array( $_GET["tab"], $valid_tabs, true ) ? sanitize_key( $_GET["tab"] ) : 'general';
        $error_message = isset( $_GET["error_message"] ) ? wp_unslash( sanitize_text_field( $_GET["error_message"] ) ) : '';
        if (! empty( $error_message )) {
            add_action("admin_notices", [$this, "pluginNameSettingsMessages"]);
            add_action("admin_notices", function() use ($error_message) {
                echo '<div class="notice notice-error"><p>' . esc_html( $error_message ) . '</p></div>';
            });
        }
        require_once "partials/{$this->plugin_name}-admin-settings-display.php";
    }

    public function pluginNameSettingsMessages($error_message)
    {
        switch ($error_message) {
            case "1":
                $message = __(
                    "There was an error adding this setting. Please try again.  If this persists, shoot us an email.",
                    "my-text-domain"
                );
                $err_code = esc_attr("zippy_form_base_url");
                $setting_field = "zippy_form_base_url";
                break;
            case "2":
                $message = __(
                    "There was an error adding this setting. Please try again.  If this persists, shoot us an email.",
                    "my-text-domain"
                );
                $err_code = esc_attr("zippy_form_license_key");
                $setting_field = "zippy_form_license_key";
                break;
        }
        $type = "error";
        add_settings_error($setting_field, $err_code, $message, $type);
    }
    public function registerAndBuildFields()
    {
        /**
         * First, we add_settings_section. This is necessary since all future settings must belong to one.
         * Second, add_settings_field
         * Third, register_setting
         */
        add_settings_section(
            // ID used to identify this section and with which to register options
            "dynamic_form_general_section",
            // Title to be displayed on the administration page
            "",
            // Callback used to render the description of the section
            [$this, "dynamic_form_display_general_account"],
            // Page on which to add this section of options
            "dynamic_form_general_settings"
        );

        $args = [
            "type" => "input",
            "subtype" => "text",
            "id" => "zippy_form_base_url",
            "name" => "zippy_form_base_url",
            "required" => "true",
            "get_options_list" => "",
            "value_type" => "normal",
            "wp_data" => "option",
        ];
        $argss = [
            "type" => "input",
            "subtype" => "text",
            "id" => "zippy_form_license_key",
            "name" => "zippy_form_license_key",
            "required" => "true",
            "get_options_list" => "",
            "value_type" => "normal",
            "wp_data" => "option",
        ];
        add_settings_field(
            "zippy_form_base_url",
            "Form Builder Base Url",
            [$this, "dynamic_form_render_settings_field"],
            "dynamic_form_general_settings",
            "dynamic_form_general_section",
            $args
        );
        add_settings_field(
            "zippy_form_license_key",
            "License Key",
            [$this, "dynamic_form_render_settings_license_field"],
            "dynamic_form_general_settings",
            "dynamic_form_general_section",
            $argss
        );

        register_setting(
            "dynamic_form_general_settings",
            "zippy_form_base_url"
        );
        register_setting(
            "dynamic_form_general_settings",
            "zippy_form_license_key"
        );
    }
    public function dynamic_form_display_general_account()
    {
        echo "<p></p>";
    }
    public function dynamic_form_render_settings_field($args)
    {
        $wp_data_value = get_option($args["name"]);

        echo '<input type="' . esc_attr( $args["subtype"] ) . '" id="' . esc_attr( $args["id"] ) . '" "' . esc_attr( $args["required"] ) . '" name="' . esc_attr( $args["name"] ) . '" size="40" value="' . esc_attr( get_option( $args["name"] ) ) . '"  />';

    }

    public function dynamic_form_render_settings_license_field($argss)
    {
        echo '<input type="' . esc_attr( $argss["subtype"] ) . '" id="' . esc_attr( $argss["id"] ) . '" "' . esc_attr( $argss["required"] ) . '" name="' . esc_attr( $argss["name"] ) . '" size="40" value="' . esc_attr( get_option( $argss["name"] ) ) . '"  />';

    }
    public function previewListFormItem($item_id)
{
     if (isset($_GET['action']) && $_GET['page'] == "zippy-form-preview" && $_GET['action'] == "preview") {
        $formID = isset($_GET["id"]) ? sanitize_text_field($_GET["id"]) : '';
        require_once "partials/" .
            $this->plugin_name .
            "-admin-display.php";
    }
}
}

if (!class_exists("WP_List_Table")) {
    require_once ABSPATH . "wp-admin/includes/class-wp-list-table.php";
}
class Zippy_List_Table extends WP_List_Table
{
    public function __construct()
    {
        parent::__construct([
            "singular" => "form",
            "plural" => "forms",
            "ajax" => false,
        ]);
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $data = $this->get_data();

        // Set up pagination
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = count($data);

        // Adjust current page if out of range
        $current_page = max(1, min($current_page, ceil($total_items / $per_page)));

        // Slice data for the current page
        $offset = ($current_page - 1) * $per_page;
        $this->items = array_slice($data, $offset, $per_page);

        // Set pagination args
        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page),
        ]);

        // Set column headers
        $this->_column_headers = [$columns, [], $this->get_sortable_columns()];
    }

    public function get_columns()
    {
        // Define your table columns
        return [
            "name" => "Form Name",
            "shortcode" => "Shortcode",
            "date" => "Date",
            // Add more columns as needed
        ];
    }

    public function get_data()
    {
        // Fetch all data from the API
        $formApiUrl = esc_url(get_option("zippy_form_base_url"));
        $license = esc_attr(get_option("zippy_form_license_key"));
        $args = [
            "headers" => [
                "Content-Type" => "application/json",
                "ZF-SECRET-KEY" => $license,
            ],
        ];

        $allData = []; 
        $page = 1; 
        $perPage = 10; 

        while (true) {
            $formUrl = wp_remote_get("$formApiUrl/dynamic-form/list?page=$page&page_size=$perPage", $args);

            if (is_wp_error($formUrl)) {
                break;
            }

            $body = wp_remote_retrieve_body($formUrl);
            $data = json_decode($body, true);

            if (empty($data) || !isset($data['data']['list']['data'])) {
                break;
            }

            $formShortcodes = $data["data"]["list"]["data"];

            foreach ($formShortcodes as $formShortcode) {
                if ($formShortcode["status"] == "active") {
                    $apidata = [];
                    $apidata["name"] = esc_html($formShortcode["name"]); // Escape the name
                    $apidata["shortcode"] = '<input type="text" class="large-text code" onfocus="this.select();" readonly="readonly" value="' . esc_attr('[zippy_form id=' . $formShortcode['id'] . ']') . '" >'; // Escape the shortcode value
                    $apidata["date"] = esc_html($formShortcode["created_date"]); // Escape the date
                    $apidata["id"] = esc_attr($formShortcode['id']); // Escape the ID
                    $allData[] = $apidata;
                }
            }

            if ($page >= $data['data']['list']['total_pages']) {
                break;
            }

            $page++; 
        }

        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        if (!empty($search)) {
            $allData = array_filter($allData, function ($item) use ($search) {
                return stripos($item["name"], $search) !== false;
            });
        }

        return $allData;
    }

    public function usort_reorder($a, $b)
    {
    }

    public function column_default($item, $column_name)
    {

        return $item[$column_name];
    }
    public function get_pagenum() {
        $pagenum = isset( $_REQUEST['paged'] ) ? absint( $_REQUEST['paged'] ) : 1;
        return $pagenum;
    }
    public function column_name($item) {
        $output = '';
    
        $output .= '<strong><a class="row-title">' . esc_html($item['name']) . '</a></strong>';
    
        // Get actions.
        $actions = array(
            'edit' => sprintf(
                '<a href="%s">Preview</a>',
                esc_url(add_query_arg(array('page' => 'zippy-form-preview', 'action' => 'preview', 'id' => $item['id']), admin_url()))
            ),
        );
    
        $row_actions = array();
    
        foreach ($actions as $action => $link) {
            $row_actions[] = '<span class="' . esc_attr($action) . '">' . $link . '</span>';
        }
    
        $output .= '<div class="row-actions">' . implode(' | ', $row_actions) . '</div>';
    
        return $output;
    }
    
    public function display()
    {
        // Output the search input
        $this->search_box("Search", "form");

        // Output the table
        parent::display();
    }


 
}