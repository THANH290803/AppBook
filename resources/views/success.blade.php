<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title></title>
    <link href='https://fonts.googleapis.com/css?family=Lato:300,400|Montserrat:700' rel='stylesheet' type='text/css'>
    <style>
        @import url(//cdnjs.cloudflare.com/ajax/libs/normalize/3.0.1/normalize.min.css);
        @import url(//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css);
    </style>
    <link rel="stylesheet" href="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/default_thank_you.css">
    <script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/jquery-1.9.1.min.js"></script>
    <script src="https://2-22-4-dot-lead-pages.appspot.com/static/lp918/min/html5shiv.js"></script>
</head>
<style>
    @import url(https://fonts.googleapis.com/css?family=Open+Sans:300);

    .inner {
        position: absolute;
        margin: auto;
        width: 50px;
        height: 95px;
        top: 0px;
        left: 0px;
        bottom: 0px;
        right: 0px;
    }

    .inner > div {
        width: 50px;
        height: 50px;
        background-color: rgba(255, 255, 255, 0.7);
        border-radius: 100%;
        position: absolute;
        transition: all 0.5s ease;
    }

    .inner > div:first-child {
        margin-left: -27px;
        animation: one 1.5s linear 1;
    }

    .inner > div:nth-child(2) {
        margin-left: 27px;
        animation: two 1.5s linear 1;
    }

    .inner > div:nth-child(3) {
        margin-top: 54px;
        margin-left: -27px;
        animation: four 1.5s linear 1;
    }

    .inner > div:nth-child(4) {
        margin-top: 54px;
        margin-left: 27px;
        animation: three 1.5s linear 1;
    }

    @keyframes one {
        0% {
            transform: scale(1);
        }
        25% {
            transform: scale(0.3);
        }
        50% {
            transform: scale(1);
        }
        75% {
            transform: scale(1.4);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes two {
        0% {
            transform: scale(1.4);
        }
        25% {
            transform: scale(1);
        }
        50% {
            transform: scale(0.3);
        }
        75% {
            transform: scale(1);
        }
        100% {
            transform: scale(1.4);
        }
    }

    @keyframes three {
        0% {
            transform: scale(1);
        }
        25% {
            transform: scale(1.4);
        }
        50% {
            transform: scale(1);
        }
        75% {
            transform: scale(0.3);
        }
        100% {
            transform: scale(1);
        }
    }

    @keyframes four {
        0% {
            transform: scale(0.3);
        }
        25% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.4);
        }
        75% {
            transform: scale(1);
        }
        100% {
            transform: scale(0.3);
        }
    }

    .inner > div.done {
        margin-left: 0px;
        margin-top:  27px;
    }

    .inner > div.page {
        transform: scale(40);
    }

    .pageLoad {
        position: fixed;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 100vh;
        background-color: #0A0A0A;
        transition: all 0.3s ease;
        z-index: 2;
    }

    .pageLoad.off {
        opacity: 0;
        pointer-events: none;
    }
</style>
<body>
    <div class="pageLoad">
        <div class="inner">
            <div></div>
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
    <header class="site-header" id="header">
        <h1 class="site-header__title" data-lead-id="site-header-title">THANK YOU!</h1>
    </header>

    <div class="main-content">
        <i class="fa fa-check main-content__checkmark" id="checkmark"></i>
        <p class="main-content__body" data-lead-id="main-content-body">Thanks a bunch for filling that out. It means a lot to us, just like you do! We really appreciate you giving us a moment of your time today. Thanks for being you.</p>
    </div>

    <footer class="site-footer" id="footer">
        <a href="http://localhost:3001/">Trở lại trang chủ</a>
    </footer>
</body>
<script>
    setTimeout(function() {
        $('.inner div').addClass('done');

        setTimeout(function() {
            $('.inner div').addClass('page');

            setTimeout(function() {
                $('.pageLoad').addClass('off');

                $('body, html').addClass('on');


            }, 500)
        }, 500)
    }, 1500)
</script>
</html>
