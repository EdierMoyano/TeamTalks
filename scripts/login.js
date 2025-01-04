const form = document.getElementById('formulario');
    const inputs = document.querySelectorAll('#formulario input');
    const docuError = document.getElementById('docu_error');
    const documentInput = document.getElementById('documentId');

    // Expresión regular para validar el documento (8-10 dígitos)
    const expresion = {
        documento: /^[0-9]{8,10}$/
    };

    // Validar los campos del formulario
    const validar = (e) => {
        switch (e.target.name) {
            case "document":
                if (expresion.documento.test(e.target.value)) {
                    documentInput.classList.remove('form_incorrecto');
                    documentInput.classList.add('form_correcto');
                    docuError.style.display = 'none'; // Esconde el mensaje de error
                } else {
                    documentInput.classList.add('form_incorrecto');
                    documentInput.classList.remove('form_correcto');
                    docuError.style.display = 'block'; // Muestra el mensaje de error
                }
                break;

            case "password":
                // Puedes añadir validación para la contraseña si es necesario
                break;
        }
    };

    // Eventos para validar al escribir o salir del input
    inputs.forEach((input) => {
        input.addEventListener('keyup', validar);
        input.addEventListener('blur', validar);
    });

    // Validar al enviar el formulario
    form.addEventListener('submit', (e) => {
        // Validar el documento antes de permitir el envío
        if (!expresion.documento.test(documentInput.value)) {
            e.preventDefault();
            docuError.style.display = 'block';
            documentInput.classList.add('form_incorrecto');
            alert('Por favor, corrige los errores en el formulario.');
        }
    });