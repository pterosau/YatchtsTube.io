<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Yatchts Tube</title>

  <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://codepen.io/olafjupiter/pen/youtubegallerywall.css" />

  <!--[if lte IE 8]>

	<style>
	/* IE8 and below specific CSS */

	ul.youtubewall li .thumbwrap:after{
		display: none; /* hide thumbnail overlay and show thumbnail icons by default */
	}

	</style>

<![endif]-->

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

  <script >
// Define YouTube Gallery Wall functionality
var youtubeapidfd = $.Deferred();

// Function to handle YouTube API readiness
function onYouTubeIframeAPIReady() {
  youtubeapidfd.resolve();
}

// Immediately invoked function expression (IIFE) for YouTube Gallery
(function ($) {
  var KEYCODE_ESC = 27;
  var thumbnailformat = "http://img.youtube.com/vi/VIDEOID/0.jpg";
  var ytubelightbox =
    '<div class="videobox">' +
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
    $videobox.on("click", function () {
      hidelightbox();
    });
    $videobox.find(".centeredchild").on("click", function (e) {
      e.stopPropagation();
    });
  }

  function showlightbox() {
    $(document.documentElement).addClass("hidescrollbar");
    $videobox.css({ display: "block" });
  }

  function hidelightbox() {
    $(document.documentElement).removeClass("hidescrollbar");
    $videobox.css({ display: "none" });
    youtubeplayer.stopVideo();
  }

  var tag = document.createElement("script");
  tag.src = "https://www.youtube.com/iframe_api";
  var firstScriptTag = document.getElementsByTagName("script")[0];
  firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

  youtubeapidfd.then(function () {
    createlightbox();
    $(document).on("keyup", function (e) {
      if (typeof $videobox != "undefined") {
        if (e.keyCode == KEYCODE_ESC) hidelightbox();
      }
    });
  });

  function createyoutubeplayer(videourl, containerid) {
    youtubeplayer = new YT.Player(containerid, {
      videoId: videourl,
      playerVars: { autoplay: 1 },
    });
  }

  jQuery.fn.youtubegallery = function (options) {
    var s = $.extend({}, defaults, options);
    return this.each(function () {
      var $ul = $(this);
      $ul.addClass("youtubewall");
      var $lis = $ul.find("li");
      $lis.each(function (i) {
        var $li = $(this);
        var link = $li.find("a").get(0);
        var videoid = getyoutubeid(link.getAttribute("href"));
        var thumbnail = thumbnailformat.replace("VIDEOID", videoid);
        var doclink = link.getAttribute("data-url");
        if (ismobile) {
          $li.css({ cursor: "pointer" });
        }
        $li.html(
          '<div class="thumbwrap">' +
            '<img src="' +
            thumbnail +
            '" />' +
            '<div class="panel"><div class="panelinner"><i class="play fa fa-play-circle-o"></i> <a class="externallink fa fa-external-link-square" href="' +
            doclink +
            '"></a></div></div>' +
            "</div>"
        );
        if (!doclink) {
          $li.find(".externallink").css({ display: "none" });
        }
        $li.find(".panel").find(".play").on("click", function () {
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
      });
    });
  };
})(jQuery);

// Document ready function
jQuery(function () {
  // Initialize YouTube gallery
  $("#youtubelist").youtubegallery();

  // Form submission to add new YouTube video URL
  $('#uploadForm').submit(function(event) {
    event.preventDefault();
    var videoUrl = $('#videoUrlInput').val().trim();
    if (videoUrl !== '') {
      var listItem = '<li><a href="' + videoUrl + '">' + videoUrl + '</a> <button class="deleteButton">Delete</button></li>';
      $('#youtubelist').append(listItem);
      $('#videoUrlInput').val(''); // Clear input field
    }
  });

  // Delete button functionality for dynamically added items
  $('#youtubelist').on('click', '.deleteButton', function() {
    $(this).closest('li').remove();
  });
});

</script>
  <script>
    jQuery(function() { // on DOM load
      //syntax $(selector).youtubegallery()
      $('#youtubelist').youtubegallery()
    })
  </script>
<style>
html.hidescrollbar {
  overflow-x: hidden;
}

html.hidescrollbar body {
  overflow-x: hidden;
}

/* ###### CSS for video thumbnails ###### */

ul.youtubewall {
  margin: 0;
  padding: 0;
  list-style: none;
  width: 100%;
  overflow: hidden;
}

ul.youtubewall li {
  width: 25%; /* by default, show 4 columns of thumbnails */
  float: left;
  display: inline;
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  padding: 5px; /* general spacing between thumbnails */
  margin-bottom: 8px; /* bottom spacing between thumbnails */
}

ul.youtubewall li .thumbwrap {
  position: relative;
  overflow: hidden;
  display: block;
}

/* thumbnail overlay */
ul.youtubewall li .thumbwrap:after {
  content: "";
  position: absolute;
  width: 100%;
  height: 100%;
  left: 0;
  top: 0;
  background: black;
  opacity: 0;
  -webkit-transition: opacity 0.6s;
  transition: opacity 0.6s;
}

ul.youtubewall li .panel {
  position: absolute;
  display: block;
  width: 100%;
  height: 100%;
  z-index: 1000;
  top: 0;
  left: 0;
  opacity: 0;
  text-align: center;
}

/* technique to center panel vertically */
ul.youtubewall li .panel:before {
  content: "";
  display: inline-block;
  height: 100%;
  vertical-align: middle;
}

ul.youtubewall li .panel .panelinner {
  display: inline-block;
  position: relative;
  vertical-align: middle; /* center .panelinner vertically */
  -webkit-transform: translate3d(0, -20px, 0);
  transform: translate3d(0, -20px, 0);
  -webkit-transition: all 0.5s;
  transition: all 0.5s;
}

/* fontawesome elements style */
ul.youtubewall li .panel i,
ul.youtubewall li .panel a {
  font-size: 44px;
  color: white;
  cursor: pointer;
  text-decoration: none;
}

ul.youtubewall li .panel a {
  margin-left: 15px;
}

ul.youtubewall li img {
  width: 100%;
  height: auto;
  float: left;
}

ul.youtubewall li:hover .thumbwrap:after {
  opacity: 0.4;
}

ul.youtubewall li:hover .panel {
  opacity: 1;
}

ul.youtubewall li:hover .panel .panelinner {
  -webkit-transform: translate3d(0, 0, 0);
  transform: translate3d(0, 0, 0);
}

/* ###### CSS for video lightbox that pops up ###### */

.videobox {
  position: fixed;
  width: 100%; /* can be any width */
  height: 100%;
  left: 0;
  top: 0;
  display: none;
  z-index: 1000;
  text-align: center;
}

.videobox:before {
  /* pseudo element to force vertical centering of child element */
  content: "";
  display: inline-block;
  height: 100%;
  vertical-align: middle;
}

.videobox:after {
  /* pseudo element to create overlay */
  background: black;
  content: "";
  position: absolute;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  opacity: 0.5;
  z-index: 10;
}

/* Centered child element can be any width and height */
.centeredchild {
  position: relative; /* position element to participate in z-indexing */
  z-index: 20; /* higher z-index than overlay */
  display: inline-block;
  vertical-align: middle;
  width: 75%; /* width of video player relative to browser */
  background: transparent;
}

/* Video container to maintain Youtube 16:9 aspect ratio */
.videowrapper {
  position: relative;
  padding-top: 25px;
  padding-bottom: 56.25%; /* 16:9 aspect ratio */
  height: 0;
}

/* Make Youtube IFRAME responsive */
.videowrapper iframe {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

/* ####### responsive layout CSS ####### */

@media (max-width: 800px) {
  /* FIRST breaking point */
  ul.youtubewall li {
    width: 33%; /* reduce to 3 columns of thumbnails */
  }

  .centeredchild {
    width: 90%; /* enlarge video player container */
  }
}

@media (max-width: 480px) {
  /* SECOND breaking point */
  ul.youtubewall li {
    width: 50%; /* reduce to 2 columns of thumbnails */
  }
}


* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.container {
  width: 100vw;
  height: 100vh;
  background-image: url("https://images.unsplash.com/photo-1542804540-bff9557c17df?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D");
  object-fit: cover;
  background-size: cover;
  filter: brightness(120%) saturate(150%) drop-shadow(2px 2px 10.5rem #fff);
}

nav {
  ;
  z-index:99;
  height: 14%;
  width: 100%;
  background-color: transparent;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 2rem;
  border-bottom: 2px solid #ddd;
  box-shadow: 0px 0px 0.5rem 5px;
}

.logo {
  font-size: 2vw;
  font-weight: 900;
  letter-spacing: 0.2vw;
  text-shadow: 1px 1px 1px;
  animation: sidein 1s linear 0s 1 alternate;
  color: #fff;
}

.nav-menu {
  ;
  width: fit-content;
  height: 12vh;
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  animation: sidein 5s linear 0s 1 alternate;
}

.nav-menu a {
  display: flex;
  justify-content: space-evenly;
  align-items: center;
  width: 8vw;
  height: 6vh;
  text-decoration: none;
  border-radius: 4px;
  font-size: 1.5vw;
  font-weight: 300;
  letter-spacing: 0.2vw;
  text-shadow: 1px 1px 1px #000;
  margin: 1vw;
  color: #fff;
  box-shadow: 1px 1px 2px 1px gray;
}

.menubtn,
.closebtn {
  display: none;
  font-size: 1.6rem;
  border: none;
  font-weight: 700;
  color: #fff;
}

@keyframes sidein {
  from {
    margin-left: 100%;
  }

  to {
    margin-left: 0px;
  }
}

.nav-menu a:hover {
  animation: rotate 3s linear 0s 2 alternate;
}

@keyframes rotate {
  from {
    transform: skew(20deg);
    box-shadow: 1px 1px 1px 3px lightslategray;
    filter: drop-shadow(1px 1px 2rem #fff);
    border: none;
    border-radius: 8px;
  }

  to {
    transform: rotate(60deg);
    box-shadow: 1px 1px 1px 3px #fff;
    filter: drop-shadow(1px 1px 2rem red);
    border: none;
    border-radius: 8px;
  }
}

/* Start hero section */

main {
  width: 100%;
  height: calc(100% - 14%);
  display: flex;
  justify-content: center;
  align-items: center;
}

.main-container {
  width: 80%;
  height: 90%;
  display: flex;
  filter: brightness(120%) saturate(150%) drop-shadow(0px 0px 5px);
}

.left {
  width: 50%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  border: none;
  border-radius: 8px;
  filter: drop-shadow(0px 0px 2px #000);
  animation: bgchange 5s linear 0s infinite alternate forwards;
}

.left button {
  padding: 0.6rem 2.5vw;
  font-size: 1.2vw;
  font-weight: 900;
  border-radius: 4px;
  border: none;
  color: #fff;
  background: none;
  box-shadow: 0 0 4px;
  text-shadow: 0 0 2px #000;
  letter-spacing: 0.2vw;
  cursor: pointer;
}

.left button:hover {
  animation: rotate 3s linear 0s 2 alternate;
}

.right {
  width: 50%;
  height: 100%;
  border: none;
  filter: drop-shadow(1px 1px 5px #000);
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  color: #fff;
}

.right h2 {
  font-size: 2.2vw;
  margin-top: 2vw;
  text-shadow: 0 0 2px #000;
  letter-spacing: 0.2vw;
}

.right form {
  width: 100%;
  height: 100%;
  display: flex;
  padding-top: 10vw;
  align-items: center;
  flex-direction: column;
}

.right form label {
  font-size: 1.4vw;
  letter-spacing: 0.1vw;
  display: flex;
  justify-content: center;
  padding-top: 0.3vw;
  flex-direction: column;
}

.right form label input {
  width: 20vw;
  height: 5vh;
  border-top: none;
  border-left: none;
  border-right: none;
  background: none;
  border-bottom-color: #fff;
  outline: none;
  color: #fff;
  text-shadow: 0 0 2px #000;
  letter-spacing: 0.1vw;
  font-size: 1vw;
}

.right form button {
  padding: 0.6rem 2.5vw;
  font-size: 1.2vw;
  font-weight: 900;
  border-radius: 4px;
  border: none;
  color: #fff;
  background: none;
  box-shadow: 0 0 4px;
  text-shadow: 0 0 2px #000;
  letter-spacing: 0.2vw;
  margin: 3.5vw;
  margin-left: -5vw;
}

.rotate-border {
  position: relative;
  padding: 10px 20px;
  border: none;
  color: #ffffff;
  overflow: hidden;
  cursor: pointer;
}

.rotate-border::before {
  content: "";
  position: absolute;
  top: -5px;
  left: -5px;
  width: calc(100% + 10px);
  height: calc(100% + 10px);
  border: 2px solid #ffffff;
  animation: rotateBorder 5s linear infinite;
}

@keyframes rotateBorder {
  0% {
    transform: rotate(0deg);
    filter: drop-shadow(1px 1px 1rem red);
  }

  100% {
    transform: rotate(360deg);
    filter: drop-shadow(1px 1px 1rem #fff);
  }
}

@keyframes bgchange {
  from {
    background-image: url("https://images.unsplash.com/photo-1457732815361-daa98277e9c8?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D");
    object-fit: cover;
    background-size: cover;
  }

  to {
    background-image: url("https://images.unsplash.com/photo-1542804540-bff9557c17df?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D");
    object-fit: cover;
    background-size: cover;
  }
}

@media only screen and (max-width: 450px) {
  nav .nav-menu {
    display: none;
  }

  /* Start Navbar */
  nav .logo {
    font-size: 2.1rem;
  }

  .menubtn {
    display: block;
  }

  nav .nav-menu {
    position: fixed;
    left: 0;
    right: 0;
    margin-top: 51.8rem;
    height: 85%;
    z-index: 999;
    background-color: gray;
  }

  nav .nav-menu a {
    padding: 3rem;
    min-width: 100vw;
    font-size: 2.1rem;
  }

  /* End Navbar */

  /* Start Hero section */
  .main-container {
    width: 100%;
    height: 90%;
    display: flex;
    flex-direction: column;
  }

  .left {
    width: 100%;
    height: 100%;
  }

  .left button {
    padding: 0.6rem 8.5vw;
    font-size: 7.2vw;
    font-weight: 900;
  }

  .right {
    width: 100%;
    height: 100%;
  }

  .right h2 {
    font-size: 9.2vw;
  }

  .right form {
    width: 100%;
    height: 100%;
  }

  .right form label {
    font-size: 4.4vw;
    letter-spacing: 0.1vw;
  }

  .right form label input {
    width: 75vw;
    height: 5vh;
    font-size: 5vw;
  }

  .right form button {
    padding: 1.2rem 20vw;
    font-size: 5.2vw;
    margin-left: -9vw;
  }

  /* End Hero section */
}

</style>
</head>


<nav style="position:fixed;background-color:black;color:white;">
  <div class="logo"><img src="logo.jpg" alt="" height="100" width="100"></div>
  <h1>Yatchts Tube</h1>
  <div class="nav-menu">
    <a href="#">Home</a>
    <a href="#">About</a>
    <a href="#">Blog</a>
    <a href="#">Career</a>
    <a href="#">Template</a>
  </div>
  <span class="menubtn" onclick="openMenu()">&#9776</span>
  <span class="closebtn" onclick="closeMenu()"><i class="fa-solid fa-x"></i></span>

</nav>


</div>

<body>
 <ul id="youtubelist">
    

    <li><a href="https://www.youtube.com/watch?v=astISOttCQ0"></a></li>
    <li><a href="https://www.youtube.com/watch?v=emBFGrQzUCo"></a></li>
    <li><a href="https://www.youtube.com/watch?v=dOnheTdXJEU"></a></li>
    <li><a href="https://www.youtube.com/watch?v=960smAXYzQU"></a></li>
    <li ><a href="https://www.youtube.com/watch?v=jhkBAKV1yMg</li>"></a></li>


    <li ><a href="https://www.youtube.com/watch?v=LN5lzg3TE60</li>"></a></li>
    <li ><a href="https://www.youtube.com/watch?v=R2WxaeIJcqY</li>"></a></li>
    <li ><a href="https://www.youtube.com/watch?v=ECG6EjE1cjY</li>"></a></li>
    <li ><a href="https://www.youtube.com/watch?v=rT1GMoi0hGo</li>"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=ZWEtWo4eYsQ"></a></li>


   <li><a href=" https://www.youtube.com/watch?v=dmmH8C0PaFg"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=7MzjBxmB9mA"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=79FhEhWGXjw"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=zYKD7YYKJbA"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=XqwGt69pDXQ"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=Sn4UNTw6rVI"></a></li>
    <li><a href="https://www.youtube.com/watch?v=cTjQp_TQlXo"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=VAAFPntJtnw"></a></li>


   <li><a href="https://www.youtube.com/watch?v=6eurHPpkgfA"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=YBDU_MWsD98"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=eCEENfXkDe4"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=nf1osBShsi4"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=eE2-zYEldGc"></a></li>

   <li><a href=" https://www.youtube.com/watch?v=2Ewf48Eg-GQ"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=TelF9lV5tgE"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=qoy7Vuac0kE"></a></li>
   <li><a href=" https://www.youtube.com/watch?v=r9PeYPHdpNo"></a></li>


   <li><a href=" https://www.youtube.com/watch?v=grk0TQGnWls&list=RDCMUCT6_klyo6_nWFGzbvvRa-qw&start_radio=1"></a><</li>
  <li><a href="https://www.youtube.com/watch?v=_FJ1TB0nwHs"></a></li>
  <li><a href="https://www.youtube.com/watch?v=w4x0R4yqr8M"></a></li>
  <li><a href="https://www.youtube.com/watch?v=JkaxUblCGz0"></a></li>
  <li><a href="https://www.youtube.com/watch?v=-RGOk2fNLeU"></a></li>
  <li><a href="https://www.youtube.com/watch?v=SsGNUn-WI5c"></a></li>
  <li><a href="https://www.youtube.com/watch?v=ZJg2vNMmIn8"></a></li>
  <li><a href="https://www.youtube.com/watch?v=IWHP7Jtyqzk"></a></li>
  <li><a href="https://www.youtube.com/watch?v=f6l8_d2VtV0"></a></li>

  <li><a href="https://www.youtube.com/watch?v=4XUGUe1lWqw"></a></li>
  <li><a href="https://www.youtube.com/watch?v=3_ziO1gFOsQ"></a></li>
  <li><a href="https://www.youtube.com/watch?v=9cz4ikFcwMY"></a></li>
  <li><a href="https://www.youtube.com/watch?v=43lNIjYvDKQ"></a></li>
  <li><a href="https://www.youtube.com/watch?v=Bu8AbtM0xA4"></a></li>
  <li><a href="https://www.youtube.com/watch?v=cdC2b6IL9No"></a></li>
  <li><a href="https://www.youtube.com/watch?v=-DbstZntGvA"></a></li>


  <li><a href="https://www.youtube.com/watch?v=gucs_Q652FA"></a></li>
  <li><a href="https://www.youtube.com/watch?v=k-to9p3v1jY"></a></li>
  <li><a href="https://www.youtube.com/watch?v=5EGacrmhxn8"></a></li>
  <li><a href="https://www.youtube.com/watch?v=G6bWwYT3Cg8"></a></li>
  <li><a href="https://www.youtube.com/watch?v=9J2K-KQ2psk"></a></li>
  <li><a href="https://www.youtube.com/watch?v=OlJwDXelMcM"></a></li>
  <li><a href="https://www.youtube.com/shorts/37HYRv8TefM"></a></li>
  <li><a href="https://www.youtube.com/shorts/XVnBvmLc9_U"></a></li>
  <li><a href="https://www.youtube.com/shorts/eDnw6dK2lb4"></a></li>


  <li><a href="https://www.youtube.com/watch?v=xiP35N4m1zQ"></a></li>
  <li><a href="https://www.youtube.com/watch?v=G0OGEk45e24"></a></li>
  <li><a href="https://www.youtube.com/watch?v=DtliuohBgB0"></a></li>
  <li><a href="https://www.youtube.com/watch?v=ewXLOSEeyKA"></a></li>
  <li><a href="https://www.youtube.com/watch?v=M7c0nkjzejc"></a></li>
  <li><a href="https://www.youtube.com/watch?v=fkGEHhM2KsE"></a></li>


  <li><a href="https://www.youtube.com/watch?v=sdYJaTIM6ss"></a></li>
  <li><a href="https://www.youtube.com/watch?v=DKcQAbbwofU"></a></li>
  <li><a href="https://www.youtube.com/watch?v=TmRpPummBfQ"></a></li>
  <li><a href="https://www.youtube.com/watch?v=2GH1c50zcYc&t=256s"></a></li>
  <li><a href="https://www.youtube.com/watch?v=e3JPe75W-Eg"></a></li>
  <li><a href="https://www.youtube.com/watch?v=nw7bGe35c80"></a></li>
  <li><a href="https://www.youtube.com/watch?v=o8F8IajtW9U"></a></li>


  <li><a href="https://www.youtube.com/watch?v=gPBhGkBN30s"></a></li>
  <li><a href="https://www.xnxx.com/video-u2rph13/mz_natural_takes_long_dick_with_facial"></a></li>
  <li><a href="https://www.youtube.com/watch?v=4LhkXX9r6kY"></a></li>
  <li><a href="https://www.youtube.com/watch?v=Jsk7RnnVtsw"></a></li>
  <li><a href="https://www.youtube.com/watch?v=D7ukzgYplNI"></a></li>
  <li><a href="https://www.youtube.com/watch?v=D7ukzgYplNI"></a></li>
  <li><a href="https://www.youtube.com/shorts/-hzue8KIS9M"></a></li>


  <li><a href="https://www.youtube.com/shorts/JjOoeeIKFtc"></a></li>
  <li><a href="https://www.youtube.com/shorts/FU7CfewRva4"></a></li>
  <li><a href="https://www.youtube.com/watch?v=Ou-ZqYZVrjA"></a></li>
  <li><a href="https://www.youtube.com/watch?v=vyI3gCBa_hM"></a></li>
  <li><a href="https://www.youtube.com/watch?v=M_gXds_w9xs"></a></li>
  <li><a href="https://www.youtube.com/watch?v=jRilLbOmtWY"></a></li>


  <li><a href="https://www.youtube.com/watch?v=5cD3DJOrlbs"></a></li>
  <li><a href="https://www.youtube.com/watch?v=WUTQBxAEnYI"></a></li>
  <li><a href="https://www.youtube.com/watch?v=nLJRwIk0Lw4"></a></li>
  <li><a href="https://www.youtube.com/watch?v=iDyWWtY-Xaw"></a></li>
  <li><a href="https://www.youtube.com/watch?v=u4GJkXYVrCE"></a></li>
  <li><a href="https://www.youtube.com/watch?v=kIDHDif7fmo"></a></li>
  <li><a href="https://www.youtube.com/watch?v=QdGDw8uqhsQ"></a></li>

  
  <li><a href="https://www.youtube.com/watch?v=wiZeMc6xW3E"></a></li>
  <li><a href="https://www.youtube.com/watch?v=i4n0w6MGDPQ"></a></li>
  <li><a href="https://www.youtube.com/watch?v=QRmRnkamkxg"></a></li>
  <li><a href="https://www.youtube.com/watch?v=pqLvFfwcqfw"></a></li>
  <li><a href="https://www.youtube.com/watch?v=ANCAyz_U_oo"></a></li>
  <li><a href="https://www.youtube.com/watch?v=9WXsdApQIY4"></a></li>
  <li><a href="https://www.youtube.com/watch?v=Qpe1FzlJgEc"></a></li>


  <li><a href="https://www.youtube.com/watch?v=C9QwcOwhbGM"></a></li>
  <li><a href="https://www.youtube.com/watch?v=N6CXqNk5a-I"></a></li>
  <li><a href="https://www.youtube.com/watch?v=UHJrmUqxDvc"></a></li>

</ul>

<!-- Pagination navigation -->
<div id="pagination">
  <button id="prevPage">Previous</button>
  <span id="pageCounter">Page 1</span>
  <button id="nextPage">Next</button>
</div>

<script>
  // Define global variables for pagination
var currentPage = 1;
var videosPerPage = 10;

// Function to render YouTube video thumbnails based on pagination
function renderVideoList() {
  var start = (currentPage - 1) * videosPerPage;
  var end = start + videosPerPage;
  var $listContainer = $('#youtubelistContainer');
  
  // Clear previous content
  $listContainer.empty();
  
  // Loop through the videos array to append thumbnails
  for (var i = start; i < Math.min(videos.length, end); i++) {
    var video = videos[i];
    var thumbnailUrl = 'http://img.youtube.com/vi/' + video.id + '/0.jpg';
    var listItem = '<div class="videoItem">' +
                     '<div class="thumbwrap">' +
                       '<img src="' + thumbnailUrl + '" />' +
                       '<div class="panel"><div class="panelinner"><i class="play fa fa-play-circle-o"></i> <a class="externallink fa fa-external-link-square" href="' + video.url + '" target="_blank"></a></div></div>' +
                     '</div>' +
                     '<a href="' + video.url + '" target="_blank">' + video.url + '</a> <button class="deleteButton">Delete</button>' +
                   '</div>';
    $listContainer.append(listItem);
  }
  
  // Update page counter
  $('#pageCounter').text('Page ' + currentPage);
}

// Initialize the YouTube gallery and pagination
$(document).ready(function() {
  renderVideoList();

  // Pagination buttons
  $('#prevPage').click(function() {
    if (currentPage > 1) {
      currentPage--;
      renderVideoList();
    }
  });

  $('#nextPage').click(function() {
    if (currentPage < Math.ceil(videos.length / videosPerPage)) {
      currentPage++;
      renderVideoList();
    }
  });

  // Handle form submission and dynamic content (as per your existing code)
  $('#uploadForm').submit(function(event) {
    // Handle form submission logic
    // This part should remain unchanged based on your existing implementation
  });

  // Function to delete a YouTube video URL from the list
  $('#youtubelistContainer').on('click', '.deleteButton', function() {
    $(this).closest('.videoItem').remove();
    // Handle deletion logic (as per your existing code)
  });

  // Function to validate YouTube URL
  function isValidYouTubeUrl(url) {
    // This function should remain unchanged based on your existing implementation
  }

  // Function to extract YouTube video ID from URL
  function getyoutubeid(link) {
    // This function should remain unchanged based on your existing implementation
  }
});

</script>




</body>

</html>