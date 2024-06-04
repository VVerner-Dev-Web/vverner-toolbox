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

jQuery("#wp_debug, #wp_debug_log, #wp_debug_display").on("change", function () {
  const debugEnabled = parseInt(jQuery("#wp_debug").val()) === 1;
  const thisEnabled = parseInt(jQuery(this).val()) === 1;
  const changingDebug = jQuery(this).is("#wp_debug");

  if (thisEnabled) {
    jQuery("#wp_debug").val(1);
  }

  if (!debugEnabled && changingDebug) {
    jQuery("#wp_debug_log, #wp_debug_display").val(0);
  }
});

jQuery("#clear-logs").on("click", function () {
  jQuery.post("/", { vjax: "vverner/admin/clear-debug" }, function (data) {
    jQuery("#vverner-debug-logs").empty();
    alert("Logs limpos com sucesso!");
  });
});

if (jQuery("#vverner-debug-logs").length) {
  const heartbeat = 10000;
  const progressHeartbeat = 1000;
  let currentInterval = 1;

  setInterval(() => {
    jQuery.post("/", { vjax: "vverner/admin/get-debug-logs" }, function (data) {
      jQuery("#vverner-debug-logs").html(data);
      jQuery("#debug-timeout").css("width", 0 + "%");
      currentInterval = 1;
    });
  }, heartbeat);

  setInterval(() => {
    if (currentInterval > heartbeat / progressHeartbeat) {
      currentInterval = 1;
    }

    jQuery("#debug-timeout").css("width", currentInterval * 10 + "%");

    currentInterval++;
  }, progressHeartbeat);
}
