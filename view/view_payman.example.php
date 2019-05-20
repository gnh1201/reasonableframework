<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title>payman example</title>
    </head>
    <body>
        <!-- payman widget -->
        <div id="area_payman"></div>
        
        <!-- scripts area -->
        <script src="<?php echo base_url(); ?>?route=webproxy&url=https://code.jquery.com/jquery-3.3.1.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/js/payman.js"></script>
        <script type="text/javascript">
        // load payman widget
        $(document).ready(function() {
            var is_payman_loaded = payman_load_widget({
                "pay_method_alias": "CRE",
                "good_name": "name of goods",
                "good_mny": 0,
                "buyr_name": "<?php echo $name; ?>",
                "buyr_mail": "<?php echo $email; ?>",
                "buyr_tel1": "<?php echo $tel; ?>",
                "pay_data": ""
            });

            if(is_payman_loaded == false) {
                alert("payman not loaded correctly.");
                document.location.href = "<?php echo $base_url; ?>";
            }

            payman_set_data("good_mny", 1000);
            payman_set_base64("pay_data", JSON.stringify({
                "color": "pink",
                "size": "2xl"
            }));

            payman_submit();
        });
    </body>
</html>
