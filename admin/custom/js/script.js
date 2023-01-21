var isSplash = -1;
function start() {

};
function startF() {
    setTimeout(function () {
        //$('.car1').css({marginRight:-500}).stop().delay(100).animate({marginRight:0},1200,'easeOutBack');
    }, 200);
};
function showSplash() {
    setTimeout(function () {
        // $('.main1').stop().animate({'marginTop': '-350px', 'top': '50%'}, 800, "easeOutExpo");
        // $('header').stop().animate({'marginTop': '0px'}, 800, "easeOutExpo");

        $('.menu .nav1').css({'display': 'block'}).stop().animate({'opacity': '1'}, 800, 'easeOutExpo');
        $('.menu .nav2').css({'display': 'block'}).stop().animate({'opacity': '1'}, 800, 'easeOutExpo');
        $('.menu .nav3').css({'display': 'block'}).stop().animate({'opacity': '1'}, 800, 'easeOutExpo');
        $('.tile .nav1').css({'display': 'block'}).stop().animate({'opacity': '1'}, 800, 'easeOutExpo');

        $('.menu .nav4').stop().delay(0).animate({'marginTop': '0px'}, 800, "easeOutExpo");
        $('.menu .nav5').stop().delay(0).animate({'marginTop': '0px'}, 800, "easeOutExpo");
        $('.menu .nav6').stop().delay(0).animate({'marginTop': '0px'}, 800, "easeOutExpo");
        $('.menu .nav7').stop().delay(0).animate({'marginTop': '0px'}, 800, "easeOutExpo");
        $('.menu .nav8').stop().delay(0).animate({'marginTop': '0px'}, 800, "easeOutExpo");
        $('.menu .nav9').stop().delay(0).animate({'marginTop': '0px'}, 800, "easeOutExpo");
        $('.tile .nav2').stop().delay(0).animate({'marginTop': '0px'}, 800, "easeOutExpo");
        $('.tile .nav4').stop().delay(0).animate({'marginTop': '0px'}, 800, "easeOutExpo");

    }, 400);
};
function hideSplash() {
    // $('.main1').stop().animate({'marginTop': '0px', 'top': '0'}, 800, "easeOutExpo");
    // $('header').stop().animate({'marginTop': '13px'}, 800, "easeOutExpo");

    $('.tile .nav1').stop().animate({'opacity': '0'}, 800, 'easeOutExpo', function () {
        $(this).css({'display': 'none'});
    });
    $('.menu .nav1').stop().animate({'opacity': '0'}, 800, 'easeOutExpo', function () {
        $(this).css({'display': 'none'});
    });
    $('.menu .nav2').stop().animate({'opacity': '0'}, 800, 'easeOutExpo', function () {
        $(this).css({'display': 'none'});
    });
    $('.menu .nav3').stop().animate({'opacity': '0'}, 800, 'easeOutExpo', function () {
        $(this).css({'display': 'none'});
    });

    $('.menu .nav4').stop().delay(0).animate({'marginTop': '-160px'}, 800, "easeOutExpo");
    $('.menu .nav5').stop().delay(0).animate({'marginTop': '-160px'}, 800, "easeOutExpo");
    $('.menu .nav6').stop().delay(0).animate({'marginTop': '-160px'}, 800, "easeOutExpo");
    $('.menu .nav7').stop().delay(0).animate({'marginTop': '-160px'}, 800, "easeOutExpo");
    $('.menu .nav8').stop().delay(0).animate({'marginTop': '-160px'}, 800, "easeOutExpo");
    $('.menu .nav9').stop().delay(0).animate({'marginTop': '-160px'}, 800, "easeOutExpo");
    $('.tile .nav4').stop().delay(0).animate({'marginTop': '-160px'}, 800, "easeOutExpo");
    $('.tile .nav2').stop().delay(0).animate({'marginTop': '-160px'}, 800, "easeOutExpo");
};
function hideSplashQ() {
    // $('.main1').css({'marginTop': '0px', 'top': '0'});
    // $('header').css({'marginTop': '13px'});

    $('.menu .nav1').css({'opacity': '0', 'display': 'none'});
    $('.tile .nav1').css({'opacity': '0', 'display': 'none'});
    $('.menu .nav2').css({'opacity': '0', 'display': 'none'});
    $('.menu .nav3').css({'opacity': '0', 'display': 'none'});

    $('.menu .nav4').css({'marginTop': '-160px'});
    $('.menu .nav5').css({'marginTop': '-160px'});
    $('.menu .nav6').css({'marginTop': '-160px'});
    $('.menu .nav7').css({'marginTop': '-160px'});
    $('.menu .nav8').css({'marginTop': '-160px'});
    $('.menu .nav9').css({'marginTop': '-160px'});
    $('.tile .nav2').css({'marginTop': '-160px'});
    $('.tile .nav4').css({'marginTop': '-160px'});


};

