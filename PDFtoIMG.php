<?php
    /*
        Plugin Name: PDFtoIMG Viewer
        Description: PDFtoIMG Viewer is a WordPress plugin that allows you to display a Base64-encoded image of a PDF file on any page or post in your WordPress site. With this plugin, you can easily embed a PDF file in your content without requiring users to download and open a separate PDF viewer, in this way it is possible to show a PDF file without the risk of direct downloading it from users.
        Version: 1.0.1
        Author: Vincenzo Tomai Pitinca
        Author URI: https://www.pitinca.it
        License: GPL2
    */

    /***************************************
     * If this file is called directly, exit
    /**************************************/
    if ( ! defined( 'ABSPATH' ) ) 
    {
        exit;
    }
    /***************************************
    /**************************************/


    /***************************************
     * Set constants
    /**************************************/
    defined( 'PDFtoIMG_PLUGIN_NAME' ) or define( 'PDFtoIMG_PLUGIN_NAME', 'PDFtoIMG' );
    defined( 'PDFtoIMG_PLUGIN_VERSION' ) or define( 'PDFtoIMG_PLUGIN_VERSION', '1.0.1' );
    defined( 'PDFtoIMG_BASE_NAME' ) or define( 'PDFtoIMG_BASE_NAME', plugin_basename( __FILE__ ) );
    defined( 'PDFtoIMG_ROOT_PATH' ) or define( 'PDFtoIMG_ROOT_PATH', plugin_dir_path( __FILE__ ) );
    defined( 'PDFtoIMG_ROOT_URL' ) or define( 'PDFtoIMG_ROOT_URL', plugin_dir_url( __FILE__ ) );

    $upload_dir = wp_upload_dir();
    $upload_path = $upload_dir['basedir'] . '/' . PDFtoIMG_PLUGIN_NAME . '/';
    $upload_url = $upload_dir['baseurl'] . '/' . PDFtoIMG_PLUGIN_NAME . '/';

    defined( 'PDFtoIMG_UPLOAD_PATH' ) or define( 'PDFtoIMG_UPLOAD_PATH', $upload_dir['basedir'] . '/' . PDFtoIMG_PLUGIN_NAME . '/' );
    defined( 'PDFtoIMG_UPLOAD_URL' ) or define( 'PDFtoIMG_UPLOAD_URL', $upload_dir['baseurl'] . '/' . PDFtoIMG_PLUGIN_NAME . '/' );

    /***************************************
     * Include component & styles in frontend
    /**************************************/
    function PDFtoIMG_load_front_resources() 
    {
        // load scripts if is frontend
        if ( 'wp_enqueue_scripts' === current_filter() ) 
        {
            wp_enqueue_style( 'front_style', plugins_url( '/css/PDFtoIMG_front_style.css', __FILE__ ) );

            wp_register_script( 'pdfjs-function', plugins_url( '/lib/pdfjs/pdf.js', __FILE__ ) );
            wp_enqueue_script( 'pdfjs-function' );

            wp_enqueue_style( 'font-awesome', plugins_url( '/lib/fontawesome/font-awesome.min.css', __FILE__ ) );

            wp_enqueue_style( 'bootstrap', plugins_url( '/lib/bootstrap/bootstrap.min.css', __FILE__ ) );
            wp_register_script( 'bootstrap', plugins_url( '/lib/bootstrap/bootstrap.min.js', __FILE__ ) );
            wp_enqueue_script( 'bootstrap' );
        }
    }

    add_action( 'wp_enqueue_scripts', 'PDFtoIMG_load_front_resources' );

    /***************************************
     * Include component & styles (only if i'm in plugin pages)
    /**************************************/
    if (
        $pagenow === 'admin.php' && 
        isset( $_GET['page'] ) && 
        (
            $_GET['page'] === PDFtoIMG_PLUGIN_NAME || 
            $_GET['page'] === 'PDFtoIMG_file_upload'
        )
    ) {
        function PDFtoIMG_load_back_resources_noconflict() 
        {
            wp_enqueue_style( 'admin_style', plugins_url( '/css/PDFtoIMG_admin_style.css', __FILE__ ) );
            wp_enqueue_style( 'font-awesome', plugins_url( '/lib/fontawesome/font-awesome.min.css', __FILE__ ) );

            include_once( 'js/function-min.php' );

            wp_register_script('functionjs', plugins_url('/js/function.js', __FILE__));
            wp_enqueue_script('functionjs');

            wp_enqueue_style( 'bootstrap', plugins_url( '/lib/bootstrap/bootstrap.min.css', __FILE__ ) );
            wp_register_script( 'bootstrap', plugins_url( '/lib/bootstrap/bootstrap.min.js', __FILE__ ) );
            wp_enqueue_script( 'bootstrap' );
        }

        add_action( 'admin_enqueue_scripts', 'PDFtoIMG_load_back_resources_noconflict' );
    }
    /***************************************
    /**************************************/


    /***************************************
     * Register plugin
    /**************************************/
    register_activation_hook( __FILE__, array( PDFtoIMG_PLUGIN_NAME, 'PDFtoIMG_activation' ) );
    /***************************************
    /**************************************/


    /***************************************
     * INSTALL PLUGIN
    /**************************************/
    class PDFtoIMG 
    {
        function __construct() 
        {
            load_plugin_textdomain(PDFtoIMG_PLUGIN_NAME, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
            add_filter( 'PDFtoIMG_plugin_row_meta', array( $this, 'PDFtoIMG_plugin_row_meta' ), 10, 2 );

            function PDFtoIMG_settings_link( $links ) 
            {
                $PDFtoIMG_settings_link = '<a href="' . admin_url( 'admin.php?page=' . PDFtoIMG_PLUGIN_NAME ) . '">Settings</a>';
                array_unshift( $links, $PDFtoIMG_settings_link );
                return $links;
            }

            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'PDFtoIMG_settings_link' );
        }

        // Activation hook
        public static function PDFtoIMG_activation() 
        {
            // PHP version compatibility check call
            if ( ! PDFtoIMG::PDFtoIMG_php_version_check() ) 
            {
                // Deactivate the plugin
                deactivate_plugins( __FILE__ );
            } else {
                // Check and create uploads folder
                if ( ! is_dir( PDFtoIMG_UPLOAD_PATH ) ) 
                {
                    wp_mkdir_p( PDFtoIMG_UPLOAD_PATH );
                }
            }
        } // end method activation

        // PHP version compatibility check
        public static function PDFtoIMG_php_version_check()
        {
            if ( version_compare( PHP_VERSION, '7.4.0', '<' ) ) 
            {
                return false;
            }

            return true;
            
        } // end method php_version_check

        // Plugin support and doc page url
        public function PDFtoIMG_plugin_row_meta( $links, $file ) 
        {
            if ( strpos( $file, PDFtoIMG_PLUGIN_NAME . '.php' ) !== false ) 
            {
                $info_links = array(
                    // 'support' => '<a href="" target="_blank">'.esc_html__('Support', 'PDFtoIMG').'</a>',
                    // 'doc' => '<a href="#" target="_blank">'.esc_html__('Documentation', 'PDFtoIMG').'</a>'
                );

                $links = array_merge( $links, $info_links );
            }

            return $links;
        }

    } // end Class PDFtoIMG
    /***************************************
    /**************************************/

    /***************************************
     * Load plugin
    /**************************************/
    function PDFtoIMG_load_plugin() 
    {
        new PDFtoIMG();
        include_once( 'PDFtoIMG_frontend.php' );
        include_once( 'PDFtoIMG_administration.php' );
    }

    add_action( 'plugins_loaded', 'PDFtoIMG_load_plugin', 5 );
    /***************************************
    /**************************************/
?>