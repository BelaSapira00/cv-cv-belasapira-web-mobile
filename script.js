$(document).ready(function () {

  function loadPage(page) {
    $("#content").fadeOut(200, function () {
      $("#content").load(page, function (response, status) {

        if (status === "error") {
          $("#content").html("<h3 style='color:red'>Halaman tidak ditemukan 😢</h3>");
        } else {
          $("#content").fadeIn(200);
        }

      });
    });
  }

  // 🔥 LOAD HOME AWAL
  loadPage("home.html");

  // 🔥 FIX EVENT MENU (biar selalu bisa diklik)
  $(document).on("click", ".menu", function (e) {
    e.preventDefault();

    $(".menu").removeClass("active");
    $(this).addClass("active");

    let page = $(this).data("page");
    loadPage(page);

    // 🔥 tutup menu kalau di HP
    $("#navMenu").removeClass("show");
  });

  // 🔥 HAMBURGER (INI YANG KAMU BUTUH)
  $(document).on("click", ".menu-toggle", function () {
    $("#navMenu").toggleClass("show");
  });

});
