/* 표준웹 실행 */
function jsf__pay(form) {
	try {
		KCP_Pay_Execute( form ); 
	} catch (e) { 
		/* IE 에서 결제 정상종료시 throw로 스크립트 종료 */ 
	}
}             

/* 주문번호 생성 예제 */
function init_orderid() {
	var today = new Date();
	var year  = today.getFullYear();
	var month = today.getMonth() + 1;
	var date  = today.getDate();
	var time  = today.getTime();

	if(parseInt(month) < 10) {
		month = "0" + month;
	}

	if(parseInt(date) < 10) {
		date = "0" + date;
	}

	var order_idxx = "ORDER" + year + "" + month + "" + date + "" + time;

	document.getElementById("ordr_idxx").value = order_idxx;
}

// 주문 양식 제출
function submit_orderform(form) {
	jsf__pay(form);
	return false;
}

// 페이지 접속 시 실행
window.onload = function() {
	init_orderid();

	// 폼 이벤트 설정
	var form_order_info = document.getElementById("order_info");
	form_order_info.setAttribute("onsubmit", "return submit_orderform(this)");

	// 무료 서비스 확인
	var form_good_mny = document.getElementById("good_mny");
	if(form_good_mny.value == "") {
		if(confirm("[가격정보 없음]\n무료 서비스를 신청하는 것이 맞습니까?\n무료 서비스 이용이 아니라면 취소를 눌러주세요.")) {
			document.getElementById("res_cd").value = "9999";
			document.getElementById("route").value = "ordercomplete.pgkcp";
			form_order_info.submit();
		} else {
			window.location.href = document.getElementById("redirect_url").value;
		}
	} else {
		// 결제 모듈 실행
		setTimeout(function() {
			jsf__pay(form_order_info);
		}, 3000);
	}
}
