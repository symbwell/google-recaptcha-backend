# Google Recaptcha V3 Backend Class
The PHP Backend class responsible for checking your frontend token (Captcha V3 only). PHP >= 8.0 require.

# How to use this class:
1. Get the token from Google.js response.
2. Use static method Captcha::isRobot to check if the user is a robot.

Example
```
require_once Captcha.php;

$isRobot = Captcha::isRobot($token);
```
