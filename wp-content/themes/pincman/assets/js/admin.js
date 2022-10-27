window.addEventListener("load", () => {
  if (["post.php", "post-new.php"].indexOf(pincman_wpdocs_editor.hook) > -1) {
    document.getElementById("_prefix_wppay_options").remove();
  }
});
