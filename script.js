$(document).ready(function () {

  function loadPage(page) {
    $("#content").fadeOut(200, function () {
      $("#content").load(page, function () {
        $("#content").fadeIn(200);
      });
    });
  }

  loadPage("home.html");

  $(".menu").click(function () {
    $(".menu").removeClass("active");
    $(this).addClass("active");

    let page = $(this).data("page");
    loadPage(page);
  });

});