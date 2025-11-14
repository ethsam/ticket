document.addEventListener('DOMContentLoaded', function () {
    const checkbox = document.querySelector('[name="ActionColum[mailForSender]"]');
    const emailField = document.querySelector('[name="ActionColum[emailReceipt]"]');

    if (!checkbox || !emailField) return;

    checkbox.addEventListener('change', () => {
        if (checkbox.checked && emailField.value.trim() === '') {
            emailField.value = 'DEMANDEUR';
        }
    });
});
