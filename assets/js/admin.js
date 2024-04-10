jQuery("#vverner-tabs .tab > a").on("click", function (e) {
  e.preventDefault();

  const $btn = jQuery(this);

  jQuery(".tab-content, .tab a").removeClass("active");

  $btn.addClass("active");
  jQuery($btn.attr("href")).addClass("active");
});

jQuery(".vverner-configuration-form").on("submit", function (e) {
  e.preventDefault();

  const $btn = jQuery(this).find("button");
  const text = $btn.text();

  $btn.text("Aguarde...").addClass("disabled");

  jQuery.post("/", jQuery(this).serialize(), function (data) {
    $btn.text(text).removeClass("disabled");

    alert(data.message ?? "Configuração salva com sucesso!");
  });
});
