document.addEventListener("DOMContentLoaded", function() {
let btn = document.querySelector("#payNow");
if (btn) {
btn.addEventListener("click", function() {
submitForm(btn);
});
}
function submitForm(button) {
if (button) {
button.disabled = true;
setTimeout(function() {
button.disabled = false;
}, 6000);
}
}
});
</> //add this line else does not work with chrome/edge/opera