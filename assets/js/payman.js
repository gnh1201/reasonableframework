function payman_get_check_msgs() {
	return {
		"good_name": "상품명을 기재하여야 합니다.",
		"good_mny": "가격을 기재하여야 합니다.",
		"buyr_name": "구매자 이름이 없습니다.\n\n로그인하시거나 회원 정보에서 반드시 등록하여 주세요.",
		"buyr_mail": "구매자 이메일이 없습니다.\n\n로그인하시거나 회원 정보에서 반드시 등록하여 주세요.",
		"buyr_tel1": "구매자 전화번호가 없습니다.\n\n로그인하시거나 회원 정보에서 반드시 등록하여 주세요."
	};
}

function payman_load_widget(data) {
	var is_available = true;

	var req_data = {
		"route": "orderform.widget",
		"redirect_url": window.location.href
	};

	var check_msgs = payman_get_check_msgs();

	var allows_zero = ["good_mny"];

	for(var k in check_msgs) {
		if( !(k in data) || (allows_zero.indexOf(k) < 0 && data[k] == "") ) {
			alert(check_msgs[k]);
			is_available = false;
			break;
		} else {
			req_data[k] = data[k];
		}
	}

	if(is_available == true) {
		$.ajax({
			type: "post",
			dataType: "text",
			url: "/payman/",
			data: req_data,
			success: function(req) {
				$("#area_payman").html(req);
			}
		});
	}
	
	return is_available;
}

function payman_set_data(name, data) {
	$("#payman_" + name).val(data);
}

function payman_get_data(name) {
	return $("#payman_" + name).val();
}

function payman_set_base64(name, data) {
	var req_data = {
		"route": "base64",
		"action": "encode",
		"data": data
	};

	$.ajax({
		type: "post",
		dataType: "json",
		url: "/payman/",
		data: req_data,
		success: function(req) {
			payman_set_data(name, req.result);
		}
	});
}

function payman_submit() {
	var check_msgs = payman_get_check_msgs();
	for(var k in check_msgs) {
		if(payman_get_data(k) == "") {
			alert(check_msgs[k]);
			return false;
		}
	}

	$("#payman_orderform").submit();
	return true;
}
