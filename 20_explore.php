<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<title>Explore - Artisan & Handmade Products</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Georgia', serif; }
body { background-color: #ebebc6; color: #3e2723; line-height: 1.6; overflow-x: hidden; }
body { margin-top:70px; }

/* Hero Section */
.hero-explore {
    min-height: 70vh;
    background: linear-gradient(135deg, rgba(62,39,35,0.8), rgba(179,125,92,0.6)), 
                url('https://images.unsplash.com/photo-1574169208507-84376144848b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1400&q=80') center/cover;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    position: relative;
}

.hero-content h1 {
    font-size: 4.5rem;
    font-weight: 700;
    color: white;
    text-shadow: 0 4px 20px rgba(0,0,0,0.5);
    margin-bottom: 20px;
    opacity: 0;
    transform: translateY(40px);
    animation: heroFadeIn 1.2s 0.3s forwards;
}

.hero-content p {
    font-size: 1.4rem;
    color: #f5f5dc;
    max-width: 600px;
    margin: 0 auto;
    opacity: 0;
    transform: translateY(30px);
    animation: heroFadeIn 1.2s 0.6s forwards;
}

@keyframes heroFadeIn {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Gallery Section */
.gallery-section {
    padding: 80px 5% 120px;
    max-width: 1400px;
    margin: 0 auto;
}

.gallery-title {
    text-align: center;
    font-size: 3.5rem;
    margin-bottom: 60px;
    position: relative;
}

.gallery-title::after {
    content: '';
    position: absolute;
    bottom: -15px;
    left: 50%;
    transform: translateX(-50%);
    width: 120px;
    height: 4px;
    background: linear-gradient(90deg, #b37d5c, #8b4513);
    border-radius: 2px;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    grid-auto-rows: 250px;
    grid-gap: 25px;
    padding: 40px 0;
}

.gallery-item {
    position: relative;
    overflow: hidden;
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    opacity: 0;
    transform: scale(0.9) translateY(50px);
    transition: all 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    cursor: pointer;
}

.gallery-item.animate {
    opacity: 1;
    transform: scale(1) translateY(0);
}

.gallery-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.gallery-item:hover img {
    transform: scale(1.1);
}

.gallery-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(62,39,35,0.9));
    color: white;
    padding: 30px 25px 25px;
    transform: translateY(100%);
    transition: transform 0.4s ease;
}

.gallery-item:hover .gallery-overlay {
    transform: translateY(0);
}

.gallery-title-item {
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 5px;
    line-height: 1.3;
}

/* Lightbox Modal */
.lightbox {
    display: none;
    position: fixed;
    z-index: 3000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.95);
    justify-content: center;
    align-items: center;
}

.lightbox-content {
    max-width: 90%;
    max-height: 90%;
    position: relative;
    border-radius: 20px;
    overflow: hidden;
    transform: scale(0.7);
    transition: transform 0.3s ease;
}

.lightbox.show .lightbox-content {
    transform: scale(1);
}

.lightbox img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.lightbox-close {
    position: absolute;
    top: 25px;
    right: 30px;
    color: white;
    font-size: 35px;
    cursor: pointer;
    z-index: 10;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(179,125,92,0.3);
    transition: all 0.3s ease;
}

.lightbox-close:hover {
    background: #b37d5c;
    transform: scale(1.1);
}

.lightbox-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(179,125,92,0.8);
    color: white;
    border: none;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.lightbox-nav:hover {
    background: #b37d5c;
    transform: translateY(-50%) scale(1.1);
}

.prev-btn { left: 30px; }
.next-btn { right: 30px; }

/* Load More Button */
.load-more {
    display: block;
    margin: 80px auto 0;
    background: linear-gradient(135deg, #b37d5c, #8b4513);
    color: white;
    border: none;
    padding: 18px 50px;
    border-radius: 50px;
    font-size: 1.2rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    opacity: 0;
    transform: translateY(30px);
}

.load-more.animate {
    opacity: 1;
    transform: translateY(0);
}

.load-more:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(179,125,92,0.4);
}

