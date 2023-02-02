<?php

namespace App\Services;

use App\Entity\User;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthService
{
    public function __construct(private HttpClientInterface $client, private UserService $userService)
    {
        
    }

    /**
     * Private Authenticate User
     *
     * @param String $email
     * @param String $password
     * @return Array
     */
    private function AuthentificationRequest(String $email, String $password) : Array
    {
        $base_url = $_ENV['BASE_URL'];
        $response = $this->client->request(
            'POST',
            $base_url.'/user/auth',
            [
                'body' => [
                    'email' => $email,
                    'password' => $password
                ]
            ]
        );

        $statusCode = $response->getStatusCode(false);
        if($statusCode == 200 || $statusCode == 201)
        {
            $content = $response->toArray(false);
            $datas =  [
                'status' => true,
                'message' => $content['message'],
                'content' => $content['data']
            ];

            //verify user role
            $role = $datas['content']['user']['user']['role'];
            if($role == "admin")
            {
                //add userBag with passport
                $user = $datas['content']['user']['user'];

                $userStore = $this->userService->findUser('email', $email);
                if($userStore)
                {
                    $userStore->setPassword($datas['content']['user']['user']['password']);
                    $userStore->setToken($datas['content']['token']);

                    $this->userService->saveUser($userStore);
                }
                else
                {
                    $newUser = new User();
                    $newUser->setEmail($email);
                    $newUser->setRoles(['admin']);
                    $newUser->setPassword($datas['content']['user']['user']['password']);
                    $newUser->setToken($datas['content']['token']);

                    $this->userService->saveUser($newUser);
                }

                return [
                    'status' => true,
                    'message' => $email.' has been authenticated successfully !'
                ];
            }
            else
            {
                return [
                    'status' => false,
                    'message' => $email.' has been authenticated successfully !'
                ];
            }

        }
        elseif($statusCode == 401)
        {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message'],
                'content' => null
            ];
        }
    }

    /**
     * Public function for call authenticate user
     *
     * @param String $email
     * @param String $password
     * @return Array
     */
    public function Auth(String $email, String $password) : Array
    {
        return $this->AuthentificationRequest($email, $password);
    }
}