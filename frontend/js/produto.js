
document.getElementById("aumentar").addEventListener("click", aumentarQuantidade);
document.getElementById("diminuir").addEventListener("click", diminuirQuantidade);

function aumentarQuantidade() {
    const quantidadeProduto = document.querySelector(".quantidade");
    let quantidade = Number(quantidadeProduto.value);
    quantidade += 1;
    quantidadeProduto.value = quantidade;
}
function diminuirQuantidade() {
    const quantidadeProduto = document.querySelector(".quantidade");
    let quantidade = Number(quantidadeProduto.value);
    quantidade -= 1;
    quantidade <= 1 ? quantidade = 1 : true;
    quantidadeProduto.value = quantidade;

}