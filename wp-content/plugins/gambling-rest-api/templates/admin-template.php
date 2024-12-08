<?php if (!defined('ABSPATH'))
    exit; ?>
<div class="wrap" id="ocambaPluginWrap">
    <h1>
        Gambling REST API
    </h1>

    <?php
    if ($seed_finished) {
        include plugin_dir_path(__FILE__) . '/admin-template-show-rest.php';
    } else {
        include plugin_dir_path(__FILE__) . '/admin-template-seed.php';
    }
    ?>


    <dialog id="loadingDialog">

        <div class="ripple">
            <div></div>
            <div></div>
        </div>

    </dialog>

</div>