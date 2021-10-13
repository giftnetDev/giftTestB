<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="euc-kr">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>기프트넷</title>

<!-- Bootstrap -->
<!--<link href="css/bootstrap.css" rel="stylesheet">-->
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<link href="css/style_v2.css" rel="stylesheet">

<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!-- Top NAV -->
<nav id="topnav">
    <div class="container">
        <ul class="nav navbar-nav navbar-right">
            <li><a href="#">로그인</a></li>
            <li><a href="#">회원가입</a></li>
            <li><a href="#"><span class="glyphicon glyphicon-expand"></span> 장바구니</a></li>
            <li><a href="#">마이페이지</a></li>
            <li><a href="#">고객센터</a></li>
        </ul>
    </div>
</nav>
<!-- //  Top NAV--> 
<!-- Head -->
<div class="container-fluid conatiner-white">
<div class="container">
    <div class="row">
            <div class="col-sm-4 col-lg-3" id="logo">
                <h3><a href="/"><img src="img/common/logo.png" alt="기프트넷"/></a></h3>
            </div>
            <div class="col-sm-4 col-lg-5 text-center" id="search">
                <form class="navbar-form" role="search">
                    <div class="form-group">
                        <input type="text" class="form-control form-search" placeholder="검색">
                    </div>
                    <button type="submit" class="btn btn-default glyphicon glyphicon-search btn-search"></button>
                    <button type="submit" class="btn btn-default btn-search-detail">상세검색</button>
                </form>
            </div>
            <div class="col-sm-4 text-right top-banner">
                <img src="img/banner_top.gif" alt=""/>
            </div>
        </div>
    </div>
</div>
<!-- // Head --> 
<!-- Main NAV -->
<nav class="mainnav navbar navbar-default ">
    <div class="container">
        <ul class="nav nav-justified">
            <li class="dropdown all"><a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="glyphicon glyphicon-menu-hamburger"></span> 전체카테고리</a>
                <div class="dropdown-menu" role="menu">
                    <a href="sub.php">서브 페이지</a><br>
                    <a href="detail1.php">디테일 페이지</a><br>
                    <a href="login.php">로그인 페이지</a><br>
                    <a href="signin_mem_type_select.php">회원가입 페이지</a><br>
                    <a href="content.php">고정 페이지</a><br>
                </div>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="glyphicon glyphicon-gift"></span> 행사별</a>
                <div class="dropdown-menu" role="menu">
                    ddd
                </div>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="glyphicon glyphicon-king"></span> 대상별</a>
                <div class="dropdown-menu" role="menu">
                    ddd
                </div>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="glyphicon glyphicon-stats"></span> 업종별</a>
                <div class="dropdown-menu" role="menu">
                    ddd
                </div>
            </li>
            <li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><span class="glyphicon glyphicon-equalizer"></span> 가격대별</a>
                <div class="dropdown-menu" role="menu">
                    ddd
                </div>
            </li>
        </ul>
    </div>
</nav>
<!-- // Main NAV --> 
<!-- VISUAL -->
<div id="myCarousel" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
        <li data-target="#myCarousel" data-slide-to="1" class=""></li>
        <li data-target="#myCarousel" data-slide-to="2" class=""></li>
    </ol>
    <div class="carousel-inner" role="listbox">
        <div class="item active">
            <img src="" alt="" style="background:#f1c2cd;"/>
            <div class="container">
                <div class="carousel-caption">
                    <a><img src="img/tmp_banner.jpg" alt=""/></a>
                </div>
            </div>
        </div>
        <!---->
        <div class="item">
            <img src="" alt="" style="background:#f1c2cd;"/>
            <div class="container">
                <div class="carousel-caption">
                    <a><img src="img/tmp_banner.jpg" alt=""/></a>
                </div>
            </div>
        </div>
        <!---->
        <div class="item">
            <img src="" alt="" style="background:#f1c2cd;"/>
            <div class="container">
                <div class="carousel-caption">
                    <a><img src="img/tmp_banner.jpg" alt=""/></a>
                </div>
            </div>
        </div>
        <!---->
    </div>
    <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev"> <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> <span class="sr-only">Previous</span> </a> <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next"> <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> <span class="sr-only">Next</span> </a>
