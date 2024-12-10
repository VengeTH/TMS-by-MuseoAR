<div class="header">
    <div class="logo">
        <img src="/img/logo.png" class="logo" width=6% height=6%>
    </div>
    <div class="titleBesideLogo">
        <h1>ORGANISS</h1>
    </div>
    <div class="menu">
        <a href="index.php"><button>Sign in</button></a>
        <a href="register.php"><button>Register</button></a>
    </div>
</div>
    <div class="welcome">
        <h1>Welcome</h1>
        <p>to your personal task Manager</p>
    </div>

<style>
body::before {
    content: "";
    background-image: url('/img/bg.jpg'); /* Replace with your image URL */
    background-size: cover; /* Cover the area */
    background-position: center; /* Center the image */
    position: absolute; /* Position it absolutely */
    top: 0;
    left: 0;
    width: 50%; /* Cover only half the width */
    height: 100%; /* Full height */
    z-index: -1; /* Send it to the back */
}
.header{
    left: 0;
    right: 0;
    height: 12vh;
    background-color: black;
}
.logo{
    position: relative;
    top: .2vh;
    left: .8vw;
}
.titleBesideLogo{
    position: relative;
    top: -13vh;
    left: 7.5vw;
    font-family: "Kablammo", system-ui;
    font-size: 20px;
    font-weight: 100;
    color: white;
}
.menu{
    position: relative;
    top: -22.5vh;
    left: 88.5vw;
    width: fit-content;
}
.menu button{
    border-radius: 7px;
    border: 1px solid black;
    height: 4vh;
    width: 4.5vw;
    margin: 2px;
    text-align: center;
}
.menu a[href="register.php"] button{
    background-color: #2c2c2c;
    color: white;
}
.welcome {
    position: relative;
    font-family: "Just Me Again Down Here", cursive;
    color: white;
    text-align: center;
    width: fit-content;
    height: fit-content;
    top: 13vh;
    left: 16vw;
    font-size: 30px;
}

.welcome h1 {
    font-size: 100px;
    font-weight: 100;
    letter-spacing: 3px;
}

.welcome p {
    position: relative;
    top: -13vh;
}

</style>