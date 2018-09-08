<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title>payman example</title>
	</head>
	<body>
		<div id="area_payman"></div> <!-- payman widget -->

		<script src="<?php echo base_url(); ?>/assets/js/payman.js"></script>
		<script type="text/javascript">
		// load payman widget
		$(document).ready(function() {
			var is_payman_loaded = payman_load_widget({
				"pay_method_alias": "CRE",
				"good_name": "상품이름",
				"good_mny": 0,
				"buyr_name": "<?php echo $name; ?>",
				"buyr_mail": "<?php echo $email; ?>",
				"buyr_tel1": "<?php echo $tel; ?>",
				"pay_data": ""
			});

			if(is_payman_loaded == false) {
				alert("결제를 중단합니다.");
				document.location.href = "<?php echo $base_url; ?>";
			}

			payman_set_data("good_mny", 1000);
			payman_set_base64("pay_data", JSON.stringify({
				"color": "pink",
				"size": "2xl"
			}));
		});
	</body>
</html>
