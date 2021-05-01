var $table = $('#table')
var $button = $('#button')

$(function() {
  $button.click(function () {
    $('form').submit(function () {
      if (!window.confirm('Are you sure?')) return false
    })
  })
//export
  $table.bootstrapTable(),
  $table.bootstrapTable('destroy').bootstrapTable({
    exportDataType: $(this).val(),
    exportTypes: ['json', 'xml', 'csv', 'excel', 'pdf'],
  })
})
