// 备用的js
// 自动点击切换评论框(解决makrdown评论被隐藏后再显示无效的问题)
// if ($(".single-download-nav").length > 0) {
//   var pillinit = false;
//   $("#pills-comments").tab("show");
//   var urlParams = new URLSearchParams(window.location.search);
//   var chapter = urlParams.get("chapter");
//   setTimeout(function () {
//     // $("#pills-details").tab("show");
//     $("#pills-comments-tab").trigger("click");
//     $('a[data-toggle="pill"]').on("shown.bs.tab", function (event) {
//       if (!pillinit) {
//         $("#pills-details-tab").trigger("click");
//         pillinit = true;
//       } else {
//         if (!chapter) {
//           scrollToVideoContainer();
//         }
//       }
//     });
//   }, 500);
//   $(".dwqa-btn").on("click", function () {
//     e.preventDefault();
//     console.log($("#wp-content-editor-container").serializeArray());
//   });
// }
// $(".ap-minimal-placeholder").remove();
// else {
//   if (!localStorage.getItem("theme")) localStorage.setItem("theme", "1");
//   changeLogo(dlogo, "0");
// }
