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
    $("#sortDropDownListByText").each(function() {
// Keep track of the selected option.
        const selectedValue = $(this).val();

// Sort all the options by text. I could easily sort these by val.
        $(this).html($("option", $(this)).sort(function(a, b) {
            return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
        }));

// Select one option.
        $(this).val(selectedValue);
    });
}

$(document).ready(sortDropDownListByText);
