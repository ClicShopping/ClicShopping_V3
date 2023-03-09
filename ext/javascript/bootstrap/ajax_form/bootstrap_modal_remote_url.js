/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */
document.body.addEventListener('click', function(event) {
  const target = event.target;
  const toggle = target.dataset.bsToggle;

  if (toggle === 'modal') {
    const modalId = target.dataset.target;
    const modalBody = document.querySelector(`${modalId} .modal-body`);

    fetch(target.dataset.remote)
      .then(response => response.text())
      .then(html => modalBody.innerHTML = html)
      .catch(error => console.error(error));
  }
});

