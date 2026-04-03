$(document).ready(function () {

  function loadPage(page) {
    console.log("Load:", page);

    $("#content").hide().load("./" + page, function (response, status) {

      if (status === "error") {
        $("#content").html("<h3 style='color:red'>File tidak ditemukan 😢</h3>");
      } else {
        $("#content").fadeIn(200);
      }

    });
  }

  // 🔥 load awal
  loadPage("home.html");

  // 🔥 klik menu (ANTI ERROR SPA)
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
