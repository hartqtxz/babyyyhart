<template>
  <div class="frontpage">
    <header class="site-header">
      <div class="brand">
        <img :src="logoUrl" alt="Logo" class="logo" />
        <span class="brand-title">JOB PORTAL</span>
      </div>

      <nav class="nav-actions">
        <router-link to="/login" class="btn-signin">Sign In</router-link>
        <router-link to="/register" class="btn-register">Register</router-link>
      </nav>
    </header>

    <main class="hero" :style="{ backgroundImage: `url(${bgUrl})` }">
      <div class="hero-overlay">
        <h1 class="hero-title">
          Find your next Job or
          <span class="hero-highlight">Perfect Hire</span>
        </h1>

        <p class="hero-subtitle">
          Connecting talented individuals with great opportunities
        </p>

        <router-link to="/register" class="btn-cta">
          Get Started
        </router-link>
      </div>
    </main>

    <section class="about-section" id="about">
      <div class="about-container">
        <div class="about-item">
          <h2>About Us</h2>
          <p>
            The DOLE Surigao del Norte Field Office, as part of the national
            Department of Labor and Employment (DOLE), aims to promote gainful
            employment and worker welfare, focusing on empowering people through
            knowledge, skills, and access to economic opportunities.
          </p>
        </div>

        <div class="about-item">
          <h2>Vision</h2>
          <p>
            With the blessings of Divine Providence, Surigao City 2040: A Smart
            City of Resilient People, enjoying a healthy and pleasant
            environment, driven by a progressive, competitive, sustainable
            economy and guided by transparent and accountable governance.
          </p>
        </div>

        <div class="about-item">
          <h2>Mission</h2>
          <p>
            To empower workers and employers through education and support, and
            to facilitate employment and entrepreneurship in Surigao del Norte.
          </p>
        </div>
      </div>
    </section>

    <footer class="site-footer">
      <div class="footer-container">
        <div class="footer-item">
          <h3>Contact</h3>
          <p class="contact-text">📞 85-359-833</p>
        </div>

        <div class="footer-item">
          <h3>DOLE</h3>
          <a href="#">Photos Policy</a>
        </div>

        <div class="footer-item">
          <h3>Follow Us</h3>
          <div class="social-links">
            <a href="#">📘 Facebook</a>
            <a href="#">🐦 Twitter</a>
            <a href="#">📷 Instagram</a>
          </div>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
const logoUrl = '/assets/images/logo.png'
const bgUrl = '/assets/images/dole-bg.jpg'
</script>

<style scoped>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.frontpage {
  width: 100%;
  min-height: 100vh;
  background: #ffffff;
  color: #222;
  font-family: Arial, Helvetica, sans-serif;
}

.site-header {
  min-height: 110px;
  background: #ffffff;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 24px 50px;
  border-bottom: 1px solid #e5e5e5;
}

.brand {
  display: flex;
  align-items: center;
  gap: 18px;
}

.logo {
  width: 62px;
  height: 62px;
  object-fit: contain;
}

.brand-title {
  font-size: 24px;
  font-weight: 700;
  color: #1f73be;
  letter-spacing: 1px;
}

.nav-actions {
  display: flex;
  align-items: center;
  gap: 18px;
}

.nav-actions a {
  text-decoration: none;
  color: #fff;
  font-weight: 700;
  font-size: 16px;
  padding: 14px 32px;
  border-radius: 999px;
  transition: 0.3s ease;
  display: inline-block;
}

.btn-signin {
  background: #24b14b;
}

.btn-register {
  background: #1f73be;
}

.nav-actions a:hover,
.btn-cta:hover {
  opacity: 0.92;
  transform: translateY(-1px);
}

.hero {
  min-height: 730px;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
}

.hero-overlay {
  width: 100%;
  max-width: 1120px;
  background: rgba(0, 0, 0, 0.45);
  border-radius: 28px;
  text-align: center;
  padding: 80px 40px;
}

.hero-title {
  font-size: 72px;
  font-weight: 800;
  line-height: 1.15;
  color: #ffffff;
  margin-bottom: 22px;
}

.hero-highlight {
  color: #ffd400;
}

.hero-subtitle {
  font-size: 26px;
  color: #ffffff;
  margin-bottom: 32px;
}

.btn-cta {
  display: inline-block;
  text-decoration: none;
  background: #ffd400;
  color: #222;
  font-size: 22px;
  font-weight: 700;
  padding: 18px 56px;
  border-radius: 999px;
  transition: 0.3s ease;
}

/* ABOUT SECTION */
.about-section {
  background: #eef1f7;
  padding: 70px 20px 90px;
  text-align: center;
}

.about-container {
  max-width: 1100px;
  margin: 0 auto;
}

.about-item {
  margin-bottom: 45px;
}

.about-item:last-child {
  margin-bottom: 0;
}

.about-item h2 {
  font-size: 32px;
  color: #1e73be;
  font-weight: 700;
  margin-bottom: 18px;
}

.about-item p {
  max-width: 1000px;
  margin: 0 auto;
  font-size: 20px;
  line-height: 1.8;
  color: #333;
}

/* FOOTER */
.site-footer {
  background-color: #005aa9;
  padding: 55px 20px;
  color: white;
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 30px;
  text-align: center;
  align-items: start;
}

.footer-item h3 {
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 18px;
  color: white;
}

.footer-item p,
.footer-item a {
  font-size: 18px;
  color: white;
  text-decoration: none;
}

.footer-item a:hover {
  text-decoration: underline;
}

.contact-text {
  font-size: 18px;
  font-weight: 500;
}

.social-links {
  display: flex;
  justify-content: center;
  gap: 18px;
  flex-wrap: wrap;
}

/* RESPONSIVE */
@media (max-width: 1200px) {
  .hero-title {
    font-size: 56px;
  }

  .hero-subtitle {
    font-size: 22px;
  }
}

@media (max-width: 992px) {
  .site-header {
    flex-direction: column;
    gap: 20px;
    padding: 24px 20px;
  }

  .hero {
    min-height: 620px;
  }

  .hero-overlay {
    padding: 50px 30px;
  }

  .hero-title {
    font-size: 44px;
  }

  .footer-container {
    grid-template-columns: 1fr;
    gap: 35px;
  }
}

@media (max-width: 768px) {
  .brand {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }

  .brand-title {
    font-size: 22px;
  }

  .nav-actions {
    flex-direction: column;
    width: 100%;
  }

  .nav-actions a {
    width: 220px;
    text-align: center;
  }

  .hero-overlay {
    padding: 36px 20px;
  }

  .hero-title {
    font-size: 32px;
  }

  .hero-subtitle {
    font-size: 18px;
  }

  .btn-cta {
    font-size: 18px;
    padding: 14px 34px;
  }

  .about-item h2 {
    font-size: 28px;
  }

  .about-item p {
    font-size: 16px;
    line-height: 1.7;
  }

  .social-links {
    flex-direction: column;
    gap: 10px;
  }
}
</style>