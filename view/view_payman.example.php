<script src="<?php echo base_url(); ?>/js/payman.js"></script>
<script type="text/javascript">
// load payman widget
$(document).ready(function() {
	var is_payman_loaded = payman_load_widget({
		"pay_method_alias": "CRE",
		"good_name": "채용정보등록",
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
});
