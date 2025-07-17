// Validación Bootstrap y envío AJAX a Formspree + WhatsApp

// Bootstrap validation
(function () {
  'use strict';
  var forms = document.querySelectorAll('.needs-validation');
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();

document.addEventListener('DOMContentLoaded', function () {
  // WhatsApp button logic
  var whatsappBtn = document.getElementById('whatsappBtn');
  if (whatsappBtn) {
    whatsappBtn.addEventListener('click', function () {
      var name = document.getElementById('name').value.trim();
      var message = document.getElementById('message').value.trim();
      if (!name) {
        alert('Por favor, ingresa tu nombre antes de enviar por WhatsApp.');
        document.getElementById('name').focus();
        return;
      }
      var text = encodeURIComponent('Nombre: ' + name + '\nMensaje: ' + message);
      var phone = '5493624122191';
      window.open('https://wa.me/' + phone + '?text=' + text, '_blank');
    });
  }

  // Formspree AJAX logic
  var contactForm = document.getElementById('contactForm');
  if (contactForm) {
    contactForm.addEventListener('submit', function (event) {
      event.preventDefault();

      // Validación manual extra
      var name = document.getElementById('name').value.trim();
      var email = document.getElementById('email').value.trim();
      var message = document.getElementById('message').value.trim();
      if (!name || !email || !message) {
        alert('Por favor, completa todos los campos antes de enviar el mensaje.');
        return;
      }

      var formData = new FormData(contactForm);
      var submitButton = contactForm.querySelector('button[type="submit"]');
      var originalButtonText = submitButton.textContent;
      submitButton.disabled = true;
      submitButton.textContent = 'Enviando...';

      fetch('https://formspree.io/f/mblyoqyl', {
        method: 'POST',
        body: formData,
        headers: { 'Accept': 'application/json' }
      })
        .then(function (response) {
          if (response.ok) {
            contactForm.reset();
            alert('¡Mensaje enviado con éxito!');
          } else {
            response.json().then(function (data) {
              if (Object.hasOwn(data, 'errors')) {
                alert(data["errors"].map(function (error) { return error["message"]; }).join(", "));
              } else {
                alert('Oops! Hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo.');
              }
            });
          }
        })
        .catch(function (error) {
          console.error('Error al enviar el formulario:', error);
          alert('Oops! Hubo un problema de red al enviar tu mensaje. Por favor, revisa tu conexión e inténtalo de nuevo.');
        })
        .finally(function () {
          submitButton.disabled = false;
          submitButton.textContent = originalButtonText;
        });
    }, false);
  }
});