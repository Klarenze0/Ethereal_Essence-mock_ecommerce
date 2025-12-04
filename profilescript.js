// Get references to the buttons and elements
const editProfileButton = document.getElementById('editProfileButton');
const saveChangesButton = document.getElementById('saveChangesButton');
const cancelButton = document.getElementById('cancelButton');
const saveCancelBtns = document.getElementById('saveCancelBtns');

// File input and profile image
const fileInput = document.getElementById('uploadFile');
const profileImage = document.getElementById('profileImage');
const uploadLabel = document.getElementById('uploadLabel');

// Profile fields (static and editable)
const profileFields = {
    username: document.getElementById('username'),
    email: document.getElementById('email'),
    phone: document.getElementById('phone'),
    firstName: document.getElementById('firstName'),
    lastName: document.getElementById('lastName'),
    address: document.getElementById('address'),
};

const profileEditFields = {
    emailEdit: document.getElementById('emailEdit'),
    phoneEdit: document.getElementById('phoneEdit'),
    firstNameEdit: document.getElementById('firstNameEdit'),
    lastNameEdit: document.getElementById('lastNameEdit'),
    addressEdit: document.getElementById('addressEdit'),
};


// Event listener for file input to change profile image
fileInput.addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            profileImage.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// Handle "Edit Profile" button click
editProfileButton.addEventListener('click', () => {
    document.querySelector('.profile-info').classList.add('editable');

    // Hide static text and show editable fields for each field except username
    for (const field in profileFields) {
        if (field !== 'username') {
            profileFields[field].style.display = 'none';
            profileEditFields[field + 'Edit'].style.display = 'block';
        }
    }

    // Show Save/Cancel buttons and hide Edit button
    saveCancelBtns.style.display = 'block';
    editProfileButton.style.display = 'none';

    // Show the Upload button (file input and label) after clicking Edit Profile
    uploadLabel.style.display = 'block'; // Show the label
    fileInput.style.display = 'block'; // Show the file input itself

    const bg = document.getElementById("containerbg");

    bg.style.height = "1400px";
});


// Handle "Save Changes" button click
saveChangesButton.addEventListener('click', () => {
    // Save changes to the profile fields
    profileFields.email.innerText = profileEditFields.emailEdit.value;
    profileFields.phone.innerText = profileEditFields.phoneEdit.value;
    profileFields.firstName.innerText = profileEditFields.firstNameEdit.value;
    profileFields.lastName.innerText = profileEditFields.lastNameEdit.value;
    profileFields.address.innerText = profileEditFields.addressEdit.value;

    // Hide input fields and show static text
    for (const field in profileFields) {
        if (field !== 'username') {
            profileFields[field].style.display = 'block';  // Show static text
            profileEditFields[field + 'Edit'].style.display = 'none';  // Hide input field
        }
    }

    // Hide Save/Cancel buttons and show Edit button
    saveCancelBtns.style.display = 'none';
    editProfileButton.style.display = 'block';

    // Remove editable class
    document.querySelector('.profile-info').classList.remove('editable');

    // Hide the Upload button after saving
    uploadLabel.style.display = 'none'; // Hide the label
    fileInput.style.display = 'none'; // Hide the file input
});


// Handle "Cancel" button click
cancelButton.addEventListener('click', () => {
    // Reset the profile edit fields to original values (from the static profile fields)
    profileEditFields.emailEdit.value = profileFields.email.innerText;
    profileEditFields.phoneEdit.value = profileFields.phone.innerText;
    profileEditFields.firstNameEdit.value = profileFields.firstName.innerText;
    profileEditFields.lastNameEdit.value = profileFields.lastName.innerText;

    // Hide input fields and show static text again
    for (const field in profileFields) {
        if (field !== 'username') {
            profileFields[field].style.display = 'block'; // Show static text
            profileEditFields[field + 'Edit'].style.display = 'none'; // Hide input field
        }
    }

    // Hide Save/Cancel buttons and show Edit button
    saveCancelBtns.style.display = 'none';
    editProfileButton.style.display = 'block';

    // Remove editable class
    document.querySelector('.profile-info').classList.remove('editable');

    // Hide the Upload button and reset the file input
    uploadLabel.style.display = 'none'; // Hide the label
    fileInput.style.display = 'none'; // Hide the file input

    // Reset the image to the original (if you need to reset the image to the default one)
    // Optionally, you could keep the original image if needed, or reset it like so:
    // profileImage.src = 'path_to_default_image.jpg'; // Uncomment if you want to reset the image

});

