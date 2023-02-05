

console.clear();
('use strict');


// Drag and drop - single or multiple image files
// https://www.smashingmagazine.com/2018/01/drag-drop-file-uploader-vanilla-js/
// https://codepen.io/joezimjs/pen/yPWQbd?editors=1000
(function () {

  'use strict';
  
  // Four objects of interest: drop zones, input elements, gallery elements, and the files.
  // dataRefs = {files: [image files], input: element ref, gallery: element ref}

  let AllFiles = [];
  var InputFiles = document.getElementById('category_image');

  const preventDefaults = event => {
    event.preventDefault();
    event.stopPropagation();
  };

  const highlight = event =>
    event.target.classList.add('highlight');
  
  const unhighlight = event =>
    event.target.classList.remove('highlight');

  const getInputAndGalleryRefs = element => {
    const zone = element.closest('.upload_dropZone') || false;
    const gallery = zone.querySelector('.upload_gallery') || false;
    const input = zone.querySelector('input[type="file"]') || false;
    console.log(input)
    return {input: input, gallery: gallery};
  }

//   const handleDrop = event => {
//     const dataRefs = getInputAndGalleryRefs(event.target);
//     dataRefs.files = event.dataTransfer.files;
//     AllFiles.push(dataRefs.files);
//     if(dataRefs.files.length > 1 || AllFiles.length > 1)
//     {
//         alert("Veuillez glisser et deposer qu'une seule image svp !");
//         return ;
//     }
//     handleFiles(dataRefs);

//     InputFiles.files = event.dataTransfer.files;

//   }


  const eventHandlers = zone => {

    const dataRefs = getInputAndGalleryRefs(zone);
    if (!dataRefs.input) return;

    // // Prevent default drag behaviors
    // ;['dragenter', 'dragover', 'dragleave', 'drop'].forEach(event => {
    //   zone.addEventListener(event, preventDefaults, false);
    //   document.body.addEventListener(event, preventDefaults, false);
    // });

    // // Highlighting drop area when item is dragged over it
    // ;['dragenter', 'dragover'].forEach(event => {
    //   zone.addEventListener(event, highlight, false);
    // });
    // ;['dragleave', 'drop'].forEach(event => {
    //   zone.addEventListener(event, unhighlight, false);
    // });

    // // Handle dropped files
    // zone.addEventListener('drop', handleDrop, false);

    // Handle browse selected files
    dataRefs.input.addEventListener('change', event => {
      dataRefs.files = event.target.files;
      handleFiles(dataRefs);
    }, false);

  }


  // Initialise ALL dropzones
  const dropZones = document.querySelectorAll('.upload_dropZone');
  for (const zone of dropZones) {
    eventHandlers(zone);
  }


  // No 'image/gif' or PDF or webp allowed here, but it's up to your use case.
  // Double checks the input "accept" attribute
  const isImageFile = file => 
    ['image/jpeg', 'image/jpg', 'image/png'].includes(file.type);


  function previewFiles(dataRefs) {
    if (!dataRefs.gallery) return;
    for (const file of dataRefs.files) {
      let reader = new FileReader();
      reader.readAsDataURL(file);
      reader.onloadend = function() {
        let img = document.createElement('img');
        img.className = 'upload_img mt-2';
        img.setAttribute('alt', file.name);
        img.src = reader.result;
        dataRefs.gallery.appendChild(img);
      }
    }
  }

  // Handle both selected and dropped files
  const handleFiles = dataRefs => {

    let files = [...dataRefs.files];

    // Remove unaccepted file types
    files = files.filter(item => {
      if (!isImageFile(item)) {
        console.log('Not an image, ', item.type);
      }
      return isImageFile(item) ? item : null;
    });

    if (!files.length) return;
    dataRefs.files = files;

    previewFiles(dataRefs);

  }

})();