var $table = $('#table')
var $button = $('#button')

$(function() {
  $button.click(function () {
    $('form').submit(function () {
//         alert($(this).serialize())
      if (!window.confirm('Are you sure?')) return false
//          return false
    })
  })
})
