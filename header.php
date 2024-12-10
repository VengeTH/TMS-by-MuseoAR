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

.header{
    left: 0;
    right: 0;
    height: 12vh;
    background-color: #fffafa;
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
    color: black;
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
    color: #000;
    text-align: center;
    width: fit-content;
    height: fit-content;
    top: 13vh;
    left: 22vw;
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