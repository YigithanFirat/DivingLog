document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const sifre = document.getElementById("sifre");
    const tekrarsifre = document.getElementById("tekrarsifre");

    form.addEventListener("submit", function (e) {
        if (sifre.value !== tekrarsifre.value) {
            e.preventDefault();
            alert("Şifreler uyuşmuyor. Lütfen iki alanın da aynı olduğundan emin olun.");
        }
    });
});