document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const sifre = document.getElementById("sifre");
    const tekrarsifre = document.getElementById("tekrarsifre");
    const errorDiv = document.getElementById("form-errors");

    form.addEventListener("submit", function (e) {
        if (sifre.value !== tekrarsifre.value) {
            e.preventDefault();
            // Hata mesajını div içine yaz
            errorDiv.innerHTML = '<ul><li>Şifreler uyuşmuyor. Lütfen iki alanın da aynı olduğundan emin olun.</li></ul>';
            // Gerekirse inputlara odaklan
            tekrarsifre.focus();
        }
    });
});

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    sidebar.classList.toggle('hidden');
}