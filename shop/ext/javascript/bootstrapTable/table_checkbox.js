var $table = $('#table')
var $button = $('#button')

$(function() {
  $button.click(function () {
    $('form').submit(function () {
      if (!window.confirm('Are you sure?')) return false
    })
  })
//export
  $table.bootstrapTable()
})
