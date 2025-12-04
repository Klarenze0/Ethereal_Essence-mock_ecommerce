document.addEventListener("DOMContentLoaded", function() {
    fetch("header.php")
      .then(response => response.text())
      .then(data => {
        document.getElementById("header").innerHTML = data;
      });

    fetch("footer.php")
      .then(response => response.text())
      .then(data => {
        document.getElementById("footer").innerHTML = data;
      });
  });