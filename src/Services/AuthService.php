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
    private function AuthentificationRequest(String $email, String $password)
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

            //get user role from API
            $role = $datas['content']['user']['user']['role'];

            //verify if user is already store
            $userStore = $this->userService->findUser('email', $email);
            if($userStore)
            {
                //update user role
                $userStore->setRoles([$role]);

                //verify user role
                if($userStore->getRoles()[0] == 'admin')
                {
                    $userStore->setPassword($datas['content']['user']['user']['password']);
                    $userStore->setToken($datas['content']['token']);
                    $userStore->setUuid($datas['content']['user']['user']['_id']);

                    $this->userService->saveUser($userStore);
                }
                else
                {
                    $this->userService->findOneAndDelete('email', $email);
                }
            }
            else
            {
                if($role == "admin")
                {
                    $newUser = new User();
                    $newUser->setEmail($email);
                    $newUser->setRoles(['admin']);
                    $newUser->setPassword($datas['content']['user']['user']['password']);
                    $newUser->setToken($datas['content']['token']);
                    $newUser->setUuid($datas['content']['user']['user']['_id']);
    
                    $this->userService->saveUser($newUser);
                }
            }

        }
    }

    /**
     * Public function for call authenticate user
     *
     * @param String $email
     * @param String $password
     * @return Array
     */
    public function Auth(String $email, String $password)
    {
        return $this->AuthentificationRequest($email, $password);
    }

    /**
     * Check User Role from API
     *
     * @param User $user
     * @return array
     */
    public function RoleChecker(User $user) : array
    {
        $base_url = $_ENV['BASE_URL'];
        $response = $this->client->request(
            'GET',
            $base_url.'/user/'.$user->getUuid(),
            [
                'headers' => [
                    'Authorization' => 'bearer '.$user->getToken(),
                ]
            ]
        );

        $statusCode = $response->getStatusCode();
        if($statusCode == 201 || $statusCode == 200)
        {
            $content = $response->toArray(false);
            $role = $content['data']['role'];
            if($role == "admin")
            {
                return [
                    'status' => true,
                    'message' => ''
                ];
            }
            else
            {
                return [
                    'status' => false,
                    'message' => 'Vos accès ont été modifier vous n\avez plus accès au tableau de bord de LAFIA, Merci !'
                ];
            }
        }
        else
        {
            $content = $response->toArray(false);
            return [
                'status' => false,
                'message' => $content['message']
            ];
        }
    }
}