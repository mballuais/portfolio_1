// script.js
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const errorElement = document.getElementById("error");

  form.addEventListener("submit", function (event) {
    const username = form.username.value.trim();
    const password = form.password.value.trim();

    if (username === "" || password === "") {
      errorElement.textContent = "Tous les champs sont requis";
      event.preventDefault();
    }
  });
});
