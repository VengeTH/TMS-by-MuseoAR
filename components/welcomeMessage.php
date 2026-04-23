<div class="welcome">
    <div class="welcome-overlay"></div>
    <div class="welcome-content">
        <h1>Welcome</h1>
        <p>Structured task intelligence by The Heedful</p>
    </div>
</div>
<style>
@import url("https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap");

.welcome {
    min-width: 45%;
    flex: 1;
    min-height: 280px;
    background-image: url("/img/bg.jpg");
    background-size: cover;
    background-position: center;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.welcome-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(255, 193, 7, 0.3) 0%, rgba(255, 179, 0, 0.22) 35%, rgba(107, 107, 107, 0.55) 100%);
}

.welcome-content {
    position: relative;
    z-index: 1;
    text-align: center;
    color: white;
}

.welcome h1 {
    margin: 0 0 0.5rem 0;
    font-family: "Space Grotesk", "Inter", system-ui, sans-serif;
    font-size: clamp(2.5rem, 6vw, 4rem);
    font-weight: 600;
    letter-spacing: 0.02em;
}

.welcome p {
    margin: 0;
    font-family: "Inter", system-ui, sans-serif;
    font-size: clamp(1rem, 2vw, 1.25rem);
    opacity: 0.95;
}
</style>
