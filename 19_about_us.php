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
<title>About Us - Artisan & Handmade Products</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Georgia', serif; }
body { background-color: #ebebc6; color: #3e2723; line-height: 1.6; overflow-x: hidden; }
body { margin-top:70px; }

/* Hero Section */
.hero-about {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f5dc 0%, #ebebc6 100%);
    display: flex;
    align-items: center;
    padding: 0 5%;
    position: relative;
    overflow: hidden;
}

.hero-content {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 80px;
    align-items: center;
}

.hero-text h1 {
    font-size: 4.5rem;
    font-weight: 700;
    line-height: 1.1;
    margin-bottom: 20px;
    background: linear-gradient(45deg, #b37d5c, #8b4513);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    opacity: 0;
    transform: translateY(50px);
    animation: fadeInUp 1s 0.5s forwards;
}

.hero-text p {
    font-size: 1.3rem;
    margin-bottom: 30px;
    opacity: 0;
    transform: translateY(30px);
    animation: fadeInUp 1s 0.8s forwards;
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.hero-image {
    position: relative;
    opacity: 0;
    transform: scale(0.8);
    animation: fadeInScale 1.5s 1s forwards;
}

@keyframes fadeInScale {
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.hero-image img {
    width: 100%;
    height: 500px;
    object-fit: cover;
    border-radius: 25px;
    box-shadow: 0 30px 80px rgba(62, 39, 35, 0.2);
    border: 8px solid #f5f5dc;
}

/* Story Section */
.story-section {
    padding: 120px 5%;
    background: #f5f5dc;
    position: relative;
}

.story-container {
    max-width: 1200px;
    margin: 0 auto;
    text-align: center;
}

.story-title {
    font-size: 3.5rem;
    margin-bottom: 20px;
    position: relative;
    display: inline-block;
}

.story-title::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, #b37d5c, #8b4513);
    border-radius: 2px;
}

.story-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 60px;
    margin-top: 80px;
}

.story-card {
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 40px 30px;
    border: 1px solid rgba(255,255,255,0.2);
    position: relative;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    opacity: 0;
    transform: translateY(50px);
}

.story-card.animate {
    opacity: 1;
    transform: translateY(0);
}

.story-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.6s;
}

.story-card:hover::before {
    left: 100%;
}

.story-card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 25px 60px rgba(62, 39, 35, 0.2);
}

.story-icon {
    font-size: 3.5rem;
    color: #b37d5c;
    margin-bottom: 20px;
    display: block;
}

.story-card h3 {
    font-size: 1.8rem;
    margin-bottom: 15px;
    color: #3e2723;
}

/* Timeline Section */
.timeline-section {
    padding: 120px 5%;
    background: #ebebc6;
}

.timeline-container {
    max-width: 1000px;
    margin: 0 auto;
    position: relative;
}

.timeline-title {
    text-align: center;
    font-size: 3rem;
    margin-bottom: 100px;
}

.timeline {
    position: relative;
    padding-left: 40px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 20px;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, #b37d5c, #8b4513);
    border-radius: 2px;
}

.timeline-item {
    position: relative;
    margin-bottom: 80px;
    opacity: 0;
    transform: translateX(-50px);
}

.timeline-item.animate {
    opacity: 1;
    transform: translateX(0);
}

.timeline-item::before {
    content: '';
    position: absolute;
    left: -60px;
    top: 25px;
    width: 20px;
    height: 20px;
    background: #b37d5c;
    border-radius: 50%;
    border: 4px solid #ebebc6;
    z-index: 1;
    transition: all 0.3s ease;
}

.timeline-item:hover::before {
    transform: scale(1.3);
    box-shadow: 0 0 20px rgba(179, 125, 92, 0.5);
}

.timeline-content {
    background: #f5f5dc;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    border-left: 4px solid #b37d5c;
    position: relative;
}

