<p>개발자용 페이지입니다. 실 운용 환경에서는 보이지 않는 부분입니다.</p>
<form id="payman_orderform" name="payman_orderform" method="post" action="<?php echo base_url(); ?>">
    <fieldset>
        <legend>결제 정보 입력</legend>
        <div>
            <input type="hidden" name="_token" value="<?php echo $_token; ?>">
            <input type="hidden" name="route" value="<?php echo $_next_route; ?>">
            <input type="hidden" name="redirect_url" value="<?php echo $redirect_url; ?>">
        </div>

        <div>
            <label for="payman_pay_method_alias">결제방법</label>
            <select id="payman_pay_method_alias" name="pay_method_alias">
                <option value="">선택하세요</option>
                <option value="CRE"<?php if($pay_method_alias == "CRE") echo " selected=\"selected\""; ?>>신용카드</option>
                <option value="ACC"<?php if($pay_method_alias == "ACC") echo " selected=\"selected\""; ?>>계좌이체</option>
                <option value="VAC"<?php if($pay_method_alias == "VAC") echo " selected=\"selected\""; ?>>가상계좌</option>
                <option value="POI"<?php if($pay_method_alias == "POI") echo " selected=\"selected\""; ?>>포인트</option>
                <option value="PHO"<?php if($pay_method_alias == "PHO") echo " selected=\"selected\""; ?>>휴대폰</option>
                <option value="GIF"<?php if($pay_method_alias == "GIF") echo " selected=\"selected\""; ?>>상품권</option>
                <option value="ARS"<?php if($pay_method_alias == "ARS") echo " selected=\"selected\""; ?>>ARS</option>
                <option value="NOP"<?php if($pay_method_alias == "NOP") echo " selected=\"selected\""; ?>>수기결제</option>
            </select>
            <span>필수 선택입니다.</span>
        </div>
        <div>
            <label for="payman_good_name">상품 이름</label>
            <input id="payman_good_name" name="good_name" type="text" value="<?php echo $good_name; ?>" placeholder="상품 이름">
            <span>필수 입력입니다.</span>
        </div>
        <div>
            <label for="payman_good_mny">상품 금액 (원)</label>
            <input id="payman_good_mny" name="good_mny" type="text" value="<?php echo $good_mny; ?>" placeholder="상품 금액">
            <span>필수 입력입니다.</span>
        </div>
        <div>
            <label for="payman_buyr_name">구매자 이름</label>
            <input id="payman_buyr_name" name="buyr_name" type="text" value="<?php echo $buyr_name; ?>" placeholder="구매자 이름">
            <span>필수 입력입니다.</span>
        </div>
        <div>
            <label for="payman_buyr_mail">구매자 이메일</label>
            <input id="payman_buyr_mail" name="buyr_mail" type="email" value="<?php echo $buyr_mail; ?>" placeholder="구매자 이메일">
            <span>필수 입력입니다.</span>
        </div>
        <div>
            <label for="payman_buyr_tel1">구매자 연락처</label>
            <input id="payman_buyr_tel1" name="buyr_tel1" type="text" value="<?php echo $buyr_tel1; ?>" placeholder="구매자 연락처">
            <span>필수 입력입니다.</span>
        </div>
        <div>
            <label for="payman_pay_data">요청 전문</label>
            <input id="payman_pay_data" name="pay_data" type="text" value="<?php echo $pay_data; ?>" placeholder="요청 전문">
        </div>
        <div>
            <label for="payman_chk_agree">
                <input id="payman_chk_agree" name="chk_agree" type="checkbox" value="1" checked="checked"> 전자상거래 약관에 동의합니다.
            </label>
            <button type="submit">결제하기</button>
        </div>
    </fieldset>
</form>
