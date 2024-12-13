<div class="welcome">
    <h1>Welcome</h1>
    <p>to your personal task Manager</p>
</div>

<style>
.welcome {
	min-height: calc(100vh - 12rem);
     background-image: url('/img/bg.jpg'); /* Replace with your image URL */
    background-size: cover; /* Cover the area */
    background-position: center; /* Center the image */
    flex-grow:1;
    position: relative;
    font-family: "Just Me Again Down Here", cursive;
    color: white;
    text-align: center;
    width: fit-content;
    font-size: 30px;
}
/* good example of when to use relative/absolute positioning */
.welcome h1 {
    position : absolute;
    font-size: 100px;
    font-weight: 100;
    letter-spacing: 3px;
    top : calc(50% - 6rem) !important;
}

.welcome p {
    position: absolute;
}
/* center using absolute */
.welcome p, .welcome h1 {
    width: 100%;
    top: 50%;  
    left: 50%; 
    transform: translate(-50%, -50%);
}
</style>