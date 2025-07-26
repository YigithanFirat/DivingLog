const deleteModal = document.getElementById('confirmDeleteModal');
deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const id = button.getAttribute('data-id');
    const input = deleteModal.querySelector('#delete-id');
    input.value = id;
});

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('hidden');
}

document.addEventListener("DOMContentLoaded", function () {
    document.querySelectorAll(".show-user-dives").forEach(button => {
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const tcno = this.getAttribute("data-tc");
            const url = new URL(window.location.href);
            url.searchParams.set("tcno", tcno);
            window.location.href = url.toString();
        });
    });

    if (document.querySelector("input[name='tcno']").value.trim() !== "") {
        const userTable = document.getElementById("user-list-table");
        if (userTable) {
            userTable.style.display = "none";
        }
    }
});