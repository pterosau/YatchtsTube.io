var youtubeapidfd = $.Deferred();

function onYouTubeIframeAPIReady() {
  youtubeapidfd.resolve();
}

(function($) {
  var KEYCODE_ESC = 27;
  var thumbnailformat = "http://img.youtube.com/vi/VIDEOID/0.jpg";
  var ytubelightbox = '<div class="videobox">' +
    '<div class="centeredchild">' +
    '<div class="videowrapper">' +
    '<div id="videoplayer"></div>' +
    "</div>" +
    "</div>" +
    "</div>";

  var defaults = {};
  var $videobox;
  var youtubeplayer;
  var ismobile =
    navigator.userAgent.match(
      /(iPad)|(iPhone)|(iPod)|(android)|(webOS)/i
    ) != null;
  var isiOS = navigator.userAgent.match(/(iPad)|(iPhone)|(iPod)/i) != null;

  function getyoutubeid(link) {
    var youtubeidreg = /(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/ ]{11})/i;
    return youtubeidreg.exec(link)[1];
  }

  function createlightbox() {
    $videobox = $(ytubelightbox).appendTo(document.body);
    $videobox.on("click", function() {
      hidelightbox();
    });
    $videobox.find(".centeredchild").on("click", function(e) {
      e.stopPropagation();
    });
  }

  function showlightbox() {
    $(document.documentElement).addClass("hidescrollbar");
    $videobox.css({
      display: "block"
    });
  }

  function hidelightbox() {
    $(document.documentElement).removeClass("hidescrollbar");
    $videobox.css({
      display: "none"
    });
    youtubeplayer.stopVideo();
  }

  var tag = document.createElement("script");
  tag.src = "https://www.youtube.com/iframe_api";
  var firstScriptTag = document.getElementsByTagName("script")[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

  youtubeapidfd.then(function() {
    createlightbox();
    $(document).on("keyup", function(e) {
      if (typeof $videobox != "undefined") {
        if (e.keyCode == KEYCODE_ESC) hidelightbox();
      }
    });
  });

  function createyoutubeplayer(videourl, containerid) {
    youtubeplayer = new YT.Player(containerid, {
      videoId: videourl,
      playerVars: {
        autoplay: 1
      }
    });
  }

  jQuery.fn.youtubegallery = function(options) {
    var s = $.extend({}, defaults, options);
    return this.each(function() {
      var $ul = $(this);
      $ul.addClass("youtubewall");
      $.ajax({
        url: 'load_urls.php', // Replace with your server-side script to load URLs
        success: function(response) {
          var urls = response.split('\n');
          urls.forEach(function(url) {
            if (url.trim() !== '') {
              var videoid = getyoutubeid(url);
              var thumbnail = thumbnailformat.replace("VIDEOID", videoid);
              var $li = $('<li><div class="thumbwrap"><img src="' + thumbnail + '" /><div class="panel"><div class="panelinner"><i class="play fa fa-play-circle-o"></i> <a class="externallink fa fa-external-link-square" href="' + url + '"></a></div></div></div></li>');
              if (ismobile) {
                $li.css({
                  cursor: "pointer"
                });
              }
              if (!doclink) {
                $li.find(".externallink").css({
                  display: "none"
                });
              }
              $li.find(".panel").find(".play").on("click", function() {
                if (typeof $videobox != "undefined") {
                  showlightbox();
                  if (typeof youtubeplayer == "undefined") {
                    createyoutubeplayer(videoid, "videoplayer");
                  } else {
                    if (isiOS) {
                      youtubeplayer.cueVideoById(videoid);
                    } else {
                      youtubeplayer.loadVideoById(videoid);
                    }
                  }
                }
              });
              $ul.append($li);
            }
          });
        },
        error: function(xhr, status, error) {
          alert('Error loading URLs');
          console.error(xhr, status, error);
        }
      });
    });
  };
})(jQuery);

jQuery(function() {
  $('#youtubelist').youtubegallery();
});
