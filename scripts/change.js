
const form = document.querySelector("form");
const password1 = document.getElementById("password1");
const password2 = document.getElementById("password2");
const coincideMsg = document.getElementById("coincide");


coincideMsg.style.display = "none";


password1.addEventListener("input", validatePasswords);
password2.addEventListener("input", validatePasswords);

password2.addEventListener("focus", () => {
    if (password1.value && password1.value !== password2.value) {
        password2.style.boxShadow = "0 2 4px rgb(255, 67, 67)";
        password2.style.borderColor = "rgb(255, 67, 67)";
        password2.style.boxShadow = "0 2px 4px rgb(255, 67, 67)";
        password2.style.transition = ".2s" ;
    }
});

password2.addEventListener("blur", () => {
    password2.style.boxShadow = "box-shadow: 0 2px 2px #023266;"; 
});

form.addEventListener("submit", (event) => {
    if (!validatePasswords()) {
        event.preventDefault(); 
    }
});


function validatePasswords() {
    if (!password1.value || !password2.value) {
        
        coincideMsg.style.display = "none";
        password2.style.border = "";
        password2.style.boxShadow = "";
        return false;
    }

    if (password1.value === password2.value) {
        
        coincideMsg.style.display = "none";
        password2.style.border = "2px solid rgb(255, 67, 67)";
        password2.style.borderColor = "#023266";
        password2.style.boxShadow = "";   
        return true;
    } else {
        
        coincideMsg.style.display = "block";
        password2.style.border = "2px solid rgb(255, 67, 67)";

        
        if (document.activeElement === password2) {
            password2.style.boxShadow = "0 2 4px rgb(255, 67, 67)";
        }
        return false;
    }
}
