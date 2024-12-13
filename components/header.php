<div class="header">
    <div class="logo-wrapper">
        <img src="/img/logo.png" class="logo" class="logo">
        <div class="titleBesideLogo">
            <h1>ORGANISS</h1>
        </div>
    </div>
    <div class="menu">
        <a href="/">Sign in</a>
        <a href="/user/register.php" id="register">Register</button></a>
    </div>
</div>
<!-- welcome css -->
<style>
/* LESSON : NEVER USE COPILOT WITHOUT UNDERSTANDING THE MEANING OF THE CODE.  */
/* this is a good example on where you could use display grid. msg me pag di mo gets. --gelo */
/* .body::before {
    content: "";
    background-image: url('/img/bg.jpg'); 
    background-size: cover; 
    background-position: center; 
    position: absolute;
    top: 0;
    left: 0;
    width: 50%; 
    height: 100%; 
    z-index: -1; 
} */
/* THIS LINE OF CODE IS NOT EVEN BEING APPLIED! */
.header{
    width: 100%;
    background-color: black !important;
    display: flex;
}
.logo-wrapper{
    /* the culprit on why everything is shifting. */
    /* position: relative;
    top: .2vh;
    left: .8vw; */
    display: flex;
    align-items: center;
    justify-content: center;
}
.logo{
    max-width: 5rem;
    max-height: 5rem;
}
.titleBesideLogo{
    position: relative;
    /* the culprit on why everything is shifting. Bad use of positioning.*/
    /* top: -13vh;
    left: 7.5vw; */
    font-family: "Kablammo", system-ui;
    font-size: 1.25rem;
    font-weight: 100;
    color: white;
}
.menu{
    position: relative;
    /* the culprit on why everything is shifting. */
    margin-left: auto;
    margin-right: 2vw;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    /* top: -22.5vh;
    left: 88.5vw; */
    width: fit-content;
}
.menu a{
    text-decoration: none;
    color: black;
    background-color : white;
    border-radius: 7px;
    border: 1px solid black;
    padding: 1rem 2rem;
    text-align: center;
}
/* just use id for here. Lemme guess, used copilot and never thought of it again. */
/* .menu a[href="/user/register.php"] button{
    background-color: #2c2c2c;
    color: white;
} */
#register{
    background-color: #2c2c2c;
    color: white;
}

</style>