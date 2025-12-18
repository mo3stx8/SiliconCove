document
  .getElementById("imageInput")
  .addEventListener("change", function (event) {
    const [file] = event.target.files;
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        document.getElementById("imagePreview").src = e.target.result;
        document.getElementById("imagePreview").style.display = "block";
      };
      reader.readAsDataURL(file);
    }
  });
