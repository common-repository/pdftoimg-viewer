<?php
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
     * Define header for all pages
    /**************************************/

    function PDFtoIMG_header( $title_page ) 
    {
        $echo = '
            <h2 class="PDFtoIMG_mainTitle pt-4">
                <span>PDFtoIMG <sub>v.' . PDFtoIMG_PLUGIN_VERSION . '</sub></span>
            </h2>

            <div class="container">
                <h3 class="pageTitle pt-5">
                ' . $title_page . '
                </h3>
            </div>
        ';

        echo wp_kses_post( $echo );
    }
    /***************************************
    /**************************************/


    /***************************************
    * Generate random string
    ***************************************/
    function PDFtoIMG_random_string( $num )
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $random_string = substr( str_shuffle( $characters), 0, $num );
        return esc_html( $random_string );
    }
    /***************************************
    ***************************************/


    /***************************************
    * FILE MANAGEMET 
    * Define administration file management page
    ***************************************/
    function PDFtoIMG_file_management() 
    {
        PDFtoIMG_header( 'FILE MANAGEMENT' );
        PDFtoIMG_pdf_management();
    }
    /***************************************
    ***************************************/


    /***************************************
    * Call Class for FILE MANAGEMENT
    ***************************************/
    function PDFtoIMG_pdf_management() 
    {
        if ( current_user_can( 'administrator' ) ) 
        {
            echo '
                <div class="container">
            ';

                    $manage_table = new PdftoImg_File_Management();
                    $manage_table->prepare_items();
                    $manage_table->display_content();

            echo '
                </div>
            ';
        } else {
            return 'Not enough rights to continue here ...';
        }
    }
    /***************************************
    ***************************************/


    /***************************************
    * Define WP_List_Table Class TABLE SETTING
    ***************************************/
    if ( ! class_exists( 'WP_List_Table' ) )
    {
        require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
    }
    

    /***************************************
    * Define WP_List_Table Class for administration file management
    ***************************************/
    class PdftoImg_File_Management extends WP_List_Table 
    {
        function __construct() 
        {
            parent::__construct( array(
                'singular' => 'row', // Singular name of the row
                'plural' => 'rows', // Plural name of the row
                'ajax' => true // Does this table support ajax?
            ) );
        }

        protected function column_default( $item, $column_name ) 
        {
            switch( $column_name ) 
            { 
                case 'file_name':
                    return $item['file_name'];

                case 'shortcode':
                    return $item['shortcode'];

                case 'actions':
                    return $this-> PDFtoIMG_delete_file( $item['file_name'] );

                default:
                    // return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes :)
            }
        }

        public function get_columns()
        {
            $columns = array(
                'file_name' => 'File name',
                'shortcode' => 'Shortcode',
                'actions' => 'Actions'
            );

            return $columns;
        }

        public function prepare_items() 
        {
            $dir_path = PDFtoIMG_UPLOAD_PATH;
            $files = scandir( $dir_path );
            $data = array();
        
            foreach( $files as $file_name ) 
            {
                if (pathinfo( $file_name, PATHINFO_EXTENSION ) == "pdf" ) 
                {
                    $data[] = array(
                        'file_name' => $file_name,
                        'shortcode' => '[' . PDFtoIMG_PLUGIN_NAME . ' file_name="' . $file_name . '"]',
                        'actions' => ''
                    );
                }
            }

            $this->_column_headers = array( $this->get_columns(), array(), array() );
            $this->items = $data;
        }

        public function PDFtoIMG_delete_file( $file_to_delete )
        {
            $rnd = PDFtoIMG_random_string( 5 );
            $my_nonce = wp_create_nonce( 'contact_form_nonce' );

            ?>
                <div class="row g-0 my-2">
                    <div class="col-12">
                        <form id="delete_file_form">
                            <input type="hidden" name="delete_file_<?php echo esc_html( $rnd ) ?>" id="delete_file_<?php echo esc_html( $rnd ) ?>" value="<?php echo esc_html( $file_to_delete ) ?>">
                            <input type="hidden" name="contact_form_nonce" id="contact_form_nonce" value="<?php echo esc_attr( $my_nonce ); ?>">

                            <a href="<?php echo PDFtoIMG_UPLOAD_URL . esc_html( $file_to_delete ) ?>" id="save_file" class="btn btn-outline-success btn-sm" download title="Download <?php echo esc_html( $file_to_delete ) ?>">
                                <i class="fa fa-download" aria-hidden="true"></i>
                            </a> 

                            <a href="javascript:PDFtoIMG_delete_file('<?php echo esc_html( $rnd ) ?>');" id="delete_file" class="btn btn-outline-danger btn-sm w-auto" title="Delete <?php echo esc_html( $file_to_delete ) ?>">
                                <i class="fa fa-trash" aria-hidden="true"></i>
                            </a>
                        </form>
                    </div>
                </div>
            <?php
        }

        public function display_content() 
        {
            $this->display();
        }
    }
    /**************************************/


    /***************************************
    * Delete file
    ***************************************/
    function PDFtoIMG_delete_file_callback() 
    {
        if ( 
            ! isset( $_POST['contact_form_nonce'] ) || 
            ! wp_verify_nonce( $_POST['contact_form_nonce'], 'contact_form_nonce' ) ) 
        {
            wp_send_json_error( 'Access denied!' );
        } else {
            $filename = sanitize_text_field( $_POST['filename'] );
            $directory = PDFtoIMG_UPLOAD_PATH;
            $files = scandir( $directory );
    
            foreach( $files as $file ) 
            {
                if ( $file == $filename ) 
                {
                    $file_path = $directory . $file;
    
                    if ( strpos( $file_path, $directory ) === 0 ) 
                    {
                        unlink( $file_path );
                        wp_send_json_success( 'File successfully deleted!' );
                    } else {
                        wp_send_json_error( 'Incorrect deletion attempt!' );
                    }
                }
            }
        }

        wp_send_json_error( 'File not found!' );
    }

    add_action( 'wp_ajax_PDFtoIMG_delete_file', 'PDFtoIMG_delete_file_callback' );
    /**************************************/


    /***************************************
    * FILE UPLOAD 
    * Define administration upload page
    ***************************************/
    function PDFtoIMG_file_upload() 
    {
        PDFtoIMG_header( 'PDF UPLOAD' );
        PDFtoIMG_pdf_upload();
    }
    /***************************************
    ***************************************/


    /***************************************
    * File upload 
    ***************************************/
    function PDFtoIMG_upload_dir( $dirs ) 
    {
        $dirs['subdir'] = '/' . PDFtoIMG_PLUGIN_NAME;
        $dirs['path'] = $dirs['basedir'] . '/' . PDFtoIMG_PLUGIN_NAME;
        $dirs['url'] = $dirs['baseurl'] . '/' . PDFtoIMG_PLUGIN_NAME;
    
        return $dirs;
    }

    function PDFtoIMG_pdf_upload() 
    {
        ?>
            <div class="container alert alert-secondary mt-5" role="alert">
                <div class="container mt-2 mb-3">
                    <span class="badge text-bg-warning float-end rounded-1">Only .pdf file are accepted</span>
                </div>

                <form method="post" enctype="multipart/form-data">
                    <div class="btn btn-dark mb-0 rounded-1" id="uploadTrigger">Browse &hellip;</div>
                    <div class="pt-2" id="fileNames"></div>
                    <input type="file" class="hidden" id="uploaded_files" name="uploaded_files[]" multiple="true">

                    <hr>

                    <div class="text-end">
                        <input type="submit" value="Upload Files" name="uploadFile" id="uploadFile" class="btn btn-dark mb-0 rounded-1">
                    </div>
                </form>
            </div>
        <?php

        if ($_SERVER["REQUEST_METHOD"] == "POST") 
        {
            $custom_upload_path = PDFtoIMG_UPLOAD_PATH;

            if (!is_dir($custom_upload_path)) 
            {
                wp_mkdir_p($custom_upload_path);
            }
    
            if (!is_writable($custom_upload_path)) 
            {
                wp_die(esc_html('Folder doesn\'t exist or you don\'t have permission to write in it'));
            }
    
            if (!isset($_FILES['uploaded_files'])) 
            {
                echo 'No file uploaded';
            } else {
                $uploaded_files = $_FILES['uploaded_files'];

                foreach ($uploaded_files['name'] as $key => $filename) 
                {
                    $file_type = sanitize_text_field( $_FILES['uploaded_files']['type'][$key] );
                    $file_name = sanitize_text_field( $uploaded_files['name'][$key] );

                    if ( $file_type === 'application/pdf' ) 
                    {
                        add_filter( 'upload_dir', 'PDFtoIMG_upload_dir' );

                        $file_data = array(
                            'name' => $uploaded_files['name'][$key],
                            'type' => $uploaded_files['type'][$key],
                            'tmp_name' => $uploaded_files['tmp_name'][$key],
                            'error' => $uploaded_files['error'][$key],
                            'size' => $uploaded_files['size'][$key]
                        );
        
                        $upload_overrides = array( 'test_form' => false );
                        $move_result = wp_handle_upload($file_data, $upload_overrides);
        
                        if ($move_result && !isset($move_result['error'])) 
                        {
                            $message = 'Upload complete for the following file: ';
                            $showFileName = esc_html( $file_name );
                            $alertType = 'success';
                        } else {
                            $message = 'Upload failed for the following file: ';
                            $showFileName = esc_html( $file_name );
                            $alertType = 'danger';
                        }
    
                        remove_filter( 'upload_dir', 'PDFtoIMG_upload_dir' );
                    } else {
                        $message = 'Upload rejected for the following file: ';
                        $showFileName = esc_html( $file_name ) . ' is not a valid .pdf file';
                        $alertType = 'danger';
                    }

                    if ( $file_type === '' ) 
                    {
                        $message = 'No file uploaded';
                        $showFileName = 'Please select .pdf file(s) before uploading';
                        $alertType = 'warning';
                    }
                    ?>
                        <div class="container alert alert-<?php echo esc_attr( $alertType ); ?>" role="alert">
                            <h6 class="alert-heading fw-bold"><?php echo esc_html( $message ); ?></h6>
                            <hr>
                            <p class="mb-0"><?php echo esc_html( $showFileName ); ?></p>
                        </div>
                    <?php
                }
            }
        } else {
            return '
                <div class="container alert alert-<?php echo esc_attr( $alertType ) ?>" role="alert">
                    <h6 class="alert-heading fw-bold">Not enough rights to continue here ...</h6>
                </div>
            ';
        }
    }

    /***************************************
    ***************************************/


    /***************************************
    * Add config_menu on sidebar
    ***************************************/
    function PDFtoIMG_config_menu() 
    {
        add_menu_page(
            PDFtoIMG_PLUGIN_NAME . " - File Management", // menu title
            PDFtoIMG_PLUGIN_NAME, // menu text
            'manage_options',  // access page policy
            PDFtoIMG_PLUGIN_NAME, // page slug
            'PDFtoIMG_file_management', // callback that calls the page
            plugins_url('pdftoimg-viewer/img/PDFtoIMG_icon.png') // icon on sidebar
        );

        add_submenu_page(
            PDFtoIMG_PLUGIN_NAME, // main menu slug
            PDFtoIMG_PLUGIN_NAME . ' - File Management', // submenu title
            'File Management', // submenu text
            'manage_options',  // access page policy
            PDFtoIMG_PLUGIN_NAME, // submenu page slug
            'PDFtoIMG_file_management' // callback that calls the page
        );

        add_submenu_page(
            PDFtoIMG_PLUGIN_NAME, // main menu slug
            PDFtoIMG_PLUGIN_NAME . ' - File Upload', // submenu title
            'File Upload', // submenu text
            'manage_options',  // access page policy
            'PDFtoIMG_file_upload', // submenu page slug
            'PDFtoIMG_file_upload' // callback that calls the page
        );
    }

    add_action( 'admin_menu', 'PDFtoIMG_config_menu' );
    /***************************************
    ***************************************/
?>