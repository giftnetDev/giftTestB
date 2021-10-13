<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="EUC-KR">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Document</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Lobster&display=swap');
            body {       
            margin: 0;
            padding: 0;
            }
            .wrapper { 
            width: 100vw;
            height: 100vh;  background:url('https://images.unsplash.com/photo-1516919549054-e08258825f80?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1050&q=80')no-repeat center center;
            background-size: cover;
            overflow-y: scroll;
            }
            .wrapper>div {
            display: flex;
            width: 60%;
            height: 300px;
            margin: 50px auto;
            justify-content: center;
            align-items: center;
            border: 1px solid rgba(255, 255, 255, .1);
            }
            div p {
            font: normal 2rem 'Lobster', cursive;
            color: #fff;
            }
            .blur {
            -webkit-backdrop-filter: blur(50px);
            backdrop-filter: blur(50px);
            }
            .brightness {
            -webkit-backdrop-filter: brightness(1.5);
            backdrop-filter: brightness(1.5);
            }
            .contrast {
            -webkit-backdrop-filter: contrast(.2);
            backdrop-filter: contrast(.2);
            }
            .drop-shadow {
            -webkit-backdrop-filter: drop-shadow(5px 5px red);
            backdrop-filter: drop-shadow(5px 5px red);
            }
            .grayscale {
            -webkit-backdrop-filter: grayscale(.8);
            backdrop-filter: grayscale(.8);
            }
            .hue-rotate {
            -webkit-backdrop-filter: hue-rotate(45deg);
            backdrop-filter: hue-rotate(45deg);
            }
            .invert {
            -webkit-backdrop-filter: invert(1);
            backdrop-filter: invert(1);
            }
            .opacity {
            -webkit-backdrop-filter: opacity(.4);
            backdrop-filter: opacity(.4);
            }
            .saturate {
            -webkit-backdrop-filter: saturate(.3);
            backdrop-filter: saturate(.3);
            }
            .sepia {
            -webkit-backdrop-filter: sepia(.9);
            backdrop-filter: sepia(.9);
            }


        </style>
    </head>
    <body>
        <div class="wrapper">
        <div class="blur"><p>blur(50px)</p></div>
        <div class="brightness"><p>brightness(1.5)</p></div>
        <div class="contrast"><p>contrast(.2)</p></div>
        <div class="drop-shadow"><p>drop-shadow(5px 5px red)</p></div>
        <div class="grayscale"><p>grayscale(.8)</p></div>
        <div class="hue-rotate"><p>hue-rotate(45deg)</p></div>
        <div class="invert"><p>invert(1)</p></div>
        <div class="opacity"><p>opacity(.4)</p></div>
        <div class="saturate"><p>saturate(.3)</p></div>
        <div class="sepia"><p>sepia(.9)</p></div>
            
    </body>
</html>