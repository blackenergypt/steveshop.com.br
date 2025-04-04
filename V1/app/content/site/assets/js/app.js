$(document).ready(function() {
    $("#plan1").mouseover(function() {
        $("#plan1").addClass("plan-2");
        $("#plan2").removeClass("plan-2");
        $("#plan2").addClass("plan");
        $("#plan2 .promo-flag div").css({ 'background': '#78b7ea', 'color': '#ffffff' });
    });
    $("#plan1").mouseout(function() {
        $("#plan1").removeClass("plan-2");
        $("#plan1").addClass("plan");
        $("#plan2").addClass("plan-2");
        $("#plan2 .promo-flag div").css({ 'background': '#ffffff', 'color': '#181124' });
    });

    $("#plan3").mouseover(function() {
        $("#plan3").addClass("plan-2");
        $("#plan2").removeClass("plan-2");
        $("#plan2").addClass("plan");
        $("#plan2 .promo-flag div").css({ 'background': '#78b7ea', 'color': '#ffffff' });
    });
    $("#plan3").mouseout(function() {
        $("#plan3").removeClass("plan-2");
        $("#plan3").addClass("plan");
        $("#plan2").addClass("plan-2");
        $("#plan2 .promo-flag div").css({ 'background': '#ffffff', 'color': '#181124' });
    });

    $('.customer-logos').slick({
        slidesToShow: 6,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 1500,
        arrows: false,
        dots: false,
        pauseOnHover: false,
        responsive: [{
            breakpoint: 768,
            settings: {
                slidesToShow: 4
            }
        }, {
            breakpoint: 520,
            settings: {
                slidesToShow: 3
            }
        }]
    });
    /*----------------------*/
    /*    Live Chat         */
    /*----------------------*/

    document.addEventListener('DOMContentLoaded', function() {
        "use strict";
        livechat();

    });

    function livechat() {
        var Tawk_API = Tawk_API || {},
            Tawk_LoadStart = new Date();
        (function() {
            var s1 = document.createElement("script"),
                s0 = document.getElementsByTagName("script")[0];
            s1.async = true;
            s1.src = 'https://embed.tawk.to/5ebb2b2d967ae56c52193869/default';
            s1.charset = 'UTF-8';
            s1.setAttribute('crossorigin', '*');
            s0.parentNode.insertBefore(s1, s0);
        })();
    }
});