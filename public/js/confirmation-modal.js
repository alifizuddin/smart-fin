function confirmAction(url, action, modalId = 'confirmationModal', extraText = '') {
    const modal = document.getElementById(modalId)
    const card = document.getElementById(modalId + 'Card')
    const icon = document.getElementById(modalId + 'Icon')
    const title = document.getElementById(modalId + 'Title')
    const message = document.getElementById(modalId + 'Message')
    const confirmButton = document.getElementById(modalId + 'ConfirmButton')
    const form = document.getElementById(modalId + 'Form')
    
    // Locate the hidden input that Laravel uses to determine the HTTP verb (PATCH, DELETE, etc.)
    const methodInput = form.querySelector('input[name="_method"]')

    form.action = url
    modal.classList.remove('hidden')
    document.body.style.overflow = 'hidden'

    setTimeout(() => {
        card.classList.remove('scale-95', 'opacity-0')
        card.classList.add('scale-100', 'opacity-100')
    }, 10)

    if (action === 'approve') {
        // Ensure method is PATCH for approvals
        if (methodInput) methodInput.value = 'PATCH'

        icon.innerHTML = `
            <div class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 p-4 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>`
        title.textContent = 'Approve Action?'
        message.textContent = 'Confirm approval ' + (extraText ? `(${extraText})` : '') + '?'
        confirmButton.className = 'px-5 py-2 rounded-lg font-semibold text-white bg-green-600 hover:bg-green-700 focus:ring-green-300 transition'
        
    } else if (action === 'delete') {
        // Change method to DELETE for delete actions
        if (methodInput) methodInput.value = 'DELETE'

        icon.innerHTML = `
            <div class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 p-4 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3m-9 0h12" />
                </svg>
            </div>`
        title.textContent = 'Delete Item?'
        message.textContent = 'Delete this item ' + (extraText ? `(${extraText})` : '') + '? This action cannot be undone.'
        confirmButton.className = 'px-5 py-2 rounded-lg font-semibold text-white bg-red-600 hover:bg-red-700 focus:ring-red-300 transition'
        
    } else {
        // Default to PATCH for rejections (or other updates)
        if (methodInput) methodInput.value = 'PATCH'

        icon.innerHTML = `
            <div class="bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-300 p-4 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
                </svg>
            </div>`
        title.textContent = 'Reject Action?'
        message.textContent = 'Reject this item ' + (extraText ? `(${extraText})` : '') + '?'
        confirmButton.className = 'px-5 py-2 rounded-lg font-semibold text-white bg-red-600 hover:bg-red-700 focus:ring-red-300 transition'
    }

    // Always bind the confirm button to submit the modal form.
    // The actual HTTP verb seen in the Network tab will be POST,
    // with Laravel using the hidden _method field (DELETE/PATCH).
    if (confirmButton && form) {
        confirmButton.onclick = () => form.submit()
    }
}

function closeModal(modalId = 'confirmationModal') {
    const modal = document.getElementById(modalId)
    const card = document.getElementById(modalId + 'Card')

    card.classList.add('scale-95', 'opacity-0')
    setTimeout(() => {
        modal.classList.add('hidden')
        document.body.style.overflow = 'auto'
    }, 150)
}

// ESC key listener (universal)
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id$="confirmationModal"]').forEach(m => {
            if (!m.classList.contains('hidden')) closeModal(m.id)
        })
    }
})