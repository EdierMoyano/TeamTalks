const nav = document.queryselector("#nav");
const abrir = document.queryselector("#abrir");
const cerrar = document.queryselector("#cerrar");

abrir.addEventlistener("click", () => {
    nav.classList.add("visible");
})
cerrar.addEventlistener("click", () => {
    nav.classList.remove("visible");
})