document.addEventListener('DOMContentLoaded', function () {
    const navLinks = document.querySelectorAll('.nav-link[data-section], .dropdown-item[data-section]');
    const sections = document.querySelectorAll('.content-section');
    const uploadLink = document.getElementById('upload-link');
    const navbarCollapse = document.querySelector('.navbar-collapse');
  
    // Function to close the mobile navigation menu
    function closeNavbar() {
      if (navbarCollapse.classList.contains('show')) {
        navbarCollapse.classList.remove('show');
      }
    }
    
  
    navLinks.forEach(link => {
      link.addEventListener('click', function (e) {
        e.preventDefault();
  
        // Remove active class from all sections
        sections.forEach(section => section.classList.remove('active'));
  
        // Add active class to the clicked section
        const targetSection = document.getElementById(link.getAttribute('data-section'));
        targetSection.classList.add('active');
  
        // Remove active class from all nav links
        navLinks.forEach(navLink => navLink.classList.remove('active'));
  
        // Add active class to the clicked nav link
        link.classList.add('active');
  
        // Close the mobile navigation menu
        closeNavbar();
      });
    });
  
    // Handle click event on the "Upload a boarding house" link
    uploadLink.addEventListener('click', function (e) {
      e.preventDefault();
  
      // Remove active class from all sections
      sections.forEach(section => section.classList.remove('active'));
  
      // Add active class to the "Upload Boarding House" section
      const targetSection = document.getElementById('upload-boarding-house');
      targetSection.classList.add('active');
  
      // Remove active class from all nav links
      navLinks.forEach(navLink => navLink.classList.remove('active'));
  
      // Find the corresponding nav link and add active class to it
      const correspondingNavLink = document.querySelector('.nav-link[data-section="upload-boarding-house"]');
      correspondingNavLink.classList.add('active');
  
      // Close the mobile navigation menu
      closeNavbar();
    });
  });
  
  document.addEventListener('DOMContentLoaded', function () {
      const saveButton = document.querySelector('#uploadModal button[type="button"]');
      const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
  
      saveButton.addEventListener('click', function () {
        // Get form data
        const businessName = document.querySelector('#businessName').value;
        const businessLocation = document.querySelector('#businessLocation').value;
        const phoneNumber = document.querySelector('#phoneNumber').value;
        const numRooms = document.querySelector('#numRooms').value;
        const boardingHouseImages = document.querySelector('#boardingHouseImages').files;
        const licensePermitImage = document.querySelector('#licensePermitImage').files;
        const description = document.querySelector('#description').value;
  
        // You can perform additional validation here
  
        // Clear form fields
        document.querySelector('#businessName').value = '';
        document.querySelector('#businessLocation').value = '';
        document.querySelector('#phoneNumber').value = '';
        document.querySelector('#numRooms').value = '';
        document.querySelector('#boardingHouseImages').value = '';
        document.querySelector('#licensePermitImage').value = '';
        document.querySelector('#description').value = '';
  
        // Close the modal
        uploadModal.hide();
  
        // You can then process the form data, for example, send it to the server via AJAX
      });
    });
    document.addEventListener('DOMContentLoaded', function () {
      const uploadForm = document.getElementById('uploadForm');
      const successModal = new bootstrap.Modal(document.getElementById('successModal'));
      const uploadModal = new bootstrap.Modal(document.getElementById('uploadModal'));
  
      uploadForm.addEventListener('submit', function (e) {
          e.preventDefault();
  
          const formData = new FormData(uploadForm);
  
          // AJAX request
          fetch('save_boarding_house.php', {
              method: 'POST',
              body: formData
          })
          .then(response => {
              if (response.ok) {
                  return response.text();
              } else {
                  throw new Error('Error in form submission');
              }
          })
          .then(data => {
              // Handle success response
              console.log(data);
              // Show success modal
              successModal.show();
              // Close the upload modal
              uploadModal.hide();
              
          })
          .catch(error => {
              console.error('Error:', error);
          });
      });
  
      // Add event listener to success modal shown event
      successModal.addEventListener('shown.bs.modal', function () {
          // Close the upload modal
          uploadModal.hide();
          
      });
  });



  // JavaScript function to show the logout modal
  function showLogoutModal() {
      // Get the logout modal element
      const logoutModal = new bootstrap.Modal(document.getElementById('logoutModal'));
      // Show the modal
      logoutModal.show();
  }
  
  // JavaScript function to close the logout modal
  function closeLogoutModal() {
    // Hide the modal using Bootstrap modal method
    const modal = document.getElementById('logoutModal');
    const bsModal = bootstrap.Modal.getInstance(modal);
    bsModal.hide();
}
  function showApprovedHousesModal() {
          // Get the Approved Houses modal element
          const approvedHousesModal = new bootstrap.Modal(document.getElementById('approvedHousesModal'));
          // Show the modal
          approvedHousesModal.show();
      }
      function closeApprovedHousesModal() {
        // Hide the modal using Bootstrap modal method
        const modal = document.getElementById('approvedHousesModal');
        const bsModal = bootstrap.Modal.getInstance(modal);
        bsModal.hide();
    }
    function showRoomModal() {
        // Get the Approved Houses modal element
        const roomModal = new bootstrap.Modal(document.getElementById('roomModal'));
        // Show the modal
        roomModal.show();
    }
    function closeRoomModal() {
        // Hide the modal using Bootstrap modal method
        const modal = document.getElementById('roomModal');
        const bsModal = bootstrap.Modal.getInstance(modal);
        bsModal.hide();
    }



    document.addEventListener('DOMContentLoaded', function () {
      const uploadForm = document.getElementById('addRoomForm');
      const successModal = new bootstrap.Modal(document.getElementById('roomsuccessModal'));
      const uploadModal = new bootstrap.Modal(document.getElementById('roomModal'));
  
      uploadForm.addEventListener('submit', function (e) {
          e.preventDefault();
  
          const formData = new FormData(addRoomForm);
  
          // AJAX request
          fetch('save_room.php', {
              method: 'POST',
              body: formData
          })
          .then(response => {
              if (response.ok) {
                  return response.text();
              } else {
                  throw new Error('Error in form submission');
              }
          })
          .then(data => {
              // Handle success response
              console.log(data);
              // Show success modal
              roomsuccessModal.show();
              // Close the upload modal
              roomModal.hide();
              
          })
          .catch(error => {
              console.error('Error:', error);
          });
      });
  
      // Add event listener to success modal shown event
      roomsuccessModal.addEventListener('shown.bs.modal', function () {
          // Close the upload modal
          roomModal.hide();
          
      });
  });

   