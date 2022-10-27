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
  var dlogo = $("img.logo").attr("src");
  var body = $("body");
  var siteContent = $(".site-content");
  if (siteContent) {
    var widgets = $(".site-content").children(".section");
    if (widgets.length > 2) {
      var widnoback = $("<div class='wid-noback'></div>");
      widnoback.appendTo(siteContent);
      $(widgets[1]).css("margin-bottom", "30px");
      $(widgets[widgets.length - 1]).css("margin-bottom", "0");
      for (var i = 2; i < widgets.length; i++) {
        widnoback.append($(widgets[i]));
      }
    }
  }
  $(document.links)
    .filter(function () {
      return (
        ((!this.href.includes("http") && !this.href.includes("https")) ||
          this.hostname != window.location.hostname) &&
        !this.target
      );
    })
    .attr("target", "_blank");
  jQuery(".toggle-dark")
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
  $("body").on("toggleDark", function () {
    var dark = body.hasClass("dark-open") ? "1" : "0";
    localStorage.setItem("theme", dark);
    if (dlogo) changeLogo(dlogo, dark);
  });

  if (!localStorage.getItem("theme") || localStorage.getItem("theme") === "0") {
    body.removeClass("dark-open").trigger("toggleDark");
  } else if (localStorage.getItem("theme") === "1") {
    body.removeClass("dark-open").addClass("dark-open").trigger("toggleDark");
  }
  $(".single-download-nav .nav-link,.short-code-tabs .nav-link")
    .off("click")
    .on("click", function (event) {
      event.preventDefault();
      $(this).tab("show");
    });
  $("pre").addClass("line-numbers").css("white-space", "pre-wrap");
  $(document).off("click", ".go-inst-question-comment");
  $(document).off("click", ".go-inst-question-new");
  $(".go-inst-question-comment").on("click", function (t) {
    t.preventDefault();
    var i = $(this),
      n = {},
      s = $(".new-question-form").serializeArray();
    $.each(s, function () {
      n[this.name] = this.value;
    });
    var a = i.children("i").attr("class");
    rizhuti_v2_ajax(
      { action: "add_question_comment", cid: 0, pid: n.pid, text: n.comment },
      function (t) {
        i.children("i").attr("class", "fa fa-spinner fa-spin");
      },
      function (t) {
        1 == t.status
          ? rizhuti_v2_toast_msg("success", "感谢您的回答", function () {
              location.reload();
            })
          : rizhuti_v2_toast_msg("info", t.msg);
      },
      function (t) {
        i.children("i").attr("class", a);
      }
    );
  });
  $(".go-inst-question-new").on("click", function (t) {
    t.preventDefault();
    var i = $(this),
      n = {},
      s = $(".new-question-form").serializeArray();
    $.each(s, function () {
      n[this.name] = this.value;
    });
    var a = i.children("i").attr("class");
    rizhuti_v2_ajax(
      {
        action: "add_question_new",
        text: n.content || n["comment"],
        title: n.title,
      },
      function (t) {
        i.children("i").attr("class", "fa fa-spinner fa-spin");
      },
      function (t) {
        1 == t.status
          ? rizhuti_v2_toast_msg("success", t.msg, function () {
              location.href = "/question";
            })
          : rizhuti_v2_toast_msg("info", t.msg);
      },
      function (t) {
        i.children("i").attr("class", a);
      }
    );
  });

  // 视频教程打开后狭义到序言页面
  if ($(".single-download-nav").length > 0) {
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

  var $downpanel = $(".rizhuti_v2-widget-shop-down").children(
    ".price-widget-body"
  );
  if ($downpanel.height() > 200) {
    // $downpanel.addClass("price-widget-body-collpase");
    $downpanel.data("collapse", true);
    $collapseCss = {
      width: "100%",
      "font-size": "12px",
      "text-align": "center",
      "padding-top": "25px",
      "font-weight": 500,
      color: "rgba(255, 91, 91,0.8)",
      opacity: 0.8,
      cursor: "pointer",
    };
    $collapse = $("<div>展开列表</div>")
      .css($collapseCss)
      .insertAfter($downpanel);
    $collapse
      .on("mouseover", function () {
        $(".dark-open").length
          ? $(this).css("color", "#fff")
          : $(this).css("color", "#000");
      })
      .on("mouseleave", function () {
        $(this).css("color", "rgba(255, 91, 91,0.8)");
      });
    $collapse.on("click", function () {
      var is_collapse = $downpanel.data("collapse");
      if (is_collapse) {
        $downpanel
          .css("overflow-y", "hidden")
          .css("max-height", "1900px")
          .data("collapse", false);
        $(this).text("收起列表").css("padding-top", "8px");
      } else {
        $downpanel
          .css({ "overflow-y": "auto", "max-height": "250px" })
          .data("collapse", true);
        $(this).text("展开列表");
        $_this = $(this);
        setTimeout(function () {
          $_this.css("padding-top", "25px");
        }, 300);
      }
    });
  }
});
