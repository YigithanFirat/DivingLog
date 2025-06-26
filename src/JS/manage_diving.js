const deleteModal = document.getElementById('confirmDeleteModal');
deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const input = deleteModal.querySelector('#delete-id');
    input.value = id;
});