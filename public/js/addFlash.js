
const addFlash = (type, message) => {
    console.log('lol');
    let backgroundColor;
    switch (type) {
        case 'success':
            backgroundColor = 'linear-gradient(to right, #00b09b, #96c93d)';
            break;
        case 'danger':
            backgroundColor = 'linear-gradient(to right, #ff6f61, #e32c2c)';
            break;
        case 'error':
            backgroundColor = 'linear-gradient(to right, #ff6f61, #e32c2c)';
            break;
        case 'reset_password_error':
            backgroundColor = 'linear-gradient(to right, #ff6f61, #e32c2c)';
            break;
        case 'warning':
            backgroundColor = 'linear-gradient(to right, #f9c513, #b79226)';
            break;
        case 'info':
            backgroundColor = 'linear-gradient(to right, #408af4, #2c68f4)';
            break;
        default:
            backgroundColor = 'linear-gradient(to right, #ffffff, #f2f2f2)';
    }

    Toastify({
        text: message,
        duration: 3000,
        newWindow: true,
        close: true,
        escapeMarkup: false,
        gravity: 'top',
        position: 'left',
        backgroundColor: backgroundColor,
        className: type,
    }).showToast();
};