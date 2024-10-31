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
     * Show PDFtoIMG table in post or page
    /**************************************/
    function PDFtoIMG_render( $atts ) 
    {
        include_once( 'js/function-min.php' );

        foreach( $atts as $key => $value )
        {
            $clear_substring = trim( $value, '=&#8221;' );
        }

        include_once( 'PDFtoIMG_menu.php' );
            ?>
                <div style="width:100%; overflow-x:scroll">
                    <canvas id="the-canvas"></canvas>
                </div>

                <script>
                    (
                        function() 
                        {
                            sendTojspdf('<?php echo wp_kses_post( $clear_substring ) ?>');
                        }
                    )();
                </script>
            <?php
    }

	add_shortcode( PDFtoIMG_PLUGIN_NAME, 'PDFtoIMG_render' );
?>