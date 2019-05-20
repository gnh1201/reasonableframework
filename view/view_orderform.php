<!doctype html>
<html lang="ko">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="route:orderform">
        <meta name="author" content="https://exts.kr/go/framework">

        <title>결제정보 입력</title>

        <link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css" integrity="sha384-nn4HPE8lTHyVtfCBi5yW9d20FjT8BJwUXyWZT9InLYax14RDjBj46LmSztkmNP9w" crossorigin="anonymous">
        <link rel="stylesheet" href="<?php echo base_url(); ?>view/public/css/simple.css">
    </head>
    <body>
        <div id="main">
            <div class="header">
                <h1>PAYMANT</h1>
                <h2>감사합니다. 결제 정보를 확인하여 주세요.</h2>
            </div>
            
            <div class="content">
                <h2 id="default-form" class="content-subhead">결제 및 환불 문의</h2>
                <p>결제 및 환불 관련 문의는 <code>webmaster@jobband.kr</code>으로 해주시기 바랍니다.</p>

                <form id="orderform" name="orderform" method="post" class="pure-form pure-form-aligned">
                    <fieldset>
                        <legend>결제 정보 입력</legend>
                        <div class="hidden">
                            <input type="hidden" name="_token" value="<?php echo $_token; ?>">
                            <input type="hidden" name="route" value="<?php echo $_next_route; ?>">
                            <input type="hidden" name="redirect_url" value="<?php echo $redirect_url; ?>">
                        </div>

                        <div class="pure-control-group">
                            <label for="pay_method_alias">결제방법</label>
                            <select id="pay_method_alias" name="pay_method_alias">
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
                            <span class="pure-form-message-inline">필수 선택입니다.</span>
                        </div>
                        <div class="pure-control-group">
                            <label for="good_name">상품 이름</label>
                            <input id="good_name" name="good_name" type="text" value="<?php echo $good_name; ?>" placeholder="상품 이름">
                            <span class="pure-form-message-inline">필수 입력입니다.</span>
                        </div>
                        <div class="pure-control-group">
                            <label for="good_mny">상품 금액 (원)</label>
                            <input id="good_mny" name="good_mny" type="text" value="<?php echo $good_mny; ?>" placeholder="상품 금액">
                            <span class="pure-form-message-inline">필수 입력입니다.</span>
                        </div>
                        <div class="pure-control-group">
                            <label for="buyr_name">구매자 이름</label>
                            <input id="buyr_name" name="buyr_name" type="text" value="<?php echo $buyr_name; ?>" placeholder="구매자 이름">
                            <span class="pure-form-message-inline">필수 입력입니다.</span>
                        </div>
                        <div class="pure-control-group">
                            <label for="buyr_mail">구매자 이메일</label>
                            <input id="buyr_mail" name="buyr_mail" type="email" value="<?php echo $buyr_mail; ?>" placeholder="구매자 이메일">
                            <span class="pure-form-message-inline">필수 입력입니다.</span>
                        </div>
                        <div class="pure-control-group">
                            <label for="buyr_tel1">구매자 연락처</label>
                            <input id="buyr_tel1" name="buyr_tel1" type="text" value="<?php echo $buyr_tel1; ?>" placeholder="구매자 연락처">
                            <span class="pure-form-message-inline">필수 입력입니다.</span>
                        </div>
                        <div class="pure-control-group">
                            <label for="pay_data">요청 전문</label>
                            <input id="pay_data" name="pay_data" type="text" value="<?php echo $pay_data; ?>" placeholder="요청 전문">
                        </div>
                        <div class="pure-controls">
                            <label for="chk_agree" class="pure-checkbox">
                                <input id="chk_agree" name="chk_agree" type="checkbox" value="1"> 전자상거래 약관에 동의합니다.
                            </label>
                            <button type="submit" class="pure-button pure-button-primary">결제하기</button>
                        </div>
                    </fieldset>
                </form>
                
                <p><small>this software granted to jobband.kr. powered by <a href="https://exts.kr/go/framework">ReasonableFramework</a></small></p>
            </div>
        </div>

        <?php echo $jsoutput; ?>
    </body>
</html>
