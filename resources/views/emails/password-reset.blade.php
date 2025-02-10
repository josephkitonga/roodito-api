<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>Roodito Password Reset</title>
    <style>
        /* Your provided CSS styles here */
        /* I'm keeping the styles but truncating them in this explanation for brevity */
        @media only screen and (max-width: 620px) {
            table[class=body] h1 {
                font-size: 28px !important;
                margin-bottom: 10px !important;
            }
        }
    </style>
</head>
<body class="">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" class="body">
    <tr>
        <td>&nbsp;</td>
        <td class="container">
            <div class="content">
                <!-- START CENTERED WHITE CONTAINER -->
                <table role="presentation" class="main">
                    <!-- START MAIN CONTENT AREA -->
                    <tr>
                        <td class="wrapper">
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <p>Hi {{ $name }},</p>
                                        <p>Please click the link below to reset your account.</p>
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                            <tbody>
                                            <tr>
                                                <td align="left">
                                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                                        <tbody>
                                                        <tr>
                                                            <td>
                                                                <a href="https://roodito.com/auth/reset/password/{{ $user_id }}" target="_blank">Reset Password</a>
                                                            </td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>
                                        <p>Study any topic, anytime. Explore thousands of topics for the lowest price ever!</p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- START FOOTER -->
                <div class="footer">
                    <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="content-block">
                                <span class="apple-link">Thank you,</span>
                                <br>Roodito family
                            </td>
                        </tr>
                        <tr>
                            <td class="content-block">
                                <span class="apple-link">Terms of Service | Privacy Policy | Support</span>
                                <br>Roodito Limited | <a href="#">Unsubscribe</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </td>
        <td>&nbsp;</td>
    </tr>
</table>
</body>
</html>