/* Responsive */
@media (max-width: 768px) {
    .hero-content h1 { font-size: 3rem; }
    .gallery-grid {
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        grid-auto-rows: 220px;
        grid-gap: 20px;
    }
}
</style>
</head>
<body>
<?php include("navbar.php"); ?>

<!-- Hero Section -->
<section class="hero-explore">
    <div class="hero-content">
        <div>
            <h1>Explore Our Collection</h1>
            <p>Discover handcrafted treasures from artisans around the world. Each piece is unique, made with love and tradition.</p>
        </div>
    </div>
</section>

<!-- Gallery Section -->
<section class="gallery-section">
    <h2 class="gallery-title">Handcrafted Masterpieces</h2>
    <div class="gallery-grid" id="galleryGrid">
        <!-- Gallery items will be populated by JavaScript -->
    </div>
    <button class="load-more" id="loadMore">Load More Treasures</button>
</section>

<!-- Lightbox Modal -->
<div class="lightbox" id="lightbox">
    <div class="lightbox-content">
        <span class="lightbox-close" id="lightboxClose">&times;</span>
        <button class="lightbox-nav prev-btn" id="prevBtn"><i class="fas fa-chevron-left"></i></button>
        <button class="lightbox-nav next-btn" id="nextBtn"><i class="fas fa-chevron-right"></i></button>
        <img id="lightboxImg" src="" alt="">
    </div>
</div>

<script>
const galleryData = [
    { 
        src: 'https://i.pinimg.com/1200x/1d/3e/ff/1d3efffebf1d529c2516a9c6e0363553.jpg', 
        title: 'Andean Weave Shawl', 
        size: 'tall' 
    },
    { 
        src: 'https://i.pinimg.com/736x/b6/4e/af/b64eafa4042fc811f576dbb881d5275f.jpg', 
        title: 'Silver Moon Necklace', 
        size: 'small' 
    },
    { 
        src: 'https://i.pinimg.com/736x/1a/bc/d1/1abcd106926b57ce13090e3166afa500.jpg', 
        title: 'Handturned Walnut Bowl', 
        size: 'normal' 
    },
    { 
        src: 'https://i.pinimg.com/736x/bd/5d/7f/bd5d7fe709490057b4229165bf63190b.jpg', 
        title: 'Terracotta Vase Set', 
        size: 'wide' 
    },
    { 
        src: 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80', 
        title: 'Embroidered Silk Cushion', 
        size: 'normal' 
    },
    { 
        src:'https://i.pinimg.com/1200x/f5/cd/71/f5cd71143f4ee216bb575e1c25ea890c.jpg', 
        title: 'Leather Artisan Wallet', 
        size: 'small' 
    },
    { 
        src: 'https://i.pinimg.com/736x/35/d3/d0/35d3d04a7094155b054b4a78385ee916.jpg', 
        title: 'Turquoise Bracelet', 
        size: 'tall' 
    },
    { 
        src: 'https://i.pinimg.com/736x/d2/17/8c/d2178cb0e0c97a6762b5a7609cc57ed3.jpg', 
        title: 'Rustic Ceramic Mug', 
        size: 'normal' 
    },
    { 
        src: 'https://i.pinimg.com/736x/22/50/53/2250530de2ed1c081db7815ba6feda18.jpg', 
        title: 'Handwoven Basket', 
        size: 'wide' 
    },
    { 
        src: 'https://i.pinimg.com/736x/a3/27/73/a32773f5e3e32dc65e6c42b3a57d3a69.jpg', 
        title: 'Carved Teak Box', 
        size: 'normal' 
    },
    { 
        src: 'https://i.pinimg.com/1200x/97/0a/aa/970aaa94d54dd51273e880b22de51fc2.jpg', 
        title: 'Moroccan Leather Bag', 
        size: 'tall' 
    },
    { 
        src: 'https://i.pinimg.com/736x/52/44/9c/52449c60f8b96aa74446dc462c9fc8fd.jpg', 
        title: 'Pearl Earrings', 
        size: 'small' 
    },
    { 
        src: 'https://i.pinimg.com/736x/23/b8/fd/23b8fd8e33641a17611f9d90607baa22.jpg', 
        title: 'Hand Painted Plate', 
        size: 'normal' 
    },
    { 
        src: 'https://i.pinimg.com/1200x/9b/c0/8f/9bc08f62fe8e53c03733a08e44877219.jpg', 
        title: 'Macrame Wall Hanging', 
        size: 'wide' 
    },
    { 
        src: 'https://i.pinimg.com/1200x/19/29/81/19298125a8ece78e724d1176e92198f5.jpg', 
        title: 'Wooden Sculpture', 
        size: 'tall' 
    }
];

