jQuery(function ($) {
  $("#vverner-tabs .tab > a").on("click", function (e) {
    e.preventDefault();

    const $btn = $(this);

    $(".tab-content, .tab a").removeClass("active");

    $btn.addClass("active");
    $($btn.attr("href")).addClass("active");
  });

  $(".vverner-configuration-form").on("submit", function (e) {
    e.preventDefault();

    const $btn = $(this).find("button");
    const text = $btn.text();

    $btn.text("Aguarde...").addClass("disabled");

    $.post("/", $(this).serialize(), function (data) {
      $btn.text(text).removeClass("disabled");

      alert(data.message ?? "Configuração salva com sucesso!");
    });
  });
});
