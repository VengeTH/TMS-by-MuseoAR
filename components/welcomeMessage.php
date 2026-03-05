<div class="welcome">
    <div class="welcome-overlay"></div>
    <div class="welcome-content">
        <h1>Welcome</h1>
        <p>to your personal task manager</p>
    </div>
</div>
<style>
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
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.75) 0%, rgba(15, 23, 42, 0.5) 100%);
}

.welcome-content {
    position: relative;
    z-index: 1;
    text-align: center;
    color: white;
}

.welcome h1 {
    margin: 0 0 0.5rem 0;
    font-family: "Righteous", system-ui, sans-serif;
    font-size: clamp(2.5rem, 6vw, 4rem);
    font-weight: 500;
    letter-spacing: 0.02em;
}

.welcome p {
    margin: 0;
    font-family: "Outfit", system-ui, sans-serif;
    font-size: clamp(1rem, 2vw, 1.25rem);
    opacity: 0.95;
}
</style>
