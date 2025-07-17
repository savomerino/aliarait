function isMobile() {
  return window.innerWidth < 768 || /Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
}

function setVideoSource() {
  const video = document.getElementById('aliaraVideo');
  const source = document.getElementById('videoSource');
  let newSrc;

  if (isMobile()) {
    newSrc = "assets/aliaraIT_back_Web_V.mp4";
  } else {
    newSrc = "assets/aliaraIT_back_Web.mp4"; // Cambiado para escritorio
  }

  if (!source.src.endsWith(newSrc)) {
    source.src = newSrc;
    video.load();
  }
}

setVideoSource();
window.addEventListener('resize', setVideoSource);

// Bootstrap validation
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()

document.addEventListener('DOMContentLoaded', function() {
    // ...código de la animación del logo...

    // WhatsApp button logic
    const whatsappBtn = document.getElementById('whatsappBtn');
    if (whatsappBtn) {
        whatsappBtn.addEventListener('click', function() {
            const name = document.getElementById('name').value.trim();
            const message = document.getElementById('message').value.trim();
            if (!name) {
                alert('Por favor, ingresa tu nombre antes de enviar por WhatsApp.');
                document.getElementById('name').focus();
                return;
            }
            const text = encodeURIComponent(`Nombre: ${name}\nMensaje: ${message}`);
            const phone = '5493624122191';
            window.open(`https://wa.me/${phone}?text=${text}`, '_blank');
        });
    }

    // Formspree logic: solo un envío, solo un mensaje de éxito
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        // Elimina listeners duplicados si existen
        contactForm.addEventListener('submit', handleFormSubmit, { once: true });
    }

    function handleFormSubmit(event) {
        event.preventDefault();

        // Validar que los tres campos estén completos
        const name = document.getElementById('name').value.trim();
        const email = document.getElementById('email').value.trim();
        const message = document.getElementById('message').value.trim();
        if (!name || !email || !message) {
            alert('Por favor, completa todos los campos antes de enviar el mensaje.');
            return;
        }

        const formData = new FormData(contactForm);
        const submitButton = contactForm.querySelector('button[type="submit"]');
        const originalButtonText = submitButton.textContent;
        submitButton.disabled = true;
        submitButton.textContent = 'Enviando...';

        fetch('https://formspree.io/f/mblyoqyl', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (response.ok) {
                contactForm.reset();
                alert('¡Mensaje enviado con éxito!');
            } else {
                response.json().then(data => {
                    if (Object.hasOwn(data, 'errors')) {
                        alert(data["errors"].map(error => error["message"]).join(", "));
                    } else {
                        alert('Oops! Hubo un problema al enviar tu mensaje. Por favor, inténtalo de nuevo.');
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error al enviar el formulario:', error);
            alert('Oops! Hubo un problema de red al enviar tu mensaje. Por favor, revisa tu conexión e inténtalo de nuevo.');
        })
        .finally(() => {
            submitButton.disabled = false;
            submitButton.textContent = originalButtonText;
            // Permite un nuevo envío solo después de terminar el anterior
            contactForm.addEventListener('submit', handleFormSubmit, { once: true });
        });
    }
});