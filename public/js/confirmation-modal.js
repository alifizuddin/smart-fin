function confirmAction(url, action, modalId = 'confirmationModal', extraText = '') {
    const modal = document.getElementById(modalId)
    const card = document.getElementById(modalId + 'Card')
    const icon = document.getElementById(modalId + 'Icon')
    const title = document.getElementById(modalId + 'Title')
    const message = document.getElementById(modalId + 'Message')
    const confirmButton = document.getElementById(modalId + 'ConfirmButton')
    const form = document.getElementById(modalId + 'Form')

    form.action = url
    modal.classList.remove('hidden')
    document.body.style.overflow = 'hidden'

    setTimeout(() => {
        card.classList.remove('scale-95', 'opacity-0')
        card.classList.add('scale-100', 'opacity-100')
    }, 10)

    if (action === 'approve') {
        icon.innerHTML = `
            <div class="bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 p-4 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>`
        title.textContent = 'Approve Action?'
        message.textContent = 'Confirm approval ' + (extraText ? `(${extraText})` : '') + '?'
        confirmButton.className = 'px-5 py-2 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:ring-blue-300 transition'
    } else {
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
