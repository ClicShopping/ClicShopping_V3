/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

function SetFocus() {
  const forms = document.forms;

  if (forms.length > 0) {
    for (let f = 0; f < forms.length; f++) {
      if (forms[f].name !== "adminlanguage") {
        const field = forms[f];

        for (let i = 0; i < field.length; i++) {
          const type = field.elements[i].type;
          const disabled = field.elements[i].disabled;

          if (
            type !== "image" &&
            type !== "hidden" &&
            type !== "reset" &&
            type !== "button" &&
            type !== "submit" &&
            disabled !== true
          ) {
            field.elements[i].focus();

            if (type === "text" || type === "password") {
              field.elements[i].select();
            }

            break;
          }
        }
      }
    }
  }
}


function toggleDivBlock(id) {
  const itm = document.getElementById(id) || document.all[id] || document.layers[id];

  if (itm) {
    if (itm.style.display != 'none') {
      itm.style.display = 'none';
    } else {
      itm.style.display = 'block';
    }
  }
}
