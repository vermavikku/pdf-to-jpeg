<!DOCTYPE html>
<html>
<head>
  <title>PDF to JPEG Conversion</title>
</head>
<body>
  <h1>PDF to JPEG Conversion</h1>

  <input type="file" id="pdfFile" accept=".pdf">
  <button id="convertBtn">Convert to JPEG</button>

  <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script> -->
  <script src="./pdf.min.js"></script>
  <script>
    // Set worker source path for PDF.js
    // pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
    pdfjsLib.GlobalWorkerOptions.workerSrc = './pdf.work.min.js';

    async function convertToJPEG(pdfData, fileName) {
      try {
        const loadingTask = pdfjsLib.getDocument({ data: pdfData });
        const pdf = await loadingTask.promise;
        const totalPageCount = pdf.numPages;
        const folderPath = "upload/";

        for (let pageNumber = 1; pageNumber <= totalPageCount; pageNumber++) {
          const page = await pdf.getPage(pageNumber);
          const viewport = page.getViewport({ scale: 2.5 });
          const canvas = document.createElement("canvas");
          const context = canvas.getContext("2d");
          canvas.height = viewport.height;
          canvas.width = viewport.width;

          const renderContext = {
            canvasContext: context,
            viewport: viewport,
          };

          await page.render(renderContext).promise;
          const imageData = canvas.toDataURL("image/jpeg");
          const jpgFilename = fileName + pageNumber + ".jpg";

          // Send the image data to the PHP file for saving
          saveImageData(imageData, jpgFilename);
        }

      } catch (error) {
        console.error("An error occurred during conversion:", error);
        alert("An error occurred during conversion. Please try again.");
      }
    }

    function saveImageData(imageData, filename) {
      const xhr = new XMLHttpRequest();
      const formData = new FormData();

      formData.append("imageData", imageData);
      formData.append("filename", filename);

      xhr.open("POST", "save_image.php", true);

      xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE) {
          if (xhr.status === 200) {
            console.log("Image data saved successfully!");
          } else {
            console.error("Error saving image data:", xhr.responseText);
          }
        }
      };

      xhr.send(formData);
    }

    const pdfFileInput = document.getElementById("pdfFile");
    pdfFileInput.addEventListener("change", function (event) {
      const file = event.target.files[0];

      if (file && file.name.toLowerCase().endsWith(".pdf")) {
        const reader = new FileReader();

        reader.onload = function (e) {
          const pdfData = new Uint8Array(e.target.result);
          const fileName = file.name.split(".pdf")[0];
          convertToJPEG(pdfData, fileName);
        };

        reader.readAsArrayBuffer(file);
      } else {
        alert("Please select a PDF file.");
      }
    });

    // const convertBtn = document.getElementById("convertBtn");
    // convertBtn.addEventListener("click", function () {
    //   const fileInput = document.getElementById("pdfFile");

    //   if (fileInput.files.length > 0) {
    //     const file = fileInput.files[0];
    //     const reader = new FileReader();

    //     reader.onload = function (e) {
    //       const pdfData = new Uint8Array(e.target.result);
    //       const fileName = file.name.split(".pdf")[0];
    //       convertToJPEG(pdfData, fileName);
    //     };

    //     reader.readAsArrayBuffer(file);
    //   } else {
    //     alert("Please select a PDF file.");
    //   }
    // });
  </script>
</body>
</html>
