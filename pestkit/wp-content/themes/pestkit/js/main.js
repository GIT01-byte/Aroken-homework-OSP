(function ($) {
  "use strict";

  // Spinner
  var spinner = function () {
    setTimeout(function () {
      if ($("#spinner").length > 0) {
        $("#spinner").removeClass("show");
      }
    }, 1);
  };
  spinner(0);

  // Initiate the wowjs
  new WOW().init();

  // Back to top button
  $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
      $(".back-to-top").fadeIn("slow");
    } else {
      $(".back-to-top").fadeOut("slow");
    }
  });
  $(".back-to-top").click(function () {
    $("html, body").animate({ scrollTop: 0 }, 1500, "easeInOutExpo");
    return false;
  });

  // Blog carousel
  $(".blog-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 1000,
    center: false,
    dots: false,
    loop: true,
    margin: 50,
    nav: true,
    navText: [
      '<i class="bi bi-arrow-left"></i>',
      '<i class="bi bi-arrow-right"></i>',
    ],
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
      },
      768: {
        items: 2,
      },
      992: {
        items: 2,
      },
      1200: {
        items: 3,
      },
    },
  });

  // Testimonial carousel
  $(".testimonial-carousel").owlCarousel({
    autoplay: true,
    smartSpeed: 1500,
    center: true,
    dots: true,
    loop: true,
    margin: 50,
    nav: true,
    navText: [
      '<i class="bi bi-arrow-left"></i>',
      '<i class="bi bi-arrow-right"></i>',
    ],
    responsiveClass: true,
    responsive: {
      0: {
        items: 1,
      },
      576: {
        items: 1,
      },
      768: {
        items: 2,
      },
      992: {
        items: 2,
      },
      1200: {
        items: 3,
      },
    },
  });
})(jQuery);
document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("custom-ajax-feedback-form");
  const popup = document.getElementById("feedback-global-popup");
  const closeBtn = document.getElementById("close-feedback-popup");

  // Элементы внутри попапа для динамической подмены контента
  const popupIcon = document.getElementById("popup-icon");
  const popupTitle = document.getElementById("popup-title");
  const popupMessage = document.getElementById("popup-message");

  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      // 1. Сброс ошибок перед валидацией
      document.querySelectorAll(".error-text-under").forEach((el) => {
        el.style.display = "none";
        el.innerText = "";
      });
      document
        .querySelectorAll(".form-control")
        .forEach((el) => el.classList.remove("is-invalid-field"));

      let hasErrors = false;

      // 2. Валидация Name
      const authorInput = form.querySelector('input[name="author"]');
      if (!authorInput.value.trim()) {
        const errBox = document.getElementById("error-author");
        errBox.innerText = "The field is required.";
        errBox.style.display = "block";
        authorInput.classList.add("is-invalid-field");
        hasErrors = true;
      }

      // 3. Валидация Email
      const emailInput = form.querySelector('input[name="email"]');
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailInput.value.trim()) {
        const errBox = document.getElementById("error-email");
        errBox.innerText = "The field is required.";
        errBox.style.display = "block";
        emailInput.classList.add("is-invalid-field");
        hasErrors = true;
      } else if (!emailRegex.test(emailInput.value.trim())) {
        const errBox = document.getElementById("error-email");
        errBox.innerText = "The e-mail address entered is invalid.";
        errBox.style.display = "block";
        emailInput.classList.add("is-invalid-field");
        hasErrors = true;
      }

      // 4. Валидация Rating
      const ratingSelected = form.querySelector('input[name="rating"]:checked');
      if (!ratingSelected) {
        const errBox = document.getElementById("error-rating");
        errBox.innerText = "Please select a rating star.";
        errBox.style.display = "block";
        hasErrors = true;
      }

      if (hasErrors) return;

      // 5. Отправка AJAX
      const formData = new FormData(form);

      fetch("/wp-admin/admin-ajax.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => {
          if (!response.ok) throw new Error("Server error");
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            form.reset(); // Очищаем поля формы

            // Настраиваем попап на УСПЕХ
            popupIcon.className =
              "fa fa-check-circle display-4 text-primary mb-3";
            popupTitle.innerText = "Thank you for your feedback!";
            popupMessage.innerText =
              "Your opinion is very important to us. It will appear on the website after moderation.";

            // Навешиваем класс для вывода на весь экран
            popup.classList.add("feedback-global-overlay");
          } else {
            // Настраиваем попап на ОБЩУЮ ОШИБКУ (включая дубликат Email из PHP)
            popupIcon.className =
              "fa fa-exclamation-circle display-4 text-danger mb-3";
            popupTitle.innerText = "Oops! Something went wrong";
            popupMessage.innerText =
              data.data.message ||
              "An error occurred while submitting your feedback.";

            // Выводим попап с ошибкой
            popup.classList.add("feedback-global-overlay");
          }
        })
        .catch((error) => {
          console.error("Error:", error);

          // Настраиваем попап на СБОЙ СЕТИ
          popupIcon.className = "fa fa-times-circle display-4 text-danger mb-3";
          popupTitle.innerText = "Submission Failed";
          popupMessage.innerText =
            "Network error or server unavailable. Please try again later.";
          popup.classList.add("feedback-global-overlay");
        });
    });
  }

  if (closeBtn && popup) {
    closeBtn.addEventListener("click", function () {
      popup.classList.remove("feedback-global-overlay");
    });
  }
});
