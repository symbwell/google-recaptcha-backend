<?php

/**
 * How to use this class:
 * 1. Get the token from Google.js response.
 * 2. Use static method Captcha::isRobot to check if the user is a robot.
 */
class Captcha
{
    /**
     * Property to store the IP address of the user. Getting from $_SERVER superglobal.
     *
     * @var string
     */
    private string $ip;

    /**
     * Store the data to send to Google's API.
     * secret: The secret key of the site.
     * response: The token returned by the Google's API.
     * remoteip: The IP address of the user.
     *
     * @var array
     */
    private array $data;

    /**
     * This is the token returned by the Google's API.
     * You need to set this token in constructor when initializing the class.
     * Get the token from Google.js response.
     *
     * @var string
     */
    private string $token;

    /**
     * The options to send to Google's API.
     * http: The header and the method to send the data.
     *  - method: The method to send the data.
     *  - content: The data to send.
     *  - header: The header to send.
     * @var array[]
     */
    private array $options;

    /**
     * The constructor of the class. It sets the IP address of the user and the data to send to Google's API.
     *
     * @param string $token Token returned by Google's API.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
        $this->ip = $_SERVER['HTTP_CLIENT_IP'] ?? ($_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR']);

        $this->data = [
            'secret' => RECAPTCHA_PRIVATE_KEY,
            'response' => $this->token,
            'remoteip' => $this->ip
        ];

        $this->options = [
            'http' => [
                "header" => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($this->data)
            ]
        ];
    }

    /**
     * Check if the user is a robot.
     *
     * @param $token
     * @return bool
     */
    static public function isRobot($token): bool {
        $response = (new self($token))->getResponseData();
        return ($response['success'] === true && $response['score'] < 0.7);
    }

    /**
     * Get the context to send to Google's API.
     * @return resource
     */
    private function getContext() {
        return stream_context_create($this->options);
    }

    /**
     * Get the response from Google's API.
     * @return false|string
     */
    private function getVerifyResponse(): false|string {
        return file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $this->getContext());
    }

    /**
     * Get the response from Google's API and decode it.
     * @return array
     */
    private function getResponseData(): array {
        return json_decode($this->getVerifyResponse(), true);
    }
}
