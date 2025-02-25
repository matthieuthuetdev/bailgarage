const btnAdmin = document.getElementById("btnAdmin");
const btnOwner = document.getElementById("btnOwner");
const inputEmail = document.getElementById("email")
const inputPassword = document.getElementById("password")
btnAdmin.addEventListener("click",() =>{
    inputEmail.value = "admin@bailgarage.fr"
    inputPassword.value = "admin";
})
btnOwner.addEventListener("click",() =>{
    inputEmail.value = "proprio@bailgarage.fr"
    inputPassword.value = "8b3ee2cb";
})