/*
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

function sortDropDownListByText() {
    // Loop for each select element on the page.
    const selectElements = document.querySelectorAll("#sortDropDownListByText");
    selectElements.forEach(function(selectElement) {
        // Keep track of the selected option.
        const selectedValue = selectElement.value;

        // Sort all the options by text.
        const options = Array.from(selectElement.options);
        options.sort(function(a, b) {
            return a.text == b.text ? 0 : a.text < b.text ? -1 : 1;
        });
        options.forEach(function(option) {
            selectElement.appendChild(option);
        });

        // Select one option.
        selectElement.value = selectedValue;
    });
}

document.addEventListener("DOMContentLoaded", sortDropDownListByText);
