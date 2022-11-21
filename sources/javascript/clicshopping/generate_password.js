$('#button-generate').on('click', function () {
    rand = '';
    string = '+%$/?@#!ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz01234567891234567890';
    for (i = 0; i < 15; i++) {
        rand += string[Math.floor(Math.random() * (string.length - 1))];
    }
    $('#inputPassword').val(rand);
});