/////////////////////// ready
$(document).ready(function () {
    MSIE8 = ($.browser.msie) && ($.browser.version == 8),
        $.fn.ajaxJSSwitch({
            classMenu: "#menu",
            classSubMenu: ".submenu",
            topMargin: 425,//mandatory property for decktop
            bottomMargin: 60,//mandatory property for decktop
            topMarginMobileDevices: 425,//mandatory property for mobile devices
            bottomMarginMobileDevices: 60,//mandatory property for mobile devices
            delaySubMenuHide: 300,
            fullHeight: true,
            bodyMinHeight: 700,
            menuInit: function (classMenu, classSubMenu) {
                //classMenu.find(">li").each(function(){
                //	$(">a", this).append("<div class='openPart'></div>");
                //})
            },
            buttonOver: function (item) {
                //$('>.over1',item).stop().animate({'opacity':'0.6'},300,'easeOutCubic');
                //$('>.txt1',item).stop().animate({'color':'#ff0000'},300,'easeOutCubic');
            },
            buttonOut: function (item) {
                //$('>.over1',item).stop().animate({'opacity':'0'},300,'easeOutCubic');
                //$('>.txt1',item).stop().animate({'color':'#ffffff'},300,'easeOutCubic');
            },
            subMenuButtonOver: function (item) {
            },
            subMenuButtonOut: function (item) {
            },
            subMenuShow: function (subMenu) {
                //subMenu.stop(true,true).animate({"height":"show"}, 500, "easeOutCubic");
            },
            subMenuHide: function (subMenu) {
                //subMenu.stop(true,true).animate({"height":"hide"}, 700, "easeOutCubic");
            },
            pageInit: function (pages) {
                //console.log('pageInit');
            },
            currPageAnimate: function (page) {
                //console.log('currPageAnimate');
                var Delay = 400; // default
                if (isSplash == -1) { // on reload
                    hideSplashQ();
                    Delay = 0;
                }
                if (isSplash == 0) { // on first time click
                    hideSplash();
                    Delay = 800;
                }
                isSplash = 2;

                // animation of current page
                jQuery('body,html').animate({scrollTop: 0}, 0);

                page.css({"left": $(window).width()}).stop(true).delay(Delay).animate({"left": 0}, 1000, "easeOutCubic", function () {
                    $(window).trigger('resize');
                });
            },
            prevPageAnimate: function (page) {
                //console.log('prevPageAnimate');
                page.stop(true).animate({"display": "block", "left": -$(window).width()}, 500, "easeInCubic");
            },
            backToSplash: function () {
                //console.log('backToSplash');
                if (isSplash == -1) {
                    isSplash = 0;
                    startF();
                }
                else {
                    isSplash = 0;
                    showSplash();
                }
                ;
                $(window).trigger('resize');
            },
            pageLoadComplete: function () {
                //console.log('pageLoadComplete');
            }
        });  /// ajaxJSSwitch end

    ////// sound control
    $("#jquery_jplayer").jPlayer({
        ready: function () {
            $(this).jPlayer("setMedia", {
                mp3: "music.mp3"
            });
            //$(this).jPlayer("play");
            var click = document.ontouchstart === undefined ? 'click' : 'touchstart';
            var kickoff = function () {
                $("#jquery_jplayer").jPlayer("play");
                document.documentElement.removeEventListener(click, kickoff, true);
            };
            document.documentElement.addEventListener(click, kickoff, true);
        },

        repeat: function (event) { // Override the default jPlayer repeat event handler
            $(this).bind($.jPlayer.event.ended + ".jPlayer.jPlayerRepeat", function () {
                $(this).jPlayer("play");
            });
        },
        swfPath: "js",
        cssSelectorAncestor: "#jp_container",
        supplied: "mp3",
        wmode: "window"
    });

    /////// supersized
    jQuery(function ($) {
        $.supersized({
            slides: [{image: 'images/bg' + (Math.random() > .5 ? '1' : '1') + '-v1.jpg', title: ''}]
        });
    });


});

/////////////////////// load
$(window).on('load', function(){
    setTimeout(function () {
        $('#spinner').fadeOut();
        $(window).trigger('resize');
        start();
    }, 100);
    setTimeout(function () {
        $("#jquery_jplayer").jPlayer("play");
    }, 2000);
});