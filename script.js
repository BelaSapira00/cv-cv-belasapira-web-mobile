$(document).ready(function () {

  function loadPage(page) {
    $("#content").hide().load(page, function (response, status) {

      if (status === "error") {
        $("#content").html("<h3>Halaman tidak ditemukan 😢</h3>");
      } else {
        $("#content").fadeIn(200);
      }

    });
  }

  // 🔥 load awal
  loadPage("home.html");

  // 🔥 FIX EVENT (INI YANG PENTING BANGET)
  $(document).on("click", ".menu", function (e) {
    e.preventDefault();

    $(".menu").removeClass("active");
    $(this).addClass("active");

    let page = $(this).data("page");
    loadPage(page);

    $("#navMenu").removeClass("show");
  });

  // 🔥 hamburger
  $(document).on("click", ".menu-toggle", function () {
    $("#navMenu").toggleClass("show");
  });

});
