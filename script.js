let items = document.querySelectorAll('.favoritesec .items');
let next = document.getElementById('next');
let prev = document.getElementById('prev');
const btn = document.getElementById('discoverbtn');

let active = 3;

function loadshow(){
    let stt = 0;
    items[active].style.transform = `none`;
    items[active].style.zIndex = '1';
    items[active].style.filter = 'none';
    items[active].style.borderRadius = '50px';
    items[active].style.opacity = 1;
    for(var i = active + 1; i < items.length; i++){
        stt++;
        items[i].style.transform = `translateX(${200 * stt}px) scale(${1 - 0.2*stt}) perspective(16px) rotateY(-0deg) `;
        items[i].style.zIndex = -stt;
        items[i].style.filter = 'blur(5px)';
        items[i].style.borderRadius = '50px';
        items[i].style.opacity = stt > 2 ? 0 : 0.8;
    }
     stt = 0;
     for(var i = active - 1; i >= 0; i--){
        stt++;
        items[i].style.transform = `translateX(${-200 * stt}px) scale(${1 - 0.2*stt}) perspective(16px) rotateY(-0deg) `;
        items[i].style.zIndex = -stt;
        items[i].style.filter = 'blur(5px)';
        items[i].style.borderRadius = '50px';
        items[i].style.opacity = stt > 2 ? 0 : 0.8;
     }
}

loadshow();

next.onclick = function() {
    active = (active + 1) % items.length; // Increment and wrap around to the start
    loadshow();
}

prev.onclick = function() {
    active = (active - 1 + items.length) % items.length; // Decrement and wrap around to the end
    loadshow();
}

function discover() {
    console.log("asdas");
    window.scrollBy({
        top: -window.scrollY,
        behavior: 'smooth'
    });
}


function sendMail(){

    let params = {
        name: document.getElementById("name").value,
        email: document.getElementById("email").value,
        message: document.getElementById("tarea").value
    };

    emailjs.send("service_ftjavun", "template_s83iarh", params)
    .then(function(response) {
        console.log('Email sent successfully:', response);
        alert("Email Sent");
    }, function(error) {
        console.error('Failed to send email:', error);
        alert("Failed to send email");
    });
}