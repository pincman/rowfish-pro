/*
 * @Author         : pincman
 * @HomePage       : https://pincman.cn
 * @Support        : support@pincman.com
 * @Created_at     : 2021-11-07 05:02:24 +0800
 * @Updated_at     : 2021-11-22 10:30:36 +0800
 * @Path           : /wp-content/themes/rowfish/assets/js/app.js
 * @Description    : 全局JS
 * Copyright 2021 pincman, All Rights Reserved.
 * 如在使用中遇到问题,可以加QQ群: 297762972交流
 */
function changeLogo(src, dark) {
  var src_arr = src.split(".");
  var current_src = $("img.logo").attr("src");
  if (current_src) {
    if (dark === "1") {
      $("img.logo").attr(
        "src",
        src_arr.slice(0, src_arr.length - 1).join(".") +
          "_dark." +
          src_arr[src_arr.length - 1]
      );
    } else {
      $("img.logo").attr("src", src);
    }
  }
}

jQuery(function () {
  ("use strict");
  //   var dlogo = $("img.logo").attr("src");
  //   var body = $("body");
  //   var siteContent = $(".site-content");
  //   if (siteContent) {
  //     var widgets = $(".site-content").children(".section");
  //     if (widgets.length > 2) {
  //       var widnoback = $("<div class='wid-noback'></div>");
  //       widnoback.appendTo(siteContent);
  //       $(widgets[1]).css("margin-bottom", "30px");
  //       $(widgets[widgets.length - 1]).css("margin-bottom", "0");
  //       for (var i = 2; i < widgets.length; i++) {
  //         widnoback.append($(widgets[i]));
  //       }
  //     }
  //   }
  // 外部链接用新窗口打开'
  $(document.links)
    .filter(function () {
      return this.hostname != window.location.hostname && !this.target;
    })
    .attr("target", "_blank");
  // 暗黑模式切换后缓存到浏览器
  $(".toggle-dark")
    .off("click")
    .on("click", function () {
      var e = $(this),
        t = e.html();
      rizhuti_v2_ajax(
        {
          action: "toggle_dark",
          dark: !0 === body.hasClass("dark-open") ? "0" : "1",
        },
        function (t) {
          e.html('<i class="fa fa-spinner fa-spin"></i> ');
        },
        function (e) {},
        function (a) {
          body.toggleClass("dark-open"), e.html(t);
          body.trigger("toggleDark");
        }
      );
    });
  if ($(".home_top_bg_image").length > 0) {
    var top_bg_height =
      document.querySelector(".home_top_bg_image").offsetHeight +
      document.querySelector(".home_top_bg_image").offsetTop +
      10;
    var top_bg_light = $(".home_top_bg_image").data("background-light");
    var top_bg_dark = $(".home_top_bg_image").data("background-dark");
    var top_bg_content = '<style type="text/css">';
    if (top_bg_light && top_bg_light.length > 0) {
      top_bg_content +=
        ".home_top_bg_image:before { height: " +
        top_bg_height +
        "px ; background-image: url(" +
        top_bg_light +
        "); }";
    }
    if (top_bg_dark && top_bg_dark.length > 0) {
      top_bg_content +=
        ".dark-open .home_top_bg_image:before { height: " +
        top_bg_height +
        "px ; background-image: url(" +
        top_bg_dark +
        "); }";
    }
    top_bg_content += "</style>";
    $(top_bg_content).appendTo($("head"));
  }
  $("body").on("toggleDark", function () {
    var dark = body.hasClass("dark-open") ? "1" : "0";
    localStorage.setItem("theme", dark);
    if (typeof particle_clear !== "undefined") {
      particle_clear();
    }
    if (typeof particle_start !== "undefined") {
      particle_start(body.hasClass("dark-open"));
    }
    // if (dlogo) changeLogo(dlogo, dark);
  });

  if (localStorage.getItem("theme") === "0") {
    body.removeClass("dark-open").trigger("toggleDark");
  } else if (
    !localStorage.getItem("theme") ||
    localStorage.getItem("theme") === "1"
  ) {
    body.removeClass("dark-open").addClass("dark-open").trigger("toggleDark");
  }
  // 为代码块添加行号
  $("pre").addClass("line-numbers").css("white-space", "pre-wrap");
  // 介绍视频打开后滚动到序言页面
  if ($(".single-course-nav-flex").length > 0) {
    var urlParams = new URLSearchParams(window.location.search);
    var chapter = urlParams.get("chapter");
    if (!chapter) {
      var vc = document.getElementById("course-container");
      if (vc) {
        vc.scrollIntoView({
          behavior: "smooth",
        });
      }
    }
    $("#goto-video").on("click", function () {
      window.scrollTo({
        top: 0,
        behavior: "smooth",
      });
    });
  }
  $toc = $(".entry-content > #toc_container");
  if ($toc.length) {
    if ($toc.length > 1) {
      $toc.slice(0, $toc.length - 1).remove();
      $toc = $(".entry-content > #toc_container");
    }
    $toc_hidden = false;
    $toc_out_width = $toc.outerWidth();
    $toc_icon_class = "fas fa-angle-double-right";
    $toc_control = $(".toc_container_control");
    if ($toc_control.length) {
      $toc_control.slice(0, $toc_control.length).remove();
    }
    $toc_control = $(
      "<div class='toc_container_control' style='right:" +
        ($toc_hidden ? 0 : $toc_out_width) +
        "px;'><i class='" +
        $toc_icon_class +
        "'></i></div>"
    );
    $toc.before($toc_control);
    $toc_toggle = function () {
      $toc_control.animate({ right: $toc_hidden ? $toc_out_width : 0 }, 350);
      $toc.animate(
        { right: $toc_hidden ? 0 : -$toc_out_width },
        350,
        function () {
          $toc_hidden = !$toc_hidden;
          $toc_control.children("i").removeClass($toc_icon_class);
          $toc_icon_class = $toc_hidden
            ? "fas fa-angle-double-left"
            : "fas fa-angle-double-right";
          $toc_control.children("i").addClass($toc_icon_class);
          $tippy.setContent($toc_hidden ? "打开文档目录" : "隐藏文档目录");
        }
      );
    };
    $toc_control.click(function () {
      $toc_toggle();
    });
    $tippy = tippy($toc_control.get(0), {
      content: $toc_hidden ? "打开文档目录" : "隐藏文档目录",
      placement: "left-start",
    });
    $toc_toggle();
    // console.log($(".toc_transparent").length);
    // $toc.remove();
  }
  $(".rowfish-widget-post-blocks .nav-tabs > li").click(function () {
    $(this).siblings().removeClass("active");
    $(this).addClass("active");
  });
  // $sc = $("body").find("[data-action='omnisearch-open']");
  // $sc.trigger("click");

  console.log(
    "\n %c Theme By Child Theme RowFish %c https://pincman.cn \n\n",
    "color: #000; background: #fffe00; padding:5px 0;",
    "background: #fadfa3; padding:5px 0;"
  );
});
