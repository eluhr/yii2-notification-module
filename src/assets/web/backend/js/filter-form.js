var sortButton = document.getElementById('sort');

if (sortButton) {
  sortButton.addEventListener("click", function() {
    document.getElementById('inbox-sort-form').submit();
  });
}
