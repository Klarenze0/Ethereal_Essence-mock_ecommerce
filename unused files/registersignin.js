const wrapper = document.querySelector('.wrapper');
const loginlink = document.querySelector('.login-link');
const registerlink = document.querySelector('.register-link');
const profile = document.querySelector('.container');
const closepopup = document.querySelector('.icon-close');

registerlink.addEventListener('click', ()=> { 

    wrapper.classList.add('active');
    const currentUrl = window.location.href.split('?')[0]; // Remove existing query params if any
    const newUrl = `${currentUrl}?wrapper=active`; // Add '?wrapper=active' to the URL
    history.pushState(null, '', newUrl);
});

loginlink.addEventListener('click', ()=> { 

    wrapper.classList.remove('active');
});

profile.addEventListener('click', ()=> { 

    wrapper.classList.add('active-popup');

});


window.addEventListener('load', () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('wrapper') && urlParams.get('wrapper') === 'active') {
        wrapper.classList.add('active'); // Add the 'active' class if the query parameter is present
    }
});


// document.addEventListener("DOMContentLoaded", () => {
//     const urlParams = new URLSearchParams(window.location.search);
//     const section = urlParams.get("section"); // Get the 'section' query parameter

//     if (section === "register") {
//         // Activate the Register section
//         const wrapper = document.querySelector('.wrapper');
//         wrapper.classList.add('active'); // Add the 'active' class to show Register section
//     }

//     const msg = urlParams.get("msg"); // Get the 'msg' query parameter
//     if (msg) {
//         const errorElement = document.getElementById("error");
//         if (errorElement) {
//             errorElement.textContent = decodeURIComponent(msg); // Display the error message
//             errorElement.style.display = "inline"; // Ensure it's visible
//         }
//     }
// });


const wrap = document.getElementById('wrapper');

function loaded(){
   wrap.style.transform = 'scale(1)';
}

