<?php
require 'db_conn.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "<script>
            alert('Please log in to update your information.');
            window.location.href = 'login.php';
          </script>";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kulturabase</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <body>
    <style>
    /* General */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f7f7f7;
            color: #4A4947;
            line-height: 1.6;
            padding-top: 80px;
        }
    </style>
    
    <!-- Navigation Bar -->
    <div class="navbar">
        <div style="display: flex; align-items: center;">
            <img src="https://scontent.xx.fbcdn.net/v/t1.15752-9/462567709_1724925585031052_4490126238712417040_n.png?_nc_cat=109&ccb=1-7&_nc_sid=0024fc&_nc_ohc=aXcrO29n7uIQ7kNvgHCi3nC&_nc_ad=z-m&_nc_cid=0&_nc_zt=23&_nc_ht=scontent.xx&oh=03_Q7cD1QEYs_r8YD6E0edmvQDXiy__0n-15fylEZhQIi5GI1RD2Q&oe=676A986A" alt="Kulturifiko Logo">
            <h1>Kulturabase</h1>
        </div>
        <div>
                <a href="home.php">Home</a>
                <a href="create-post.php">+ Create</a>
                <a href="explore.php" class="active">Explore</a>
                <a href="notification.php">Notification</a>
                <div class="dropdown">
                    <a href="#" class="dropdown-btn" onclick="toggleDropdown()">Menu</a>
                    <div class="dropdown-content">
                        <a href="profile.php">Profile</a>
                        <a href="settings.php">Settings</a>
                        <a href="#">Logout</a>
                    </div>
                </div>
                <a href="generate_report.php">Generate Report</a>
                <a href="login.php">Log In</a>
            </div>
    </div>

    <style>
    /* Navigation Bar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #365486;
            padding: 20px 40px;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar img {
            height: 50px;
            width: auto;
        }

        .navbar h1 {
            color: #DCF2F1;
            font-size: 2rem;
            font-weight: 600;
            margin-left: 10px;
        }

        .navbar a {
            color: #DCF2F1;
            text-decoration: none;
            margin: 0 15px;
            font-size: 1rem;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 30px;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .navbar a:hover {
            background-color: #7FC7D9;
            color: #0F1035;
        }

        .navbar a.active {
            background-color: #1e3c72;
            color: #fff;
        }
        
    /* Dropdown */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 150px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 4px;
        }

        .dropdown-content a {
            color: black;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #ddd;
        }

        .dropdown-content a:last-child {
            border-bottom: none;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

    /* Toggle class for show/hide */
        .show {
            display: block;
        }
    </style>

    <script>
        function toggleDropdown() {
            var dropdownContent = document.querySelector(".dropdown-content");
            dropdownContent.classList.toggle("show");
        }
    </script>

<!-- Search Section -->
<div class="search">
    <h1 id="category-heading">Cultural Database</h1> 
    <p>Your gateway to a world of cultural knowledge and discussions.</p>
    <div class="search-container">
        <div class="search-bar">
            <input type="text" placeholder="Search articles, topics, or discussions..." />
            <button>Search</button>
        </div>
    </div>
</div>

<style>
/* Search Section */
    .search {
        text-align: center;
        padding: 50px 20px;
        background: linear-gradient(45deg, #1e3c72, #2a5298)
    }

    .search h1 {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 10px;
        color: #fff;
    }

    .search p {
        font-size: 1rem;
        margin-bottom: 20px;
        color: #fff;
    }

/* Search Bar */
    .search-bar {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        border: 1px solid #ddd;
        border-radius: 50px;
        padding: 10px 20px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        width: 60%;
        margin: 0 auto;
    }

    .search-bar input {
        border: none;
        outline: none;
        font-size: 1rem;
        flex: 1;
        padding: 10px;
        border-radius: 50px;
    }

    .search-bar input::placeholder {
        color: #aaa;
    }

    .search-bar button {
        background-color: #000;
        color: #ffffff;
        border: none;
        padding: 10px 20px;
        border-radius: 50px;
        font-size: 1rem;
        cursor: pointer;
        transition: transform 0.3s ease;
    }

    .search-bar button:hover {
        transform: scale(1.05);
    }
</style>

<script>
const categories = document.querySelectorAll('.categories span');

// Add click event listener to each category span
categories.forEach(category => {
    category.addEventListener('click', function() {
        // Remove 'active' class from all categories
        categories.forEach(cat => cat.classList.remove('active'));
        // Add 'active' class to the clicked category
        this.classList.add('active');
    });
});
</script>

<!-- Hero Section -->
<section id="home" class="hero">
  <div class="container hero-container">
      <div class="hero-content">
        <h1>Experience the Culture</h1>
        <p>Explore the beauty of global traditions and connect with communities worldwide. Start your journey with Kulturifiko today.</p>
        <a href="explore.html" class="cta-btn explore-btn">Explore Now</a>
      </div>
      <br>
      <div class="hero-image">
        <img src="https://i.pinimg.com/736x/be/8c/6c/be8c6cbf1d049825ffd2df0442f0c66b.jpg" alt="Cultural Exploration">
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="about">
    <div class="container">
      <h2>Who We Are</h2>
      <p>We are a platform that celebrates cultural diversity, creating a space to share stories, events, and experiences that connect us all.</p>
    </div>
  </section>

  <!-- Features Section -->
  <section id="features" class="features">
    <div class="container">
      <h2>Our Features</h2>
      <div class="feature-grid">
        <div class="feature-card">
          <i class="icon fas fa-globe"></i>
          <h3>Global Community</h3>
          <p>Connect with like-minded people across the globe.</p>
        </div>
        <div class="feature-card">
          <i class="icon fas fa-calendar-alt"></i>
          <h3>Event Listings</h3>
          <p>Discover events near you or share your own.</p>
        </div>
        <div class="feature-card">
          <i class="icon fas fa-photo-video"></i>
          <h3>Interactive Media</h3>
          <p>Engage with cultural photos, videos, and more.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Gallery Section -->
  <section id="gallery" class="gallery-section">
    <div class="container">
      <h2>Gallery</h2>
      <p>Explore snapshots of cultural moments from around the world.</p>
      <div class="gallery-grid">
        <img src="https://i.pinimg.com/736x/76/b5/c2/76b5c226f33b37337934bce7ab9c0159.jpg" alt="Cultural Image 1">
        <img src="https://i.pinimg.com/736x/1d/c6/2f/1dc62ff8ecf9107fe08af2ca13b1a2f5.jpg" alt="Cultural Image 2">
        <img src="https://i.pinimg.com/736x/fd/d3/68/fdd36868ad02196d0f17c2bc2e022d43.jpg" alt="Cultural Image 3">
        <img src="https://i.pinimg.com/736x/ef/d2/64/efd264f714d553dda14755cb78034699.jpg" alt="Cultural Image 4">
      </div>
    </div>
  </section>  
  
  <!-- Partners Section -->
  <section id="partners" class="partners-section">
    <div class="container">
      <h2>Our Partners</h2>
      <p>We collaborate with these incredible organizations to bring cultures together.</p>
      <div class="partners-grid">
        <img src="https://i.pinimg.com/736x/08/5a/d4/085ad448933875d5c3f3da93bfaac820.jpg" alt="UNICEF Logo">
        <img src="https://i.pinimg.com/736x/3f/89/b2/3f89b281abd80b6d92cc131652f5ddfc.jpg" alt="Red Cross Logo">
        <img src="https://i.pinimg.com/736x/80/5b/b9/805bb99df768a69afe3b83b7d4e3b9a6.jpg" alt="Smithsonian Logo">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSPvtIXri6znQj6CQiMAj7HyYFcw9LnGdFv2g&s" alt="Cultural Survival Logo">
        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ2Sin1P5o24LYo6gbinywSWVoPo68INzkvRA&s" alt="WHO Logo">
        <img src="https://www.cdnlogo.com/logos/n/6/national-geographic-channel.svg" alt="National Geographic Logo">
      </div>
    </div>
  </section>   

  <style>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

/* Hero Section */
.hero {
  background-color: #f0f8ff;
  padding: 60px 20px;
}

.hero-container {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

.hero-content {
  flex: 1;
  max-width: 50%;
}

.hero-content h1 {
  font-size: 2.5rem;
  color: #003366;
  margin-bottom: 20px;
}

.hero-content p {
  font-size: 1.2rem;
  color: #555555;
  margin-bottom: 30px;
  line-height: 1.6;
}

.cta-btn {
  background-color: #003366;
  color: #ffffff;
  border: none;
  padding: 10px 20px;
  font-size: 1rem;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.cta-btn:hover {
  background-color: #00509e;
}

.hero-image {
  flex: 1;
  max-width: 50%;
  text-align: right;
}


/* Features Section */
.features {
  background-color: #f7fbff;
  padding: 40px 20px;
}

.feature-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
}

.feature-card {
  text-align: center;
  padding: 20px;
  background-color: #ffffff;
  border-radius: 10px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.feature-card .icon {
  font-size: 40px;
  color: #007bff;
  margin-bottom: 10px;
}

/* Gallery Section */
.gallery-section,
.partners-section {
  padding: 60px 20px;
  background-color: #f0f8ff;
  text-align: center;
}

.container h2 {
  font-size: 2.5rem;
  color: #003366;
  margin-bottom: 20px;
}

.container p {
  font-size: 1.2rem;
  color: #555555;
  margin-bottom: 40px;
}

/* Gallery Section */
.gallery-section {
  background-color: #f0f8ff; /* Light blue background */
  padding: 50px 0;
  text-align: center;
}

.gallery-section h2 {
  font-size: 2.5rem;
  color: #007bff; /* Blue color */
  margin-bottom: 20px;
}

.gallery-section p {
  font-size: 1.2rem;
  color: #333;
  margin-bottom: 40px;
}

.gallery-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 15px;
}

.gallery-grid img {
  width: 100%;
  height: auto;
  border-radius: 8px;
  transition: transform 0.3s ease;
}

.gallery-grid img:hover {
  transform: scale(1.05);
}

/* Partners Section */
.partners-section {
  background-color: #ffffff;
  padding: 50px 0;
  text-align: center;
}

.partners-section h2 {
  font-size: 2.5rem;
  color: #007bff;
  margin-bottom: 20px;
}

.partners-section p {
  font-size: 1.2rem;
  color: #333;
  margin-bottom: 40px;
}

.partners-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
  gap: 20px;
  justify-items: center;
}

.partners-grid img {
  width: 120px;
  height: auto;
  transition: transform 0.3s ease;
}

.partners-grid img:hover {
  transform: scale(1.1);
}
  </style>

  <script>
    // Testimonials Slider
let currentSlide = 0;

const testimonials = document.querySelectorAll('.testimonial');
const prevBtn = document.querySelector('.prev-btn');
const nextBtn = document.querySelector('.next-btn');

function showSlide(index) {
  testimonials.forEach((testimonial, i) => {
    testimonial.style.transform = `translateX(${100 * (i - index)}%)`;
  });
}

prevBtn.addEventListener('click', () => {
  currentSlide = (currentSlide - 1 + testimonials.length) % testimonials.length;
  showSlide(currentSlide);
});

nextBtn.addEventListener('click', () => {
  currentSlide = (currentSlide + 1) % testimonials.length;
  showSlide(currentSlide);
});

showSlide(currentSlide);

  </script>

<!-- Sidebar -->
<div class="sidebar">
    <div class="logo-section">
    </div>

        <div class="menu-section">
            <h3>Elements of Culture</h3>
            <div class="menu-item">
                <ul>
                    <li><a href="geography.php">Geography</a></li>
                    <li><a href="history.php">History</a></li>
                    <li><a href="demographics.php">Demographics</a></li>
                    <li><a href="culture.php">Culture</a></li>
                </ul>
            </div>

        <div class="menu-section">
            <h3>Learning Styles</h3>
            <div class="menu-item">
                <ul>
                    <li><input type="checkbox">Visual</li>
                    <li><input type="checkbox">Auditory & Oral</li>
                    <li><input type="checkbox">Read & Write</li>
                    <li><input type="checkbox">Kinesthetic</li>
                </ul>
            </div>

        <div class="menu-section">
            <h3>Location</h3>
            <div class="menu-item">
                <a href="choose-loc.php"><span>+</span> Choose a location</a>
            </div>
        </div>
        
    <div class="menu-section">
      <h3>Resources</h3>
      <div class="menu-item">
        <span>🔗</span>
        <a href="#">About Kulturifiko</a>
      </div>
    </div>
  </div>

<style>
  /* Sidebar */
  .sidebar {
    position: fixed;
    top: 60px; 
    left: 0;
    width: 240px;  
    height: 100vh;
    background-color: #365486;
    padding-top: 30px;
    z-index: 999; 
    display: flex;
    flex-direction: column;
    align-items: center;
    overflow-y: auto;
    flex-grow: 1;
    box-shadow: 4px 0 12px rgba(0, 0, 0, 0.1);
    border-radius: 0 5px 5px 0;
}

/* Logo Section */
.logo-section {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 15px;
  margin-bottom: 15px;
}

.logo-section img {
  max-width: 100px;
  border-radius: 5px;
}

.explore-btn {
    padding: 10px 20px;
    background: #00438f; 
    color: white;
    font-size: 14px;
    font-weight: 600;
    border: none;
    border-radius: 10px; 
    cursor: pointer;
    position: relative;
    display: inline-block;
    text-decoration: none;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
    transition: all 0.3s ease; 
}

.explore-btn:hover {
    background: #0056b3; 
    transform: translateY(-3px); 
    box-shadow: 0 8px 12px rgba(0, 0, 0, 0.15);
}

.explore-btn:active {
    transform: translateY(1px); 
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
}

.explore-btn:focus {
    outline: none;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.5); 
}

.explore-btn:hover {
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% {
        transform: translateY(-3px);
    }
    50% {
        transform: translateY(-5px);
    }
    100% {
        transform: translateY(-3px);
    }
}

/* Section Menus */
.menu-section {
  margin-bottom: 10px;
}

.menu-section h3 {
  font-size: 15px;
  margin-bottom: 8px;
  color: #DCF2F1;
}

/* Menu Items */
.menu-item {
  display: inline-block;
  align-items: center;
  justify-content: flex-start;
  margin: 3px 0;
  cursor: pointer;
  transition: background 0.2s ease;
  padding: 5px 5px;
  border-radius: 4px;
  color: #ffffff;
}

.menu-item a {
    color: #ffffff;
    text-decoration: none;
    font-size: .8rem;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 30px;
}

.menu-item a:hover {
    background-color: #7FC7D9;
    color: #0F1035;
}

.menu-item a.active {
    background-color: #1e3c72;
    color: #fff;
}

.menu-item ul {
    list-style: none;
    padding: 0;
}
  
.menu-item li {
    margin-bottom: 10px;
    font-size: .8rem;
}
  
input[type="checkbox"] {
    margin-right: 5px;
}

#chosen-location-container {
    margin-top: 20px; 
    display: block;
}

#chosen-location-container label {
    font-size: 12px; 
    color: #ffffff;
}
</style>

</body>
</head>
</html>