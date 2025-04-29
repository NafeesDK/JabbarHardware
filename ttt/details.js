document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const subjectInput = document.getElementById('subject');
    const messageTextarea = document.getElementById('message');
    const charCount = document.getElementById('charCount');

    // Input length check for name
    nameInput.addEventListener('input', function() {
        const maxLength = this.maxLength;
        const currentLength = this.value.length;
        const errorMessage = document.getElementById('name-error');

        if (currentLength >= maxLength) {
            errorMessage.textContent = 'Maximum limit reached';
            this.style.borderColor = 'red';
        } else {
            errorMessage.textContent = '';
            this.style.borderColor = '';
        }
    });

    // Input length check for email
    emailInput.addEventListener('input', function() {
        const maxLength = this.maxLength;
        const currentLength = this.value.length;
        const errorMessage = document.getElementById('email-error');

        if (currentLength >= maxLength) {
            errorMessage.textContent = 'Maximum limit reached';
            this.style.borderColor = 'red';
        } else {
            errorMessage.textContent = '';
            this.style.borderColor = '';
        }
    });

    // Input length check for subject
    subjectInput.addEventListener('input', function() {
        const maxLength = this.maxLength;
        const currentLength = this.value.length;
        const errorMessage = document.getElementById('subject-error');

        if (currentLength >= maxLength) {
            errorMessage.textContent = 'Maximum limit reached';
            this.style.borderColor = 'red';
        } else {
            errorMessage.textContent = '';
            this.style.borderColor = '';
        }
    });

    // Character counter for message textarea
    messageTextarea.addEventListener('input', function() {
        this.style.height = 'auto'; 
        this.style.height = this.scrollHeight + 'px'; 
        charCount.textContent = this.value.length + '/500'; 
    });
});