</div>
<!-- // VISUAL --> 
<!-- Main Best -->
<div class="container-fluid" id="mainbest">
    <h3 class="text-center"><strong>기프트넷</strong> 베스트 상품 </h3>
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-md-3 col-lg-3 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code text-center">150-107809</p>
                <p class="title text-center">기프트세트 실속 1호</p>
                <p class="price text-center"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-3 item">
                <a href="#" class="thumbnail"> <img src="img/thumb_184.jpg" alt="..."> 
                <p class="code text-center">150-107809</p>
                <p class="title text-center">기프트세트 실속 1호</p>
                <p class="price text-center"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-3 item">
                <a href="#" class="thumbnail"> <img src="img/thumb_184.jpg" alt="...">
                <p class="code text-center">150-107809</p>
                <p class="title text-center">기프트세트 실속 1호</p>
                <p class="price text-center"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-xs-6 col-md-3 col-lg-3 item">
                <a href="#" class="thumbnail"> <img src="img/thumb_184.jpg" alt="...">
                <p class="code text-center">150-107809</p>
                <p class="title text-center">기프트세트 실속 1호</p>
                <p class="price text-center"><strong>7,400</strong>원</p></a>
            </div>
            <div class="control left">
                <a href="#"><span class="glyphicon glyphicon-chevron-left"></span> <span class="sr-only">Previous</span></a>
            </div>
            <div class="control right">
                <a href="#"><span class="glyphicon glyphicon-chevron-right"></span> <span class="sr-only">Next</span></a>
            </div>
        </div>
    </div>
</div>
<!-- // Main Best -->
<!-- 선물세트, 인기품목 -->
<div class="container-fluid conatiner-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-6" id="giftset">
                <h4><span>기프트넷</span> 선물세트</h4>
                <a class="more">+ 더보기</a>
                <div class="row">
                    <div class="col-lg-4 item">
                        <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                        <p class="code">150-107809</p>
                        <p class="title">기프트세트 실속 1호</p>
                        <p class="price"><strong>7,400</strong>원</p></a>
                    </div>
                    <div class="col-lg-4 item">
                        <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                        <p class="code">150-107809</p>
                        <p class="title">기프트세트 실속 1호</p>
                        <p class="price"><strong>7,400</strong>원</p></a>
                    </div>
                    <div class="col-lg-4 item">
                        <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                        <p class="code">150-107809</p>
                        <p class="title">기프트세트 실속 1호</p>
                        <p class="price"><strong>7,400</strong>원</p></a>
                    </div>
                    <div class="col-lg-4 item">
                        <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                        <p class="code">150-107809</p>
                        <p class="title">기프트세트 실속 1호</p>
                        <p class="price"><strong>7,400</strong>원</p></a>
                    </div>
                    <div class="col-lg-4 item">
                        <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                        <p class="code">150-107809</p>
                        <p class="title">기프트세트 실속 1호</p>
                        <p class="price"><strong>7,400</strong>원</p></a>
                    </div>
                    <div class="col-lg-4 item">
                        <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                        <p class="code">150-107809</p>
                        <p class="title">기프트세트 실속 1호</p>
                        <p class="price"><strong>7,400</strong>원</p></a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" id="popular">
                <h4><span>기프트넷</span> 인기품목</h4>
                
                <div class="row">
                    <div class="col-lg-3 item">
                        <a href="#" class="thumbnail"><img src="img/common/popular_01.gif" alt="...">
                        <p class="title text-center">사무/문구</p></a>
                    </div>
                    <div class="col-lg-3 item">
                        <a href="#" class="thumbnail"><img src="img/common/popular_02.gif" alt="...">
                        <p class="title text-center">가방</p></a>
                    </div>
                    <div class="col-lg-3 item">
                        <a href="#" class="thumbnail"><img src="img/common/popular_03.gif" alt="...">
                        <p class="title text-center">컴퓨터용품</p></a>
                    </div>
                    <div class="col-lg-3 item">
                        <a href="#" class="thumbnail"><img src="img/common/popular_04.gif" alt="...">
                        <p class="title text-center">건강용품</p></a>
                    </div>
                    <div class="col-lg-3 item">
                        <a href="#" class="thumbnail"><img src="img/common/popular_05.gif" alt="...">
                        <p class="title text-center">주방용품</p></a>
                    </div>
                    <div class="col-lg-3 item">
                        <a href="#" class="thumbnail"><img src="img/common/popular_06.gif" alt="...">
                        <p class="title text-center">상패/판촉물</p></a>
                    </div>
                    <div class="col-lg-3 item">
                        <a href="#" class="thumbnail"><img src="img/common/popular_07.gif" alt="...">
                        <p class="title text-center">우산</p></a>
                    </div>
                    <div class="col-lg-3 item">
                        <a href="#" class="thumbnail"><img src="img/common/popular_08.gif" alt="...">
                        <p class="title text-center">컵</p></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- // 선물세트, 인기품목 -->
