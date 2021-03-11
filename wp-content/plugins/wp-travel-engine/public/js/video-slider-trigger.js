jQuery(document).ready(function($) {
    // $('.wte-video-gallery-slider').owlCarousel({
    //     video          : true,
    //     nav            : true,
    //     navigationText : ['&lsaquo;','&rsaquo;'],
    //     items          : 1,
    //     autoplay       : true,
    //     slideSpeed     : 300,
    //     paginationSpeed: 400,
    //     loop           : true,
    //     dots           : false,
    //     navText : ['<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="currentColor" d="M231.293 473.899l19.799-19.799c4.686-4.686 4.686-12.284 0-16.971L70.393 256 251.092 74.87c4.686-4.686 4.686-12.284 0-16.971L231.293 38.1c-4.686-4.686-12.284-4.686-16.971 0L4.908 247.515c-4.686 4.686-4.686 12.284 0 16.971L214.322 473.9c4.687 4.686 12.285 4.686 16.971-.001z"></path></svg>','<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 512"><path fill="currentColor" d="M24.707 38.101L4.908 57.899c-4.686 4.686-4.686 12.284 0 16.971L185.607 256 4.908 437.13c-4.686 4.686-4.686 12.284 0 16.971L24.707 473.9c4.686 4.686 12.284 4.686 16.971 0l209.414-209.414c4.686-4.686 4.686-12.284 0-16.971L41.678 38.101c-4.687-4.687-12.285-4.687-16.971 0z"></path></svg>']
    // });
});

// DOM Ready
jQuery(function($) {
    // POST commands to YouTube or Vimeo API
    function postMessageToPlayer(player, command){
        if (player == null || command == null) return;
        player.contentWindow.postMessage(JSON.stringify(command), "*");
    }

    // When the slide is changing
    function playPauseVideo(slick, control){
        var currentSlide, slideType, startTime, player, video;

        currentSlide = slick.find(".slick-current");
        slideType    = currentSlide.attr("class").split(" ")[1];
        player       = currentSlide.find("iframe").get(0);
        startTime    = currentSlide.data("video-start");
        // debugger;
        if (slideType === "vimeo") {
        switch (control) {
            case "play":
            if ((startTime != null && startTime > 0 ) && !currentSlide.hasClass('started')) {
                currentSlide.addClass('started');
                postMessageToPlayer(player, {
                "method": "setCurrentTime",
                "value" : startTime
                });
            }
            postMessageToPlayer(player, {
                "method": "play",
                "value" : 1
            });
            break;
            case "pause":
            postMessageToPlayer(player, {
                "method": "pause",
                "value": 1
            });
            break;
        }
        } else if (slideType === "youtube") {
        switch (control) {
            case "play":
            postMessageToPlayer(player, {
                "event": "command",
                // "func": "mute"
            });
            postMessageToPlayer(player, {
                "event": "command",
                "func": "playVideo"
            });
            break;
            case "pause":
            postMessageToPlayer(player, {
                "event": "command",
                "func": "pauseVideo"
            });
            break;
        }
    } else if (slideType === "video") {
            video = currentSlide.children("video").get(0);
            if (video != null) {
                if (control === "play"){
                    video.play();
                } else {
                    video.pause();
                }
            }
        }
    }

    // Resize player
    function resizePlayer(iframes, ratio) {
        if (!iframes[0]) return;
        var win = $(".main-slider"),
            width = win.width(),
            playerWidth,
            height = win.height(),
            playerHeight,
            ratio = ratio || 16/9;

        iframes.each(function(){
            var current = $(this);
            if (width / ratio < height) {
                playerWidth = Math.ceil(height * ratio);
                current.width(playerWidth).height(height).css({
                left: (width - playerWidth) / 2,
                    top: 0
                });
            } else {
                playerHeight = Math.ceil(width / ratio);
                current.width(width).height(playerHeight).css({
                left: 0,
                top: (height - playerHeight) / 2
                });
                }
            });
    }
    // Initialize
    var slideWrapper = $('.main-slider');
    // slideWrapper.on("init", function(slick){
    //     slick = $(slick.currentTarget);
    //     setTimeout(function(){
    //         playPauseVideo(slick,"play");
    //     }, 1000);
    //     // resizePlayer(iframes, 16/9);
    // });
    slideWrapper.on("beforeChange", function(event, slick) {
        slick = $(slick.$slider);
        playPauseVideo(slick,"pause");
    });
    slideWrapper.on("afterChange", function(event, slick) {
        slick = $(slick.$slider);
        playPauseVideo(slick,"play");
    });

     //start the slider
    slideWrapper.slick({
        lazyLoad:"progressive",
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        dots: false,
        fade: true,
        asNavFor: '.slider-nav',
        cssEase:"cubic-bezier(0.87, 0.03, 0.41, 0.9)"
    });

    $('.slider-nav').slick({
        slidesToShow: 3,
        // autoplay: true,
        // autoplaySpeed: 2000,
        slidesToScroll: 1,
        asNavFor: '.main-slider',
        dots: false,
        arrows: true,
        focusOnSelect: true
    });
});
