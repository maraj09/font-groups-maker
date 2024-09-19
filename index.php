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

  <section id="font-list" class="mt-5">
    <div class="container">
      <h4 class="mb-3">Our Fonts</h4>
      <table class="table" id="fontsTable">
        <thead>
          <tr class="table-active">
            <th scope="col">Font Name</th>
            <th scope="col">Preview</th>
            <th scope="col"></th>
          </tr>
        </thead>
        <tbody id="fontsTableBody">

        </tbody>
      </table>
    </div>
  </section>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
            loadFonts();

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

    function loadFonts() {
      $.ajax({
        url: 'submit.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          let fontsTableBody = $('#fontsTableBody');
          let fontFaceCSS = '';
          fontsTableBody.empty();

          response.data.fonts.forEach(function(font) {
            fontFaceCSS += `
          @font-face {
            font-family: '${font.font_name}';
            src: url('${font.file_path}');
          }
        `;

            fontsTableBody.append(`
            <tr>
              <td>${font.font_name}</td>
              <td style="font-family: '${font.font_name}'; font-size: 18px;">
                The quick brown fox jumps over the lazy dog.
              </td>
              <td><a href="#" class="text-decoration-none text-danger delete-font" data-id="${font.id}">DELETE</a></td>
            </tr>
            `);
          });

          $('head').append(`<style>${fontFaceCSS}</style>`);
        },
        error: function(error) {
          var alertDiv = document.getElementById('response-danger-alert');
          alertDiv.innerHTML = error.responseText;
          alertDiv.style.display = 'block';
        }
      });
    }

    loadFonts();

    $(document).on('click', '.delete-font', function(e) {
      e.preventDefault();

      let fontId = $(this).data('id');

      $.ajax({
        url: 'submit.php',
        method: 'POST',
        data: {
          action: 'delete',
          id: fontId,
          status: 'deleteFont',
        },
        success: function(response) {
          if (response.success) {
            $(`a[data-id="${fontId}"]`).closest('tr').remove();

            var alertDiv = document.getElementById('response-success-alert');
            alertDiv.innerHTML = 'Font deleted successfully!';
            alertDiv.style.display = 'block';

            setTimeout(function() {
              alertDiv.style.display = 'none';
            }, 3000);
          } else {
            var alertDiv = document.getElementById('response-danger-alert');
            alertDiv.innerHTML = response.message;
            alertDiv.style.display = 'block';

            setTimeout(function() {
              alertDiv.style.display = 'none';
            }, 3000);
          }
        },
        error: function(error) {
          console.error('Error during AJAX request:', error);
        }
      });
    })
  </script>

</body>

</html>