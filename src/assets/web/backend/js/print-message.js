// credit: https://stackoverflow.com/questions/2255291/print-the-contents-of-a-div#answer-2255438
function printElement(element)
{
  var printWindow = window.open('', 'PRINT', 'height=400,width=600');

  printWindow.document.write('<html><head><title>' + document.title  + '</title>');
  printWindow.document.write('</head><body >');
  printWindow.document.write(document.getElementById(element).innerHTML);
  printWindow.document.write('</body></html>');

  printWindow.document.close(); // necessary for IE >= 10
  printWindow.focus(); // necessary for IE >= 10*/

  printWindow.print();
  printWindow.close();

  return true;
}

var printButtons = document.querySelectorAll('[data-print]');

printButtons.forEach(function(element) {
  element.addEventListener("click", function() {
    printElement(element.getAttribute('data-print'))
  });
});


