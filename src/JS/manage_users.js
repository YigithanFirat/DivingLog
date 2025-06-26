function openConfirmModal(userId) {
    document.getElementById('deleteUserId').value = userId;
    document.getElementById('confirmModal').style.display = 'flex';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}