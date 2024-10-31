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


    $y = plugins_url('');

?>

<script>
    function sendTojspdf(n){""!=n?fun_a(n):console.log("error")}function fun_a(n){""!=(b="<?php echo wp_kses_post( $y ) . "/"?>")?fun_b(n,b):console.log("error")}function fun_b(n,o){""!=(c="<?php echo wp_kses_post('../uploads/') . "/"?>")?fun_c(n,o,c):console.log("error")}function fun_c(n,o,r){""!=(d="PDFtoIMG/")?jsPDF(n,o,r,d):console.log("error")}

    function jsPDF(e,t,n,a){x=t+n+a+e,document.addEventListener("contextmenu",e=>e.preventDefault());var l=null,u=!0,c=null,g=1,i=document.getElementById("the-canvas"),d=i.getContext("2d");function m(e,t){u=!0,l.getPage(e).then(function(e){var n=e.getViewport(t);i.height=n.height,i.width=n.width,e.render({canvasContext:d,viewport:n}).promise.then(function(){u=!1,null!==c&&(m(c),c=null)})}),document.getElementById("page_num").value=e}function o(e){u?c=e:m(e,g)}document.getElementById("jumpTo").addEventListener("click",function e(t){var n;o(Number(document.getElementById("page_num").value),l.scale)}),document.getElementById("next").addEventListener("click",function e(t){!(Number(document.getElementById("page_num").value)>=l.numPages)&&(""==document.getElementById("page_num").value||(t=Number(document.getElementById("page_num").value)),o(++t,l.scale))}),document.getElementById("prev").addEventListener("click",function e(t){!(1>=Number(document.getElementById("page_num").value))&&(""==document.getElementById("page_num").value||(t=Number(document.getElementById("page_num").value)),o(--t,l.scale))}),document.getElementById("zoomin").addEventListener("click",function e(){!(g>=l.scale)&&m(1,g+=.1)}),document.getElementById("zoomout").addEventListener("click",function e(){!(g>=l.scale)&&o(1,g-=.1)}),document.getElementById("zoomfit").addEventListener("click",function e(){!(g>=l.scale)&&o(1,g=1)}),PDFJS.getDocument(x).then(function(e){var t=(l=e).numPages;document.getElementById("page_count").textContent="/ "+t,jQuery("#page_num").on("change",function(){var e=Number(jQuery(this).val());e>0&&e<=t&&o(e,g)}),m(1,g)})}
</script>