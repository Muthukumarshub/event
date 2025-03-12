<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PlanPro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Base styles and reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            line-height: 1.6;
            color: #333;
            overflow-x: hidden;
            background-color: #f5f7fa;
        }
        
        a {
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }
        
        /* Gradient background similar to login page */
        .gradient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #6254e8 0%, #8a72f9 50%, #a57aff 100%);
            z-index: -1;
        }
        
        /* Wavy pattern overlay */
        .wave-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: repeating-linear-gradient(
                45deg,
                rgba(255, 255, 255, 0.05) 0px,
                rgba(255, 255, 255, 0.05) 20px,
                transparent 20px,
                transparent 40px
            );
            z-index: -1;
        }
        
        /* Header and Navigation */
        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 20px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(10px);
        }
        
        .logo {
            font-size: 28px;
            font-weight: 700;
            color: #6254e8;
        }
        
        .logo span {
            color: #a57aff;
        }
        
        .nav-links {
            display: flex;
            gap: 30px;
        }
        
        .nav-links a {
            color: #555;
            font-weight: 500;
            position: relative;
            padding: 5px 0;
        }
        
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: #6254e8;
            transition: width 0.3s ease;
        }
        
        .nav-links a:hover {
            color: #6254e8;
        }
        
        .nav-links a:hover::after {
            width: 100%;
        }
        
        .login-btn {
            background-color: #6254e8;
            color: white;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(98, 84, 232, 0.4);
        }
        
        .login-btn:hover {
            background-color: #a57aff;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(98, 84, 232, 0.6);
        }
        
        /* Mobile menu */
        .menu-toggle {
            display: none;
            cursor: pointer;
            font-size: 24px;
            color: #6254e8;
        }
        
        /* Hero Section */
        .hero {
            min-height: 100vh;
            width: 100%;
            padding-top: 100px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero-content {
            max-width: 800px;
            padding: 40px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(5px);
            margin: 0 20px;
            animation: fadeIn 1s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .hero h1 {
            font-size: 48px;
            font-weight: 700;
            color: #6254e8;
            margin-bottom: 20px;
            position: relative;
            display: inline-block;
        }
        
        .hero h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(to right, #6254e8, #a57aff);
            border-radius: 2px;
        }
        
        .hero p {
            font-size: 18px;
            color: #555;
            margin-bottom: 30px;
        }
        
        .cta-btn {
            background: linear-gradient(to right, #6254e8, #a57aff);
            color: white;
            padding: 15px 40px;
            border-radius: 30px;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(98, 84, 232, 0.4);
            display: inline-block;
            margin-top: 20px;
        }
        
        .cta-btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 25px rgba(98, 84, 232, 0.6);
        }
        
        /* Features Section */
        .features {
            padding: 120px 40px;
            max-width: 1200px;
            margin: 0 auto;
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.1);
            margin-top: -50px;
            position: relative;
            z-index: 1;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-header h2 {
            font-size: 36px;
            color: #6254e8;
            margin-bottom: 15px;
            font-weight: 700;
            position: relative;
            display: inline-block;
        }
        
        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(to right, #6254e8, #a57aff);
            border-radius: 2px;
        }
        
        .section-header p {
            color: #666;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 60px;
            margin-top: 40px;
        }
        
        .features-text {
            padding-right: 20px;
        }
        
        .features-text p {
            margin-bottom: 20px;
            color: #555;
            font-size: 16px;
            line-height: 1.8;
        }
        
        .feature-list {
            margin-top: 30px;
        }
        
        .feature-item {
            display: flex;
            margin-bottom: 25px;
            align-items: flex-start;
            transition: all 0.3s ease;
            padding: 15px;
            border-radius: 10px;
            cursor: pointer;
        }
        
        .feature-item:hover {
            background-color: rgba(98, 84, 232, 0.05);
            transform: translateX(5px);
        }
        
        .feature-icon {
            color: #a57aff;
            font-size: 24px;
            margin-right: 15px;
            background-color: rgba(98, 84, 232, 0.1);
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
        }
        
        .feature-text strong {
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 5px;
            font-size: 18px;
        }
        
        .feature-text p {
            margin: 0;
            color: #666;
        }
        
        .features-image {
            position: relative;
            overflow: hidden;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }
        
        .features-image:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }
        
        .features-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .features-image:hover img {
            transform: scale(1.05);
        }
        
        /* Footer */
        footer {
            background-color: #222;
            color: #fff;
            padding: 60px 40px;
            text-align: center;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .footer-logo {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
            color: white;
        }
        
        .footer-logo span {
            color: #a57aff;
        }
        
        .social-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }
        
        .social-links a {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: #6254e8;
            transform: translateY(-3px);
        }
        
        .copyright {
            margin-top: 20px;
            color: #aaa;
            font-size: 14px;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .feature-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            
            .features-text {
                padding-right: 0;
            }
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 36px;
            }
            
            header {
                padding: 15px 20px;
            }
            
            .nav-links {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 70px);
                background-color: white;
                flex-direction: column;
                padding: 40px;
                transition: all 0.3s ease;
                z-index: 99;
            }
            
            .nav-links.active {
                left: 0;
            }
            
            .menu-toggle {
                display: block;
            }
            
            .features {
                padding: 80px 20px;
            }
        }
        
        /* Animation classes */
        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }
        
        .animate-slide-up {
            animation: slideUp 1s ease-out;
        }
        
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Gradient Background -->
    <div class="gradient-bg"></div>
    <div class="wave-pattern"></div>
    
    <!-- Header Section -->
    <header>
        <div class="logo">Plan<span>Pro</span></div>
        <nav class="nav-links" id="navLinks">
            <a href="#features"><i class="fas fa-star-of-life"></i> Features</a>
            <a href="#about"><i class="fas fa-info-circle"></i> About</a>
            <a href="#pricing"><i class="fas fa-tag"></i> Pricing</a>
            <a href="#contact"><i class="fas fa-envelope"></i> Contact</a>
        </nav>
        <a href="login.php" class="login-btn">Log In</a>
        <div class="menu-toggle" id="menuToggle">
            <i class="fas fa-bars"></i>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Event Management System</h1>
            <p>Event management success starts with optimal planning and organization. Manage all aspects of your event management business with a single tool to ensure that your entire team is on the same page.</p>
            <a href="login.php" class="cta-btn"><i class="fas fa-rocket"></i> Try It Now</a>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-header">
            <h2>Professional Software for Event Management</h2>
            <p>The right professional event management software makes all the difference. Our complete event software tools give you total control over the entire event lifecycle.</p>
        </div>
        
        <div class="feature-grid">
            <div class="features-text animate-fade-in">
                <p>Whether you're planning an enterprise-level conference or a kid's birthday bash, the event needs to go smoothly.</p>
                <p>PlanPro's complete event software tools give event organizers and event venues total control over the entire event lifecycle with its powerful, easy-to-use features.</p>
                <p>Our event management solution is highly customizable, efficient, consistent, and secure.</p>
                
                <div class="feature-list">
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Venue Management</strong>
                            <p>Maximize your facility's revenue potential with smart scheduling.</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Staff Management</strong>
                            <p>Hire and track staff productivity with intuitive dashboards.</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-utensils"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Catering Management</strong>
                            <p>Customize menus for attendees preferences and dietary requirements.</p>
                        </div>
                    </div>
                    <div class="feature-item">
                        <div class="feature-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="feature-text">
                            <strong>Resource Management</strong>
                            <p>Prevent shortages and double bookings with intelligent tracking.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="features-image animate-slide-up">
                <img src="images/image1.png" alt="EventPro Dashboard" />
            </div>
        </div>
    </section>
    
    <!-- Footer Section -->
    <footer>
        <div class="footer-content">
            <div class="footer-logo">Plan<span>Pro</span></div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
            <p class="copyright">&copy; 2025 PlanPro. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        const menuToggle = document.getElementById('menuToggle');
        const navLinks = document.getElementById('navLinks');
        
        menuToggle.addEventListener('click', () => {
            navLinks.classList.toggle('active');
            menuToggle.innerHTML = navLinks.classList.contains('active') ? 
                '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                // Close mobile menu if open
                if (navLinks.classList.contains('active')) {
                    navLinks.classList.remove('active');
                    menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
                }
                
                // Scroll to the section
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
        
        // Animate elements on scroll
        const animateOnScroll = () => {
            const elements = document.querySelectorAll('.animate-fade-in, .animate-slide-up');
            
            elements.forEach(element => {
                const elementPosition = element.getBoundingClientRect().top;
                const screenPosition = window.innerHeight / 1.2;
                
                if (elementPosition < screenPosition) {
                    element.style.opacity = 1;
                    element.style.transform = 'translateY(0)';
                }
            });
        };
        
        // Initial state for animations
        document.querySelectorAll('.animate-fade-in, .animate-slide-up').forEach(element => {
            element.style.opacity = 0;
            element.style.transform = element.classList.contains('animate-slide-up') ? 
                'translateY(40px)' : 'translateY(20px)';
            element.style.transition = 'all 0.8s ease-out';
        });
        
        // Listen for scroll events
        window.addEventListener('scroll', animateOnScroll);
        
        // Trigger animations on page load
        window.addEventListener('load', animateOnScroll);
    </script>
</body>
</html>