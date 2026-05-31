<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
if(isset($_SESSION['logout_msg'])){
    echo "<div id='logoutMsg' class='logout-msg'>".$_SESSION['logout_msg']."</div>";
    unset($_SESSION['logout_msg']);
}
?>
<!DOCTYPE html>
<html lang="en">
    
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<html lang="en">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<title>Artisan & Handmade Products</title> 
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: Arial, sans-serif; }
body { background-color: #ebebc6; color: #3e2723; }

/* ❌ REMOVE OLD NAV STYLE EFFECT (keep rest same) */
body { margin-top:70px; }
.logout-msg{
    background:#dff0d8;
    color:#3c763d;
    padding:12px;
    text-align:center;
    font-weight:bold;
    border-radius:6px;
    margin:10px;
}
.hero { min-height: 100vh; display: flex; padding-top: 70px; padding-bottom: 30px; }
.hero-content { width: 40%; display: flex; flex-direction: column; justify-content: center; padding-left: 2%; }
.main-heading { display: flex; flex-direction: column; gap: 5px; margin-bottom: 20px; }
.small-text { font-size: 3.8rem; font-weight: 700; }
.big-text { font-size: 3.8rem; font-weight: 700; line-height: 1.05; white-space: nowrap; }
.hero-content p { font-size: 1.2rem; max-width: 420px; margin-bottom: 2rem; }
.btn { background-color: #b37d5c; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; width: fit-content; }
.video-container { width: 60%; display: flex; justify-content: center; align-items: center; }
.video-box { width: 100%; max-width: 800px; aspect-ratio: 4 / 3; position: relative; overflow: hidden; border-radius: 20px; box-shadow: 0 25px 60px rgba(0,0,0,0.15); }
.video-box video { width: 100%; height: 100%; object-fit: cover; }
.mute-btn { position: absolute; top: 20px; right: 20px; background: rgba(0,0,0,0.6); color: white; border: none; font-size: 18px; padding: 8px 12px; border-radius: 6px; cursor: pointer; }
.video-controls { position: absolute; bottom: 25px; left: 50%; transform: translateX(-50%); display: flex; gap: 30px; }
.video-controls button { background: rgba(0, 0, 0, 0.6); color: white; border: none; font-size: 26px; padding: 10px 18px; cursor: pointer; border-radius: 8px; }
footer { background-color: #3e2723; color: white; text-align: center; padding: 1.5rem; }

@media (max-width: 768px) {
    .hero { flex-direction: column; min-height: auto; }
    .hero-content, .video-container { width: 100%; padding: 40px 8%; }
    .big-text { font-size: 2.5rem; }
    .video-box { max-width: 95%; }
}

/* WHY SECTION */
.why-section {
    padding: 50px 5%;
    text-align: center;
    background-color: #ebebc6;
}

.why-section h2 {
    font-size: 2.5rem;
    margin-bottom: 30px;
    color: #3e2723;
}

.containers {
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
}

.container {
    width: 400px;
    height: 450px;
    background-color: #f5f5dc;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: transform 0.3s, box-shadow 0.3s;
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    position: relative;
    overflow: hidden;
}

.container:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 16px rgba(0,0,0,0.2);
}

.container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    top: 0;
    left: 0;
    transition: opacity 0.3s;
}

.container .default-img { filter: blur(1px); }
.container .hover-img { opacity: 0; }

.container:hover .default-img { opacity: 0; }
.container:hover .hover-img { opacity: 1; }

.container p {
    font-size: 1rem;
    color: #3e2723;
    text-align: center;
    position: relative;
    z-index: 1;
    background: rgba(245, 245, 220, 0.8);
    padding: 5px;
    border-radius: 5px;
}

/* MODAL support artisans and handmade product system 
 */
.modal {
    display: none;
    position: fixed;
    z-index: 2000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
}

.modal-content {
    width: 500px;
    height: 600px;
    border-radius: 20px;
    padding: 20px;
    position: relative;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-y: auto;
    background-size: cover;
}

.close {
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 28px;
    cursor: pointer;
}

#modal-body {
    text-align: center;
    background: rgba(245,245,220,0.9);
    padding: 15px;
    border-radius: 10px;
    margin-top: auto;
}
</style>
</head>

<body>

<!-- ✅ ONLY ADD THIS PRODUCT WISHLIST ORIGINATED IN INDIA FOR SUPPORTING ARTISANS AND LOCAL PUBLIC 
        SO THAT THEY CAN EARN MORE COMMISSION THROUGH OUR WEBSITE MAHARASHTRA IS FAMOUS FOR POITERY AND MITTI PRODUCT 
        RAJASTHAN IS FAMOUS FOR HAND EMBROIDERY LIKE JUTTI BANGLES 
        STAES LIKE NORTHEAST SISTER ASSAM, NAGALAND, ETC & UTHARAKHAND ARE FAMOUS FOR SUCH PRODUCT LIKE JUTTI, POTS, PLATES, BASKET, ETC 
        1. Reduce Manual Errors: Minimize human intervention and errors in the booking and payment process
        •	Support artisans and small-scale craft sellers by increasing product visibility. 
