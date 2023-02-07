console.clear();
('use strict');

// Drag and drop - single or multiple image files
(function () {

  'use strict';
  
  // Four objects of interest: drop zones, input elements, gallery elements, and the files.
  // dataRefs = {files: [image files], input: element ref, gallery: element ref}

  const getInputAndGalleryRefs = element => {
    const zone = element.closest('.upload_dropZone') || false;
    const gallery = zone.querySelector('.upload_gallery') || false;
    const input = zone.querySelector('input[type="file"]') || false;
    return {input: input, gallery: gallery};
  }


  const eventHandlers = zone => {

    let dataRefs = getInputAndGalleryRefs(zone);
    if (!dataRefs.input) return;

    // Handle browse selected files
    dataRefs.input.addEventListener('change', event => {
      if(!dataRefs.files)
      {
        dataRefs.files = event.target.files;
        handleFiles(dataRefs);
        console.log(dataRefs.files);
      }
      else if(dataRefs.files)
      {
        dataRefs = event.target.files;
        console.log(dataRefs.files);
        handleFiles(dataRefs);
      }

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
        img.className = 'mt-2 img-fluid rounded';
        img.setAttribute('alt', file.name);
        img.setAttribute('width','150');
        img.src = reader.result;
        dataRefs.gallery.appendChild(img);
        var illustration = document.getElementById('illustration');
        illustration && dataRefs.files ? illustration.setAttribute('width', 50) : null
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
    if(!dataRefs.files)
    {
      dataRefs.files = files;
    }

    previewFiles(dataRefs);
  }



})();

function showMessage(event){
  const response =  confirm("Vous êtes sure de vouloir supprimer cette catégorie d'entreprises? Notez que cette action est irréversible !");
  if(!response) event.preventDefault();
  return;
}







