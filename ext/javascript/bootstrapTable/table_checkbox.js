const table = document.getElementById('table');
const button = document.getElementById('button');

window.addEventListener('load', function() {
  button.addEventListener('click', function() {
    const form = document.querySelector('form');
    form.addEventListener('submit', function(event) {
      if (!window.confirm('Are you sure?')) {
        event.preventDefault();
      }
    });
  });

  table.classList.add('table');
  table.classList.add('table-hover');
  table.classList.add('table-bordered');

  const exportTypes = ['json', 'xml', 'csv', 'excel', 'pdf'];
  const exportDataType = exportTypes[0];

  const options = {
    exportDataType: exportDataType,
    exportTypes: exportTypes,
  };

  const bootstrapTable = new BootstrapTable(table, options);
});