•	Improve customer experience with a simple and user-friendly website interface. 
•	Promote appreciation of handmade products and traditional craftsmanship. 
•	Provide organized product information for better browsing and product management
  PAYMENT METHOD LIKE UPI NET BANKING ARE USED FOR EASY GOING PAYMENT SYSTEM .
  COMFIRMATION PAGE DISPLAY ALL THE DETAILS LIKE PRODUCT ID PRODUCT NAME -->
<?php include("navbar.php"); ?>

<section class="hero">
    <div class="hero-content">
        <h1 class="main-heading">
            <span class="small-text">Welcome to</span>
            <span class="big-text">Artisan & Handmade</span>
            <span class="big-text">Products</span>
        </h1>
        <p>Discover unique, handcrafted goods from skilled artisans around the world.</p>
        <a href="20_explore.php" class="btn">Explore More</a>
    </div>

    <div class="video-container">
        <div class="video-box">
            <video id="heroVideo" autoplay muted></video>
            <button id="muteBtn" class="mute-btn">🔇</button>
            <div class="video-controls">
                <button id="prevBtn">&lt;</button>
                <button id="nextBtn">&gt;</button>
            </div>
        </div>
    </div>
</section>

<section class="why-section">
    <h2>Why Handmade Products</h2>
    <div class="containers">

        <div class="container" id="container1">
            <img src="image/i1.jpg" class="default-img">
            <img src="image/i2.jpg" class="hover-img">
            <p>Unique and personalized items.</p>
        </div>

        <div class="container" id="container2">
            <img src="image/i3.jpg" class="default-img">
            <img src="image/i4.jpg" class="hover-img">
            <p>Supports local artisans.</p>
        </div>

        <div class="container" id="container3">
            <img src="image/i5.jpg" class="default-img">
            <img src="image/i6.jpg" class="hover-img">
            <p>Eco-friendly and sustainable.</p>
        </div>

    </div>
</section>

<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <div id="modal-body"></div>
    </div>
</div>

<footer>Artisan & Homemade Products</footer>

<!--✅YOUR SCRIPT 100% UNCHANGED unchanged the code python code why the name-->
<script>
    
setTimeout(function() {
    var msg = document.getElementById("logoutMsg");
    if(msg){
        msg.style.display = "none";
    }
}, 3000); // 10 seconds

const video = document.getElementById("heroVideo");
const prevBtn = document.getElementById("prevBtn");
const nextBtn = document.getElementById("nextBtn");
const muteBtn = document.getElementById("muteBtn");

const videoSources = ["video/v5.mp4", "video/video2.mp4"];
let currentVideoIndex = 0;

function setVideo(index) {
    currentVideoIndex = index;
    video.src = videoSources[currentVideoIndex];
    video.play();
}
setVideo(currentVideoIndex);

nextBtn.addEventListener("click", () => setVideo((currentVideoIndex + 1) % videoSources.length));
prevBtn.addEventListener("click", () => setVideo((currentVideoIndex - 1 + videoSources.length) % videoSources.length));
video.addEventListener("ended", () => setVideo((currentVideoIndex + 1) % videoSources.length));
muteBtn.addEventListener("click", () => {
    video.muted = !video.muted;
    muteBtn.textContent = video.muted ? "🔇" : "🔊";
});
const modal = document.getElementById("modal");
const modalContent = document.querySelector(".modal-content");
const modalBody = document.getElementById("modal-body");
const closeBtn = document.querySelector(".close");

const contentData = {
    container1: {
        bgImage: "image/i1.jpg",
        title: "Unique and Personalized",
        description: "Handmade products are one-of-a-kind, crafted with care to reflect individual styles and preferences. Unlike mass-produced items, each piece tells a story and adds a personal touch to your life."
    },
    container2: {
        bgImage: "image/i3.jpg",
        title: "Supports Local Artisans",
        description: "By choosing handmade, you empower skilled artisans and small businesses."
    },
    container3: {
        bgImage: "image/i5.jpg",
        title: "Eco-Friendly and Sustainable",
        description: "Handmade goods often use natural, recycled materials and reduce waste."
    }
};

document.querySelectorAll('.container').forEach(container => {
    container.addEventListener('click', () => {
        const id = container.id;
        const data = contentData[id];
        modalContent.style.backgroundImage = `url(${data.bgImage})`;
        modalBody.innerHTML = `
            <h3>${data.title}</h3>
            <p>${data.description}</p>
        `;
        modal.style.display = "flex";
    });
});

closeBtn.addEventListener('click', () => {
    modal.style.display = "none";
    modalContent.style.backgroundImage = "";
});

window.addEventListener('click', (event) => {
    if (event.target === modal) {
        modal.style.display = "none";
        modalContent.style.backgroundImage = "";
    }
});

</script>

</body>
</html>