<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8"/>
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"/>
        <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
        <title>Demo - Mailer</title>
        <link rel="shortcut icon" href="https://mailer.gainhq.com/images/icon.png"/>
        <link rel="apple-touch-icon" href="https://mailer.gainhq.com/images/icon.png"/>
        <link rel="apple-touch-icon-precomposed" href="https://mailer.gainhq.com/images/icon.png"/>


        <style>
            body{
                background:#1e454a;
            }
            main{
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }
            .demo-btn {
                background: #19A526;
                color:#ffffff;
            }

            .demo-btn:hover{
                background: #1dbd2c;
            }
            
            .logo-container{
                margin-bottom: 30px;
                width: 80%;
            }
            
            .demo-btn-container{
                display:flex;
                flex-direction: column;
                border-radius:5px !important;
                background: #006838;
                padding: 10PX 10PX;
            }

            .btn{
                display: flex;
                justify-content: center;
                text-decoration: none;
                cursor: pointer;
            }

            .btn-default{
                padding: 10px;
                font-size: large;
            }


        </style>

    </head>

    <body>
        <main>
            <div class="logo-container">
                <img src="https://mailer.gainhq.com/images/logo.png" alt="logo">
            </div>
            <div class="demo-btn-container">
                <a  class="btn btn-default demo-btn" href="https://mailer.gainhq.com/" target="new">Click to view demo</a>
            </div>
        </main>

    </body>
</html>
