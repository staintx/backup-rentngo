window.addEventListener('scroll', ()=>{
    document.querySelector('.n1-container').classList.toggle("scrolling", scrollY > 40)
})

const menuBtn = document.querySelector('.fa-bars');
const navList = document.querySelector('.navlist');

menuBtn.onclick = function(){
    menuBtn.classList.toggle('fa-xmark');
    navList.classList.toggle('active');
}


//==========================SCROLL REVEAL===================================

const sr = ScrollReveal({
    distance:'60px',
    duration: 2500,
    delay: 400,
    reset: true,
})

sr.reveal('.nav-top', {delay: 222, origin: 'top'})
sr.reveal('.home .info', {delay: 444, origin: 'bottom'})
sr.reveal('.info .booking-form', {delay: 999, origin: 'right'})
sr.reveal('.works', {delay: 600, origin: 'top'})
sr.reveal('.about-container .image', {delay: 322, origin: 'left'})
sr.reveal('.about-container .content', {delay: 322, origin: 'right'})
sr.reveal('.services .title', {delay: 322, origin: 'top'})
sr.reveal('.services-container', {delay: 322, origin: 'bottom'})
sr.reveal('.cars-container-dets', {delay: 800, origin: 'bottom'})
sr.reveal('.blog-container-bl', {delay: 322, origin: 'left'})
sr.reveal('footer', {delay: 600, origin: 'top'})

