<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
</head>

<body>
  <nav class="navbar bg-dark navbar-expand-lg border-bottom border-body" data-bs-theme="dark">
    <div class="container">
      <span class="navbar-brand" href="#">Font Groups Maker</span>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" aria-current="page" href="#uploader">Font Uploader</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#font-list">Font List</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Create Font Groups</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Font Groups List</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-5">
    <div class="alert alert-success" style="display: none;" id="response-success-alert" role="alert"></div>
    <div class="alert alert-danger" style="display: none;" id="response-danger-alert" role="alert"></div>
  </div>

  <section id="uploader" class="mt-5">
    <div class="container">
      <h4 class="mb-3">Upload Fonts</h4>
      <form action="./submit.php" class="dropzone" id="my-great-dropzone"></form>
    </div>
  </section>


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
  <script>
    Dropzone.options.myGreatDropzone = {
      paramName: "fontFile",
      maxFilesize: 2, // MB
      acceptedFiles: ".ttf",
      dictDefaultMessage: "Drag & drop your TTF file here or click to upload",
      autoProcessQueue: true,
      init: function() {
        var dropzoneInstance = this;

        this.on("success", function(file, response) {
          if (response.status) {
            var alertDiv = document.getElementById('response-success-alert');
            alertDiv.innerHTML = 'Font file uploaded successfully!';
            alertDiv.style.display = 'block';

            setTimeout(function() {
              alertDiv.style.display = 'none';
            }, 3000);
          } else {
            var alertDiv = document.getElementById('response-danger-alert');
            alertDiv.innerHTML = response;
            alertDiv.style.display = 'block';

            setTimeout(function() {
              alertDiv.style.display = 'none';
            }, 3000);
          }

        });
        this.on("complete", function(file) {
          setTimeout(function() {
            dropzoneInstance.removeFile(file);
          }, 1000);
        });
      }
    };
  </script>

</body>

</html>