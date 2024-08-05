//Form handler
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const alertContainer = document.createElement('div');
    alertContainer.style.display = 'none';
    form.parentNode.insertBefore(alertContainer, form.nextSibling);

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch('php-form-processor.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('HTTP status ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            showAlert(data.success, data.message);
        })
        .catch(error => {
            showAlert(false, 'Error al enviar el formulario: ' + error.message);
        });
    });

    function showAlert(success, message) {
        const alertClass = success ? 'alert-success' : 'alert-danger';
        alertContainer.className = `alert ${alertClass} alert-dismissible fade show`;
        alertContainer.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        alertContainer.style.display = 'block';

        // Opcional: desplazarse hasta el mensaje
        alertContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });

        // Opcional: limpiar el formulario si el envío fue exitoso
        if (success) {
            form.reset();
        }
    }
});