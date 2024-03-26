import { Notyf } from 'notyf';
import '../css/notyf.min.css';

// Création d'une instance de Notyf
const notyf = new Notyf(
    {
        duration: 5000,
        position: {
            x: 'right',
            y: 'top',
        },
        types: [
            {
                type: 'success',
                background: '#00d25b',
                icon: false,
            },
            {
                type: 'error',
                background: '#ff4d4f',
                icon: false
            },
            {
                type: 'info',
                background: '#00a9ff',
                icon: false
            },
            {
                type: 'warning',
                background: '#ffaf00',
                icon: false,
            },
        ],
    }
);

let messages = document.querySelectorAll('#message-notyf');

messages.forEach(message => {
    if (message.className === 'success') {
        notyf.success(message.innerHTML);
    }

    if (message.className === 'danger') {
        notyf.error(message.innerHTML);
    }

    if (message.className === 'info') {
        notyf.open({
            type: 'info',
            message: message.innerHTML,
            icon: false,
        });
    }

    if (message.className === 'warning') {
        notyf.open({
            type: 'warning',
            message: message.innerHTML,
            icon: false,
        });
    }
});