/* Team Section */
.team-section {
    padding: 120px 5%;
    background: linear-gradient(135deg, #f5f5dc 0%, #ebebc6 100%);
}

.team-grid {
    max-width: 1200px;
    margin: 80px auto 0;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 40px;
}

.team-card {
    text-align: center;
    position: relative;
    opacity: 0;
    transform: translateY(50px);
    transition: all 0.6s ease;
}

.team-card.animate {
    opacity: 1;
    transform: translateY(0);
}

.team-photo {
    width: 200px;
    height: 200px;
    border-radius: 50%;
    margin: 0 auto 25px;
    overflow: hidden;
    position: relative;
    border: 5px solid #f5f5dc;
    transition: all 0.4s ease;
}

.team-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.team-card:hover .team-photo {
    transform: scale(1.1);
    box-shadow: 0 20px 50px rgba(62, 39, 35, 0.3);
}

.team-name {
    font-size: 1.6rem;
    font-weight: 600;
    margin-bottom: 8px;
    color: #3e2723;
}

.team-role {
    color: #b37d5c;
    font-size: 1.1rem;
    margin-bottom: 15px;
}

/* Stats Section */
.stats-section {
    padding: 80px 5%;
    background: #3e2723;
    color: white;
}

.stats-grid {
    max-width: 1200px;
    margin: 0 auto;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 40px;
    text-align: center;
}

.stat-item h3 {
    font-size: 3.5rem;
    font-weight: 700;
    color: #b37d5c;
    margin-bottom: 10px;
    opacity: 0;
    transform: translateY(30px);
    transition: all 1s ease;
}

.stat-item.animate h3 {
    opacity: 1;
    transform: translateY(0);
}

.stat-item p {
    font-size: 1.2rem;
    opacity: 0.9;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-content {
        grid-template-columns: 1fr;
        gap: 40px;
        text-align: center;
    }
    
    .hero-text h1 {
        font-size: 3rem;
    }
    
    .story-grid, .team-grid {
        grid-template-columns: 1fr;
        gap: 30px;
    }
    
    .timeline {
        padding-left: 20px;
    }
    
    .timeline-item::before {
        left: -40px;
    }
}
</style>
</head>
<body>
<?php include("navbar.php"); ?>

<!-- Hero Section -->
<section class="hero-about">
    <div class="hero-content">
        <div class="hero-text">
            <h1>Our Story of<br>Handcrafted Excellence</h1>
            <p>From humble beginnings to a global community of artisans, we're passionate about preserving traditional craftsmanship and bringing unique, soulful creations to your doorstep.</p>
        </div>
        <div class="hero-image">
            <img src="image/i1.jpg" alt="Artisan at work">
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="story-section">
    <div class="story-container">
        <h2 class="story-title">Why We Exist</h2>
        <div class="story-grid">
            <div class="story-card">
                <i class="fas fa-heart story-icon"></i>
                <h3>Passion for Craftsmanship</h3>
                <p>Every piece tells a story of dedication, skill, and love for the art of handmade creation.</p>
            </div>
            <div class="story-card">
                <i class="fas fa-globe story-icon"></i>
                <h3>Global Artisan Network</h3>
                <p>Connecting talented makers from every corner of the world with those who appreciate true artistry.</p>
            </div>
            <div class="story-card">
                <i class="fas fa-leaf story-icon"></i>
                <h3>Sustainable Practices</h3>
                <p>Committed to ethical sourcing, eco-friendly materials, and supporting fair trade practices.</p>
            </div>
        </div>
    </div>
</section>

<!-- Timeline Section -->
<section class="timeline-section">
    <div class="timeline-container">
        <h2 class="timeline-title">Our Journey</h2>
        <div class="timeline">
            <div class="timeline-item">
                <div class="timeline-content">
                    <h3>2018</h3>
                    <p>Founded by artisans passionate about preserving traditional crafts in the digital age.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <h3>2020</h3>
                    <p>Expanded to 50+ countries, supporting 500+ artisans worldwide.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <h3>2023</h3>
                    <p>Delivered 100,000+ unique handmade pieces to customers globally.</p>
                </div>
            </div>
            <div class="timeline-item">
                <div class="timeline-content">
                    <h3>2024</h3>
                    <p>Launching artisan workshops and live craft demonstrations worldwide.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section">
    <div class="team-container" style="text-align: center; max-width: 1200px; margin: 0 auto;">
        <h2 style="font-size: 3rem; margin-bottom: 20px;">Meet Our Founders</h2>
        <p style="font-size: 1.2rem; color: #6b5e4f; max-width: 600px; margin: 0 auto 80px;">The passionate souls behind our mission</p>
    </div>
    <div class="team-grid">
        <div class="team-card">
            <div class="team-photo">
                <img src="image/i3.jpg" alt="Maria artisan">
            </div>
            <div>
                <h3 class="team-name">Maria Gonzalez</h3>
                <p class="team-role">Master Weaver & CEO</p>
                <p>20+ years perfecting traditional weaving techniques from her Andean village.</p>
            </div>
        </div>
        <div class="team-card">
            <div class="team-photo">
                <img src="image/i5.jpg" alt="Ahmed craftsman">
            </div>
            <div>
                <h3 class="team-name">Ahmed Khalil</h3>
                <p class="team-role">Wood Carver & COO</p>
                <p>Bringing ancient Egyptian woodwork mastery to modern design.</p>
            </div>
        </div>
        <div class="team-card">
            <div class="team-photo">
                <img src="image/i1.jpg" alt="Li Mei potter">
            </div>
            <div>
                <h3 class="team-name">Li Mei</h3>
                <p class="team-role">Ceramic Artist & Creative Director</p>
                <p>Transforming Chinese pottery traditions into contemporary art pieces.</p>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="stats-grid">
        <div class="stat-item">
            <h3>500+</h3>
            <p>Artisans Worldwide</p>
        </div>
        <div class="stat-item">
            <h3>50+</h3>
            <p>Countries Served</p>
        </div>
        <div class="stat-item">
            <h3>100K+</h3>
            <p>Happy Customers</p>
        </div>
        <div class="stat-item">
            <h3>5+</h3>
            <p>Years of Craft</p>
        </div>
    </div>
</section>

<script>
/* Scroll Animations */
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate');
        }
    });
}, observerOptions);

// Observe all animated elements
document.querySelectorAll('.story-card, .timeline-item, .team-card, .stat-item').forEach(el => {
    observer.observe(el);
});

// Stats counter animation
const stats = document.querySelectorAll('.stat-item h3');
const animateStats = () => {
    stats.forEach(stat => {
        const target = parseInt(stat.textContent.replace('+', ''));
        const increment = target / 100;
        let current = 0;
        
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                stat.textContent = target + '+';
                clearInterval(timer);
            } else {
                stat.textContent = Math.floor(current) + '+';
            }
        }, 30);
    });
};

const statsSection = document.querySelector('.stats-section');
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateStats();
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

statsObserver.observe(statsSection);

// Parallax effect for hero image
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const heroImage = document.querySelector('.hero-image');
    if (heroImage) {
        heroImage.style.transform = `translateY(${scrolled * 0.5}px)`;
    }
});
</script>

<footer style="background-color: #3e2723; color: white; text-align: center; padding: 2rem; margin-top: 50px;">
    <p>&copy; 2024 Artisan & Handmade Products. Crafted with ❤️ for creators worldwide.</p>
</footer>

</body>
</html>