<!-- 브랜드관 -->
<div class="container-fluid" id="brand">
    <div class="container">
        <h4><img src="img/brand_perioe.jpg" alt="페리오"/></h4>
        <div class="row">
            <div class="item col-lg-3">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="item col-lg-3">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="item col-lg-3">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="item col-lg-3">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
        </div>
    </div>
    <div class="container">
        <h4><img src="img/brand_beyond.jpg" alt="비욘드"/></h4>
        <div class="row">
            <div class="item col-lg-3">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="item col-lg-3">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="item col-lg-3">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="item col-lg-3">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
        </div>
    </div>
</div>
<!-- // 브랜드관 -->
<!-- 최근 등록 상품 -->
<div class="container-fluid" id="recent">
    <div class="container">
        <h4><span>기프트넷</span> 최근 등록 상품</h4>
        <div class="row">
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
            <div class="col-lg-2 item">
                <a href="#" class="thumbnail"><img src="img/thumb_184.jpg" alt="...">
                <p class="code">150-107809</p>
                <p class="title">기프트세트 실속 1호</p>
                <p class="price"><strong>7,400</strong>원</p></a>
            </div>
        </div>
    </div>
</div>
<!-- // 최근 등록 상품 -->
<!-- footer #1 -->
<div class="container-fluid footer-1">
    <div class="container">
        <div class="row">
            <div class="col-sm-4 cs-center col-lg-3">
                <h4>고객센터</h4>
                <div class="tel">
                    <span class="glyphicon glyphicon-phone-alt" aria-hidden="true"></span> <span class="title">TEL</span> <strong>031-527-6812</strong>
                </div>
                <div class="office">
                    <!--<span class="title">업무시간</span>--> 
                    <strong>평일 9시 ~ 6시 (점심 12시 ~ 13시)</strong>
                </div>
                <div class="fax">
                    <span class="title">FAX</span> <strong>031-527-6858</strong>
                </div>
            </div>
            <div class="col-sm-4 col-lg-3 bank-account">
                <h4>입금계좌</h4>
                <span class="title">농협은행</span> <strong>105-12-305836</strong> <small>예금주 이기호</small>
            </div>
			
            <div class="col-sm-4 col-lg-3 web-hard">
                <h4>문의</h4>
                <div class="web">
                    <span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> <span class="title"></span> <strong><em></em></strong>
                </div>
                <div class="email">
                    <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span> <span class="title">이메일</span> <strong>gift@giftnet.co.kr</strong>
                </div>
            </div>
			
            <div class="col-sm-4 col-lg-3 community">
                <ul class="list-group">
                    <li class="list-group-item notice"><a href="#">공지사항</a></li>
                    <li class="list-group-item faq"><a href="#">자주묻는 질문</a></li>
                    <li class="list-group-item qna"><a href="#">Q&amp;A</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- // footer #1 --> 
<!-- footer #2 -->
<nav id="links">
    <div class="container">
        <ul class="nav nav-justified">
            <li><a href="#">회사소개</a></li>
            <li><a href="#">개인정보취급방침</a></li>
            <li><a href="#">이용안내</a></li>
            <li><a href="#">협력제휴사</a></li>
            <li><a href="#">제조업체입점안내</a></li>
        </ul>
    </div>
</nav>
<div class="container-fluid" id="footer">
    <div class="container">
        <img src="img/common/logo_foot.gif" alt="기프트넷"/>
        <p>(주)기프트넷 |  경기도 남양주시 진건읍 배양리 98번지 (주)기프트넷 ㅣ 대표이사 양진현 ㅣ 사업자등록번호 132-81-58846 ㅣ 통신판매업고신고 2005-경기남양주-0238<br>
            본 페이지와 상품이미지 저작권은 (주)기프트넷에 있습니다. 상품내용 및 이미지의 무단복제를 금합니다.</p>
        <p>Copyright &copy; 2016 giftnet ALL Rights Reserved.</p>
    </div>
</div>
<!-- // footer #2 --> 
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) --> 
<!--<script src="js/jquery-1.11.3.min.js"></script> -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" crossorigin="anonymous"></script>

<!-- Include all compiled plugins (below), or include individual files as needed --> 
<!--<script src="js/bootstrap.js"></script>-->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
