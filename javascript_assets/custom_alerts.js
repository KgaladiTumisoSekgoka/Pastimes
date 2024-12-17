const customAlert = {
    alert: function(message) {
        const alertBox = document.createElement('div');
        alertBox.className = 'custom-alert';
        alertBox.innerText = message;
        document.body.appendChild(alertBox);
        
        setTimeout(() => {
            document.body.removeChild(alertBox);
        }, 3000); // Auto-remove after 3 seconds
    }
};

// Styles for custom alert (can also be in your CSS file)
const style = document.createElement('style');
style.innerHTML = `
.custom-alert {
    position: fixed;
    top: 20px;
    left: 50%;
    transform: translateX(-50%);
    background-color: #f44336;
    color: white;
    padding: 15px;
    border-radius: 5px;
    z-index: 1000;
    animation: fadeIn 0.5s, fadeOut 0.5s 2.5s;
}
@keyframes fadeIn {
    from {opacity: 0;}
    to {opacity: 1;}
}
@keyframes fadeOut {
    from {opacity: 1;}
    to {opacity: 0;}
}
`;
document.head.appendChild(style);
