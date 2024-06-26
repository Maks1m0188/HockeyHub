(function ($) {
  $(".contact-form").submit(function (event) {
    event.preventDefault();

    // Сообщения формы
    let successSendText = "Сообщение успешно отправлено";
    let errorSendText = "Сообщение не отправлено. Попробуйте еще раз!";
    let requiredFieldsText = "Заполните все поля с * !";
    let popup = document.querySelector('.popup');
    let formWrap = document.querySelector('.form-wrapper');

    // Сохраняем в переменную класс с параграфом для вывода сообщений об отправке
    let message = $(this).find(".contact-form__message");

    let form = $("#" + $(this).attr("id"))[0];
    let fd = new FormData(form);
    $.ajax({
      url: "/telegramform/php/send-message-to-telegram.php",
      type: "POST",
      data: fd,
      processData: false,
      contentType: false,
      beforeSend: () => {
        $(".preloader").addClass("preloader_active");
      },
      success: function success(res) {
        $(".preloader").removeClass("preloader_active");

        // Посмотреть на статус ответа, если ошибка
        // console.log(res);
        let respond = $.parseJSON(res);

        if (respond === "SUCCESS") {
          // message.text(successSendText).css("color", "#21d4bb");
          window.scrollTo({
            top: 0,
            left: 0,
            behavior: 'smooth'
          });
          setTimeout(() => {
            message.text("");
          }, 4000);
          popup.classList.add('popup_active');
          formWrap.classList.add('form-wrapper-active');

        } else if (respond === "NOTVALID") {
          message.text(requiredFieldsText).css("color", "#d42121");
          window.scrollTo({
            top: 0,
            left: 0,
            behavior: 'smooth'
          });
          setTimeout(() => {
            message.text("");
          }, 4000);
          
        } else {
          message.text(errorSendText).css("color", "#d42121");
          window.scrollTo({
            top: 0,
            left: 0,
            behavior: 'smooth'
          });
          setTimeout(() => {
            message.text("");
          }, 4000);
        }
      }
    });
  });
})(jQuery);
