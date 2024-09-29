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
            <a class="nav-link" href="#create-font-group">Create Font Groups</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#font-group-list">Font Groups List</a>
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

  <section id="create-font-group" class="mt-5">
    <div class="container">
      <h4>Create Font Group</h4>
      <span>You have to select at least two fonts</span>
      <div class="mt-3">
        <div class="alert alert-success" style="display: none;" id="font-group-response-success-alert" role="alert"></div>
        <div class="alert alert-danger" style="display: none;" id="font-group-response-danger-alert" role="alert"></div>
      </div>
      <form action="" class="mt-3" id="create-font-group-form">
        <input type="hidden" name="font_group_id" id="font_group_id" value="">
        <input type="text" name="title" class="form-control" placeholder="Group Title">
        <div id="fontGroupContainer">
          <div class="card my-2 font-group-item">
            <div class="card-body">
              <div class="row align-items-center">
                <div class="col-5">
                  <input type="text" name="font_name[]" class="form-control font-name-input" placeholder="Font Name">
                </div>
                <div class="col-5">
                  <select class="form-select font-group-select-font" name="font_id[]">
                    <option selected disabled>Select a font</option>
                  </select>
                </div>
                <div class="col-2 text-center">
                  <a href="javascript:void(0)" class="text-decoration-none text-danger remove-row">X</a>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex mt-3">
          <button type="button" class="btn btn-outline-primary add-row">+ Add Row</button>
          <button type="submit" class="btn btn-success ms-auto">Save</button>
        </div>
      </form>
    </div>
  </section>

  <section id="font-group-list" class="my-5">
    <div class="container">
      <h4>Our Font Groups</h4>
      <table class="table" id="fontGroupsTable">
        <thead>
          <tr class="table-active">
            <th scope="col">NAME</th>
            <th scope="col">FONTS</th>
            <th scope="col">COUNT</th>
            <th scope="col">ACTION</th>
          </tr>
        </thead>
        <tbody id="fontGroupsTableBody">
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
            loadFontsAndFontGroups();

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

    let fontOptions = '<option selected disabled>Select a font</option>';

    const newCard = `
        <div class="card my-2 font-group-item">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-5">
                        <input type="text" name="font_name[]" class="form-control font-name-input" placeholder="Font Name">
                    </div>
                    <div class="col-5">
                        <select class="form-select font-group-select-font" name="font_id[]">
                            <option selected disabled>Select a font</option>
                        </select>
                    </div>
                    <div class="col-2 text-center">
                        <a href="javascript:void(0)" class="text-decoration-none text-danger remove-row">X</a>
                    </div>
                </div>
            </div>
        </div>
        `;

    function loadFontsAndFontGroups() {
      $.ajax({
        url: 'submit.php',
        method: 'GET',
        dataType: 'json',
        success: function(response) {
          let fontsTableBody = $('#fontsTableBody');
          let fontFaceCSS = '';
          fontOptions = '<option selected disabled>Select a font</option>';
          fontsTableBody.empty();

          response.data.fonts.forEach(function(font) {
            fontFaceCSS += `
          @font-face {
            font-family: '${font.font_name}';
            src: url('${font.file_path}');
          }
        `;

            fontOptions += `
          <option value="${font.id}" data-font-name="${font.font_name}">${font.font_name}</option>
        `;

            fontsTableBody.append(`
            <tr>
              <td>${font.font_name}</td>
              <td style="font-family: '${font.font_name}'; font-size: 18px;">
                The quick brown fox jumps over the lazy dog.
              </td>
              <td><a href="#" class="text-decoration-none text-danger delete-font" data-id="${font.id}" data-name="${font.font_name}">DELETE</a></td>
            </tr>
            `);
          });

          $('head').append(`<style>${fontFaceCSS}</style>`);

          $('.font-group-select-font').each(function() {
            $(this).html(fontOptions);
          });

          // For Font Groups 
          let fontGroupsTableBody = $('#fontGroupsTableBody');
          fontGroupsTableBody.empty();
          response.data.fontGroups.forEach(function(group) {
            let fontNames = group.fonts.join(', ');
            fontGroupsTableBody.append(`
                    <tr data-id="${group.id}">
                      <td>${group.title}</td>
                      <td>${fontNames}</td>
                      <td>${group.font_count}</td>
                      <td>
                          <button class="btn btn-sm btn-primary edit-group" data-id="${group.id}">Edit</button>
                          <button class="btn btn-sm btn-danger delete-group" data-id="${group.id}">Delete</button>
                      </td>
                    </tr>
                `);
          });
        },
        error: function(error) {
          var alertDiv = document.getElementById('response-danger-alert');
          alertDiv.innerHTML = error.responseText;
          alertDiv.style.display = 'block';
        }
      });
    }

    loadFontsAndFontGroups();

    $(document).on('click', '.delete-font', function(e) {
      e.preventDefault();

      let fontId = $(this).data('id');
      let fontName = $(this).data('name');

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
            loadFontsAndFontGroups();

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

    $('.add-row').on('click', function() {
      const newCardElement = $(newCard).appendTo('#fontGroupContainer');
      newCardElement.find('.font-group-select-font').html(fontOptions);
    });

    $(document).on('click', '.remove-row', function() {
      if ($('.font-group-item').length > 1) {
        $(this).closest('.font-group-item').remove();
      }
    });

    $(document).on('change', '.font-group-select-font', function() {
      let selectedFontName = $(this).find('option:selected').data('font-name');
      $(this).closest('.row').find('.font-name-input').val(selectedFontName);
    });

    $('#create-font-group-form').on('submit', function(e) {
      e.preventDefault();

      const title = $('input[name="title"]').val();
      const fontIds = [];
      const fontTitles = [];

      $('.font-group-item').each(function() {
        const fontId = $(this).find('select.font-group-select-font').val();
        const fontTitle = $(this).find('input.font-name-input').val();
        if (fontId) {
          fontIds.push(fontId);
          fontTitles.push(fontTitle);
        }
      });

      let action = $('#font_group_id').val() ? 'editFontGroup' : 'createFontGroup';

      $.ajax({
        url: 'submit.php',
        method: 'POST',
        data: {
          title: title,
          font_id: fontIds,
          font_name: fontTitles,
          font_group_id: $('#font_group_id').val(),
          action: action,
        },
        success: function(response) {
          if (response.success) {
            $('input[name="title"]').val('');
            $('#fontGroupContainer').html(newCard);
            $('#font_group_id').val('');
            loadFontsAndFontGroups();
            var alertDiv = document.getElementById('font-group-response-success-alert');
            alertDiv.innerHTML = response.message;
            alertDiv.style.display = 'block';

            setTimeout(function() {
              alertDiv.style.display = 'none';
            }, 3000);
          } else {
            var alertDiv = document.getElementById('font-group-response-danger-alert');
            alertDiv.innerHTML = response.message;
            alertDiv.style.display = 'block';

            setTimeout(function() {
              alertDiv.style.display = 'none';
            }, 3000);
          }
        },
        error: function(xhr, status, error) {
          console.error('AJAX Error:', status, error);
        }
      });
    });

    $(document).on('click', '.delete-group', function(e) {
      e.preventDefault();

      let groupId = $(this).data('id');

      $.ajax({
        url: 'submit.php',
        method: 'POST',
        data: {
          action: 'deleteGroup',
          group_id: groupId
        },
        success: function(response) {
          if (response.success) {
            $(`tr[data-id="${groupId}"]`).remove();
            var alertDiv = document.getElementById('font-group-response-success-alert');
            alertDiv.innerHTML = response.message;
            alertDiv.style.display = 'block';

            setTimeout(function() {
              alertDiv.style.display = 'none';
            }, 3000);
          } else {
            var alertDiv = document.getElementById('font-group-response-danger-alert');
            alertDiv.innerHTML = response.message;
            alertDiv.style.display = 'block';

            setTimeout(function() {
              alertDiv.style.display = 'none';
            }, 3000);
          }
        },
        error: function(error) {
          console.error('Error during AJAX request:', error);
          alert('An error occurred while deleting the group.');
        }
      });
    });

    $(document).on('click', '.edit-group', function() {
      document.getElementById('create-font-group').scrollIntoView();
      let groupId = $(this).data('id');
      $.ajax({
        url: 'submit.php',
        method: 'GET',
        data: {
          action: 'getGroup',
          group_id: groupId
        },
        success: function(response) {
          if (response.success) {
            $('#font_group_id').val(groupId);
            $('input[name="title"]').val(response.data.title);

            let fontGroupContainer = $('#fontGroupContainer');
            fontGroupContainer.empty();

            response.data.fonts.forEach(function(font) {
              let newCard = `
                        <div class="card my-2 font-group-item">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-5">
                                        <input type="text" name="font_name[]" class="form-control font-name-input" value="${font.font_title}" placeholder="Font Name">
                                    </div>
                                    <div class="col-5">
                                        <select class="form-select font-group-select-font" name="font_id[]">
                                            ${fontOptions}
                                        </select>
                                    </div>
                                    <div class="col-2 text-center">
                                        <a href="javascript:void(0)" class="text-decoration-none text-danger remove-row">X</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
              fontGroupContainer.append(newCard);
              $('#fontGroupContainer .font-group-select-font:last').val(font.font_id);
            });
          } else {
            var alertDiv = document.getElementById('font-group-response-danger-alert');
            alertDiv.innerHTML = response.message;
            alertDiv.style.display = 'block';

            setTimeout(function() {
              alertDiv.style.display = 'none';
            }, 3000);
          }
        },
        error: function(error) {
          console.error('Error fetching font group:', error);
        }
      });
    });
  </script>

</body>

</html>