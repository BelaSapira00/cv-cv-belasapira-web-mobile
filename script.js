$(document).ready(function () {

  function loadPage(page) {
    $("#content").fadeOut(200, function () {
      $("#content").load(page, function () {
        $("#content").fadeIn(200);
      });
    });
  }

  // 🔥 load pertama (home)
  loadPage("home.html");

  // 🔥 klik menu
  $(".menu").click(function () {
    $(".menu").removeClass("active");
    $(this).addClass("active");

    let page = $(this).data("page");
    loadPage(page);

    // 🔥 auto close menu di HP
    $("#navMenu").removeClass("show");
  });

  // 🔥 hamburger toggle
  $(".menu-toggle").click(function () {
    $("#navMenu").toggleClass("show");
  });

});
