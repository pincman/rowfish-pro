window.tinymce = $("#tinymce").css("display", "none").appendTo("body");
jQuery(function () {
  $(".ap-field-type-textarea").not(".ap-field-form_comment-content").hide();
  var $mdTA = $('textarea[name="wp-content-editor-container-markdown-doc"]');
  var $answerTA = $('textarea[name="form_answer[post_content]"]');
  var $questionTA = $('textarea[name="form_question[post_content]"]');
  if ($answerTA && $answerTA.length) {
    $mdTA.text($answerTA.val());
    $("#wp-content-editor-container").on("keyup", function () {
      $answerTA.text($mdTA.val());
    });
  }
  if ($questionTA && $questionTA.length) {
    $mdTA.text($questionTA.val());
    $("#wp-content-editor-container").on("keyup", function () {
      $questionTA.text($mdTA.val());
    });
  }
  //   $("#form_answer").on("submit", function (e) {
  //     $answerTA.text($mdTA.val());
  //   });
  $(".ap-btn-submit")
    .addClass("btn")
    .addClass("btn-outline-primary")
    .addClass("btn-sm");
  var answers = $(".answer");
  if (answers.length > 0) {
    $("html, body").stop();
    $("html, body").animate(
      {
        scrollTop: answers.first().offset().top - 80,
      },
      1000
    );
  }
  $("body")
    .off("submit", "[apform]")
    .on("submit", "[apform]", function (e) {
      e.preventDefault();
      $answerTA.text($mdTA.val());
      var self = $(this);
      var submitBtn = $(this).find('button[type="submit"]');

      if (submitBtn.length > 0) AnsPress.showLoading(submitBtn);
      $(this).ajaxSubmit({
        url: ajaxurl,
        beforeSerialize: function () {
          //   if (typeof tinymce !== "undefined") tinymce.triggerSave();

          $(".ap-form-errors, .ap-field-errors").remove();
          $(".ap-have-errors").removeClass("ap-have-errors");
        },
        success: function (data) {
          if (submitBtn.length > 0) AnsPress.hideLoading(submitBtn);

          data = AnsPress.ajaxResponse(data);
          if (data.snackbar) {
            AnsPress.trigger("snackbar", data);
          }

          if (
            typeof grecaptcha !== "undefined" &&
            typeof widgetId1 !== "undefined"
          )
            grecaptcha.reset(widgetId1);

          AnsPress.trigger("formPosted", data);

          if (typeof data.form_errors !== "undefined") {
            $formError = $('<div class="ap-form-errors"></div>').prependTo(
              self
            );

            $.each(data.form_errors, function (i, err) {
              $formError.append(
                '<span class="ap-form-error ecode-' + i + '">' + err + "</div>"
              );
            });

            $.each(data.fields_errors, function (i, errs) {
              $(".ap-field-" + i).addClass("ap-have-errors");
              $(".ap-field-" + i)
                .find(".ap-field-errorsc")
                .html('<div class="ap-field-errors"></div>');

              $.each(errs.error, function (code, err) {
                $(".ap-field-" + i)
                  .find(".ap-field-errors")
                  .append(
                    '<span class="ap-field-error ecode-' +
                      code +
                      '">' +
                      err +
                      "</span>"
                  );
              });
            });

            self.apScrollTo();
          } else if (typeof data.hide_modal !== undefined) {
            // Hide modal
            AnsPress.hideModal(data.hide_modal);
          }

          if (typeof data.redirect !== "undefined") {
            window.location = data.redirect;
          } else {
            if (e.target.id === "form_answer") {
              window.location.reload();
            }
          }
        },
      });
    });

  $("[ap-loadmore]").off("click");

  $("body").on("click", "[ap-loadmore]", function (e) {
    e.preventDefault();
    var self = this;
    var args = JSON.parse($(this).attr("ap-loadmore"));
    args.action = "ap_ajax";

    if (typeof args.ap_ajax_action === "undefined")
      args.ap_ajax_action = "bp_loadmore";

    AnsPress.showLoading(this);
    AnsPress.ajax({
      data: args,
      success: function (data) {
        AnsPress.hideLoading(self);
        console.log(data.element);
        if (data.success) {
          $(data.element).append(data.html);
          $(self).attr("ap-loadmore", JSON.stringify(data.args));
          if (!data.args.current) {
            $(self).hide();
          }
        }
      },
    });
  });
});
