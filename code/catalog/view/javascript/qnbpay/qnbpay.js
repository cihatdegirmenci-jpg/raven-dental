$("#payment_qnbpay_card").on("change", function () {
  selectedText = $(this).find("option:selected").text();
  selectedVal = $(this).find("option:selected").val();
  if (selectedVal == "0") {
    $('.cardForm input[type="text"]').prop("required", true);
    $(".cardForm").show();
    $("#deleteMyCard").hide();
  } else {
    $(".cardForm").hide();
    $('.cardForm input[type="text"]').prop("required", false);
    getInstallmentsByCard(selectedText);
    $("#deleteMyCard").show();
    let deletehref = $("#deleteMyCard").data("url");
    deletehref = deletehref + "&card_token=" + selectedVal;
    $("#deleteMyCard").attr("href", deletehref);
  }
});

$("#payment_qnbpay_taksit").on("change", function () {
  $("#payment_qnbpay_total").val($(this).find(":selected").data("total"));
});

function getInstallment(val) {
  var len = val.value.length;
  if (len == 7 || len == 19) {
    getInstallmentsByCard(val.value);
  }
}

function getInstallmentsByCard(cardN) {
  console.log(cardN);
  $.ajax("index.php?route=extension/payment/qnbpay/ajax&getInstallments=1", {
    type: "POST",
    data: { card: cardN },
    dataType: "json",
    success: function (data, status, xhr) {
      // Response kontrolü
      if (!data || typeof data !== 'object') {
        console.error('QNB Pay: Geçersiz JSON yanıtı', data);
        return;
      }
      
      // Hata kontrolü
      if (data.status === 'error') {
        console.error('QNB Pay: Taksit hatası', data.message);
        // Hata durumunda sadece peşin seçeneğini göster
        $("#payment_qnbpay_taksit option").remove();
        $("#payment_qnbpay_taksit").append(
          $("<option></option>")
            .attr("value", "1")
            .text("Peşin")
            .attr("data-total", $("#payment_qnbpay_total").val())
        );
        return;
      }
      
      // Başarılı yanıt
      if (data.status === 'success' && data.taksitler) {
        $("#payment_qnbpay_taksit option").remove();
        $.each(data.taksitler, function (key, value) {
          if (key == 1) $("#payment_qnbpay_total").val(value.toplam);

          $("#payment_qnbpay_taksit").append(
            $("<option></option>")
              .attr("value", key)
              .text(value.text)
              .attr("data-total", value.toplam)
          );
        });
      }
    },
    error: function (jqXhr, textStatus, errorMessage) {
      console.error('QNB Pay AJAX Hatası:', textStatus, errorMessage);
      console.error('Response:', jqXhr.responseText);
      
      // JSON parse hatası kontrolü
      if (textStatus === 'parsererror') {
        console.error('QNB Pay: JSON parse hatası. Response:', jqXhr.responseText.substring(0, 200));
        // Hata durumunda sadece peşin seçeneğini göster
        $("#payment_qnbpay_taksit option").remove();
        $("#payment_qnbpay_taksit").append(
          $("<option></option>")
            .attr("value", "1")
            .text("Peşin")
            .attr("data-total", $("#payment_qnbpay_total").val())
        );
      }
    },
  });
}
