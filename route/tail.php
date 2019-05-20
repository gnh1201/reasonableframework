<?php
if (!defined("_WZ_MOBILE_")) exit; // 개별 페이지 접근 불가 

// 사용자 화면 우측과 하단을 담당하는 페이지입니다.
// 우측, 하단 화면을 꾸미려면 이 파일을 수정합니다.
?>

                </div> <!-- end .content_box -->
            </div> <!-- end #content -->
        </div> <!-- end #container -->

<?php /*
            <div id="aside">
                <div class="aside_box">
                    <div class="aside_title">
                        <h2>업종별</h2>
                    </div>
                    <div class="aside_content">
                        <ul class="nav nav_job">
                        <li><a href="#">미디어/광고</a></li>
                        <li><a href="#">신문/잡지/출판/인쇄</a></li>
                        <li><a href="#">방송/문화/연예/엔터테인먼트</a></li>
                        <li><a href="#">광고/홍보대행/이벤트</a></li>
                        <li><a href="#">디자인/CAD/설계</a></li>
                        <li><a href="#">제조/건설</a></li>
                        <li><a href="#">섬유/의류/패션</a></li>
                        <li><a href="#">건설/토목/건축/인테리어</a></li>
                        <li><a href="#">기계/철강/금속/자동차</a></li>
                        <li><a href="#">건강/의료/제약/바이오</a></li>
                        <li><a href="#">생활화학/화장품</a></li>
                        <li><a href="#">전기/전자</a></li>
                        <li><a href="#">팬시/문구/사무</a></li>
                        <li><a href="#">식품/음료</a></li>
                        <li><a href="#">정보통신/인터넷</a></li>
                        <li><a href="#">웹에이전시</a></li>
                        <li><a href="#">컨텐츠/포털</a></li>
                        <li><a href="#">네트워크/통신/텔레콤</a></li>
                        <li><a href="#">캐릭터/애니메이션</a></li>
                        <li><a href="#">게임/모바일/무선</a></li>
                        <li><a href="#">서비스/유통/물류</a></li>
                        <li><a href="#">백화점/할인점/아울렛</a></li>
                        <li><a href="#">쇼핑몰/전자상거래/오픈마켓</a></li>
                        <li><a href="#">음식료/외식/프랜차이즈</a></li>
                        <li><a href="#">물류/운송/창고</a></li>
                        <li><a href="#">금융(은행/보험/증권/카드)</a></li>
                        <li><a href="#">호텔/관광/여행/항공</a></li>
                        <li><a href="#">스포츠/오락/헬스</a></li>
                        <li><a href="#">교육/학원/유학/학습지</a></li>
                        <li><a href="#">의료/병원</a></li>
                        <li><a href="#">웨딩/스튜디오</a></li>
                        <li><a href="#">디자인</a></li>
                        <li><a href="#">디자인학원</a></li>
                        <li><a href="#">디자인협회/단체</a></li>
                        <li><a href="#">인재파견/용역</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="aside_box">
                    <div class="aside_title">
                        <h2>근무지역별</h2>
                    </div>
                    <div class="aside_content">
                        <ul class="nav nav_loc">
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "seoul")); ?>">서울, 서울특별시</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "gyeonggi")); ?>">경기, 경기도</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "incheon")); ?>">인천, 인천광역시</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "busan")); ?>">부산, 부산광역시</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "deagu")); ?>">대구, 대구광역시</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "deajeon")); ?>">대전, 대전광역시</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "gwangju")); ?>">광주, 광주광역시</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "ulsan")); ?>">울산, 울산광역시</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "sejong")); ?>">세종, 세종특별자치시</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "gangwon")); ?>">강원, 강원도</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "gyeongnam")); ?>">경남, 경상남도</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "gyeongbuk")); ?>">경북, 경상북도</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "jeonnam")); ?>">전남, 전라남도</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "jeonbuk")); ?>">전북, 전라북도</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "chungnam")); ?>">충남, 충청남도</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "chungbuk")); ?>">충북, 충청북도</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "jeju")); ?>">제주, 제주특별자치도</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "all")); ?>">전국</a></li>
                        <li><a href="<?php echo get_route_link("geosearch", array("q" => "oversea")); ?>">해외</a></li>
                        </ul>
                    </div>
                </div>
            </div><!-- /.aside -->
*/ ?>

        <!-- footer { -->
        <footer id="footer" class="tc">
            <div class="list_foot">
                <a class="btn s" href="<?php echo M_URL; ?>" title="홈">홈</a>
<?php if($is_member) { ?>
                <a class="btn s" href="/payman/?route=mobileswitcher&amp;from=pc&amp;do=logout&amp;redirect_url=<?php echo urlencode("http://" . $_SERVER['HTTP_HOST'] . "/m/"); ?>" title="로그아웃">로그아웃</a>
<?php } else { ?>
                <a class="btn s" href="<?php echo M_BBS_URL;?>/login.php?url=<?php echo urlencode($_SERVER[REQUEST_URI]); ?>" title="로그인">로그인</a>
                <a class="btn s" href="<?php echo M_BBS_URL;?>/register_join.php" title="회원가입">회원가입</a>
<?php } ?>
                <a class="btn s" href="/payman/?route=mobileswitcher&amp;from=mobile&amp;redirect_url=<?php echo urlencode("http://" . $_SERVER['HTTP_HOST']); ?>" title="PC버전">PC버전</a>
            </div>
            <div class="wrap_notice">
                <ul class="list_notice">
                    <li><a class="link" href="<?php// echo get_route_link("app"); ?>" title="앱 다운로드">앱 다운로드</a></li>
                    <li><a class="link" href="<?php// echo get_route_link("assignment"); ?>" title="이용약관">이용약관</a></li>
                    <li><a class="link" href="<?php// echo get_route_link("policy"); ?>" title="개인정보취급방침">개인정보취급방침</a></li>
                </ul>
            </div>
            <div class="info_foot">
                <address>
                    잡밴드 JOBBAND &nbsp; 서울특별시 영등포구 도림동 31길 4 대림 위너빌 704호 &nbsp; 사업자등록번호: 605-08-83331<br><hr>
                    <span>통신판매업신고 : 제2010-서울영등포-0490호 &nbsp; 이메일 : jobhankook@naver.com</span>
                </address>
            </div>
            <div class="copyright">
                <span>Copyright ⓒ <a href="http://jobband.co.kr" class="red" target="_blank"><strong>jobband.co.kr</strong></a> All rights reserved.</span>
            </div>
            <hr>
        </footer>
        <!-- } footer -->
    </div><!-- /.wrap -->

    <script>
    <!--
        // 모바일 <-> PC 화면전환을 위한 함수.
        function chagePage(pc_url) {
            $.ajax({
                type : "POST" ,
                async : true ,
                url :  pc_url,
                dataType : "html" , 
                timeout : 30000 ,
                cache : false ,
                data: {},    
                contentType: "application/x-www-form-urlencoded; charset=<?php echo $g4[charset]?>" ,
                error : function(request, status, error) {
                    location.href = "<?php echo $g4[path_pc]?>/?vtype=pc";
                } ,
                success : function(response, status, request) {
                    location.href = pc_url;
                }
            });
        }
    //-->
    </script>

<?php
include_once($g4[path] . "/tail.sub.php");
?>
