function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const svg = button.querySelector('svg');
    const paths = svg.querySelectorAll('path');

    if (input.type === 'password') {
        input.type = 'text';
        paths[1].setAttribute('d', 'M3 3l18 18 M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'); // Change to eye-off symbol
    } else {
        input.type = 'password';
        paths[1].setAttribute('d', 'M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z'); // Reset to eye
    }
}
