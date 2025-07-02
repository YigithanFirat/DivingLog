function openConfirmModal(userId) {
    const modal = document.getElementById('confirmModal');
    const input = document.getElementById('deleteUserId');

    if (!modal || !input) {
        console.error("Silme modali ya da gizli input bulunamadı.");
        return;
    }

    input.value = userId;
    modal.classList.add('active'); // CSS'te 'display: flex' için bu class kullanılabilir
}

function closeConfirmModal() {
    const modal = document.getElementById('confirmModal');
    if (!modal) return;

    modal.classList.remove('active');
}

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('hidden');
}