let currentItems = [...galleryData];
let currentIndex = 0;
let lightboxIndex = 0;

function createGalleryItem(item, index) {
    const sizeClass = item.size === 'tall' ? 'grid-row-span-2' : 
                     item.size === 'wide' ? 'grid-column-span-2' : '';
    
    return `
        <div class="gallery-item ${sizeClass}" data-index="${index}">
            <img src="${item.src}" alt="${item.title}">
            <div class="gallery-overlay">
                <div class="gallery-title-item">${item.title}</div>
            </div>
        </div>
    `;
}

function renderGallery(items = currentItems) {
    const grid = document.getElementById('galleryGrid');
    grid.innerHTML = items.map((item, index) => createGalleryItem(item, index)).join('');
    
    // Animate items
    setTimeout(() => {
        document.querySelectorAll('.gallery-item').forEach((item, index) => {
            setTimeout(() => item.classList.add('animate'), index * 100);
        });
    }, 100);
}

// Load more functionality
document.getElementById('loadMore').addEventListener('click', () => {
    const btn = document.getElementById('loadMore');
    btn.textContent = 'Loading...';
    btn.style.opacity = '0.7';
    
    setTimeout(() => {
        // Add more items
        const newItems = galleryData.slice(0, 8);
        currentItems.push(...newItems);
        renderGallery();
        btn.textContent = 'Load More Treasures';
        btn.style.opacity = '1';
        btn.classList.add('animate');
    }, 1200);
});

// Lightbox functionality
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightboxImg');
const lightboxClose = document.getElementById('lightboxClose');
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');

document.addEventListener('click', (e) => {
    if (e.target.classList.contains('gallery-item')) {
        lightboxIndex = parseInt(e.target.closest('.gallery-item').dataset.index);
        showLightbox();
    }
});

function showLightbox() {
    const item = galleryData[lightboxIndex % galleryData.length];
    lightboxImg.src = item.src;
    lightboxImg.alt = item.title;
    lightbox.classList.add('show');
    document.body.style.overflow = 'hidden';
}

lightboxClose.addEventListener('click', hideLightbox);
lightbox.addEventListener('click', (e) => {
    if (e.target === lightbox) hideLightbox();
});

function hideLightbox() {
    lightbox.classList.remove('show');
    document.body.style.overflow = 'auto';
}

prevBtn.addEventListener('click', () => {
    lightboxIndex = (lightboxIndex - 1 + galleryData.length) % galleryData.length;
    showLightbox();
});

nextBtn.addEventListener('click', () => {
    lightboxIndex = (lightboxIndex + 1) % galleryData.length;
    showLightbox();
});

// Keyboard navigation
document.addEventListener('keydown', (e) => {
    if (!lightbox.classList.contains('show')) return;
    
    if (e.key === 'Escape') hideLightbox();
    if (e.key === 'ArrowLeft') prevBtn.click();
    if (e.key === 'ArrowRight') nextBtn.click();
});

// Initialize
window.addEventListener('load', () => {
    renderGallery();
    document.getElementById('loadMore').classList.add('animate');
});
</script>

<footer style="background-color: #3e2723; color: white; text-align: center; padding: 2rem;">
    <p>Discover more artisan treasures every day ✨</p>
</footer>

</body>
</html>