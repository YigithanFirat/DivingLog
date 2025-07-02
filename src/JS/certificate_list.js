function setDeleteId(id) {
    document.getElementById('deleteCertificateId').value = id;
}

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('hidden');
}