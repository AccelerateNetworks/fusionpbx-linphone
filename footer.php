<?php echo "<script src='".PROJECT_PATH."/resources/jquery/jquery-qrcode.min.js'></script>"; ?>
<script type="text/javascript">
function show_qr(url) {
    $('#qr_code').empty().qrcode({
        render: 'canvas',
        minVersion: 6,
        maxVersion: 40,
        ecLevel: 'H',
        size: 650,
        radius: 0.2,
        quiet: 6,
        background: '#fff',
        mode: 4,
        mSize: 0.2,
        mPosX: 0.5,
        mPosY: 0.5,
        text: url,
    });

    $('#qr_code_container').fadeIn(400);
}

function copy(data) {
    navigator.clipboard.writeText(data).then(() => {
        // TODO: positive feedback
    }).catch((e) => {
        // TODO: negative feedback
    });
}
</script>
<style type="text/css">
#qr_code_container {
    z-index: 999999; 
    position: absolute;
    left: 0;
    top: 0;
    right: 0;
    bottom: 0;
    text-align: center;
    vertical-align: middle;
}

#qr_code {
    display: block;
    width: 650px;
    height: 650px;
    -webkit-box-shadow: 0px 1px 20px #888;
    -moz-box-shadow: 0px 1px 20px #888;
    box-shadow: 0px 1px 20px #888;
}
</style>
<?php

require_once "resources/footer.php";
