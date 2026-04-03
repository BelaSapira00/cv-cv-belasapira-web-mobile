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

  // 🔥 pertama buka langsung home
  loadPage("home.html");

  // 🔥 klik menu
  $(document).on("click", ".menu", function (e) {
    e.preventDefault();

    $(".menu").removeClass("active");
    $(this).addClass("active");

    let page = $(this).data("page");
    loadPage(page);

    // 🔥 tutup menu HP
    $("#navMenu").removeClass("show");
  });

  // 🔥 hamburger
  $(".menu-toggle").click(function () {
    $("#navMenu").toggleClass("show");
  });

});
