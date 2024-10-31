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
?>

<div id="PDFtoIMG_main_menu">
    <div class="btn-group">
        <button id="prev" class="btn btn-light">
            <i class="fa fa-arrow-circle-o-left fa-2x"></i>
        </button>
        <button id="next" class="btn btn-light">
            <i class="fa fa-arrow-circle-o-right fa-2x" aria-hidden="true"></i>
        </button>
    </div>

    <div class="btn-group">
        <button id="zoomin" class="btn btn-light">
            <i class="fa fa-search-plus fa-2x" aria-hidden="true"></i>
        </button>
        <button id="zoomout" class="btn btn-light">
            <i class="fa fa-search-minus fa-2x" aria-hidden="true"></i>
        </button>
        <button id="zoomfit" class="btn btn-light">
            <div style="display: flex; align-items: center;">
                <span class="icon">
                    <i class="fa fa-arrows-alt fa-2x" aria-hidden="true"></i> &nbsp;&nbsp;&nbsp;
                </span>
                <span class="text"> 100%</span>
            </div>
        </button>
    </div>

    <div class="btn-group">
        <div class="input-group ms-1">
            <div style="display: flex; align-items: center;">
                <span class="icon">
                    <input class="form-control" id="page_num" oninput="this.value = this.value.replace(/[^0-9]/g, '').replace(/(\..*)\./g, '$1');" type="text" maxlength="5" size="5">
                </span>
                <span class="text">
                    <div class="input-group-append">
                        <div class="btn btn-white" id="page_count">/</div>
                    </div>
                </span>
            </div>
        </div>
    </div>

    <div class="btn-group">
        <button id="jumpTo" class="btn btn-light ps-3 pe-4">
            <i class="fa fa-search fa-2x" aria-hidden="true"></i>
        </button>
    </div>

</div>