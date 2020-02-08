<?php
namespace Uzink\BackendBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;
use Uzink\BackendBundle\Entity\User;

class OAuthUserProvider extends BaseClass
{
    /**
     * {@inheritDoc}
     */
    public function connect(UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

        //on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        //we "disconnect" previously connected users
        if (null !== $previousUser = $this->userManager->findUserBy(array($property => $username))) {
            $previousUser->$setter_id(null);
            $previousUser->$setter_token(null);
            $this->userManager->updateUser($previousUser);
        }

        //we connect current user
        $user->$setter_id($username);
        $user->$setter_token($response->getAccessToken());

        $this->userManager->updateUser($user);
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthUserResponse(UserResponseInterface $response)
    {
        switch ($response->getResourceOwner()->getName()) {
            case 'facebook':
                $user = $this->createUserWithFacebook($response);
                break;

            default:
                $user = $this->createUser($response);
        }

        $service = $response->getResourceOwner()->getName();

        $getter         = 'get'.ucfirst($service);
        $getter_id      = $getter.'Id';

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';

        $username = $response->getUsername();
        $resultEmail = $this->userManager->findUserBy(array('email' => $user->getEmail()));
        $resultToken = $this->userManager->findUserBy(array($this->getProperty($response) => $username));
        if ($resultEmail && $resultEmail->$getter_id() == $username) {
            $user = $resultEmail;
        } elseif ($resultEmail && $resultEmail->$getter_id() != $username) {
            $resultEmail->$setter_id($username);
            $user = $resultEmail;
        } elseif (!$resultEmail && $resultToken) {
            $resultToken->setUsername($user->getEmail());
            $resultToken->setEmail($user->getEmail());
            $resultToken->setEnabled(true);
            $user = $resultToken;
        }

        $this->userManager->updateUser($user);

        //if user exists - go with the HWIOAuth way
        $user = parent::loadUserByOAuthUserResponse($response);

        $serviceName = $response->getResourceOwner()->getName();
        $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        //update access token
        $user->$setter($response->getAccessToken());

        return $user;
    }

    protected function createUser(UserResponseInterface $response) {
        $service = $response->getResourceOwner()->getName();

        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

        // Create new user here
        $user = $this->userManager->createUser();
        $user->$setter_id($response->getUsername());
        $user->$setter_token($response->getAccessToken());

        $user->setEnabled(true);

        return $user;
    }

    protected function createUserWithFacebook(UserResponseInterface $response) {
        $user = $this->createUser($response);

        $data = $response->getResponse();

        $user->setUsername($response->getEmail());
        $user->setEmail($response->getEmail());
        $user->setSocialProfileLink(User::SOCIAL_FACEBOOK, $data['link']);
        $user->setPassword($response->getUsername());
        $user->setName($data['first_name']);
        $lastName = $data['last_name'];
        $lastNameChunks = explode(' ', $lastName);
        switch (count($lastNameChunks)) {
            case 0:
                break;
            case 1:
                $user->setSurname1($lastNameChunks[0]);
                break;
            case 2:
                $user->setSurname1($lastNameChunks[0]);
                $user->setSurname2($lastNameChunks[1]);
                break;
            default:
                $user->setSurname1(array_shift($lastNameChunks));
                $name = null;
                foreach ($lastNameChunks as $chunk) {
                    $name .= $chunk . ' ';
                }
                $user->setSurname2($name);
        }

        return $user;
    }
}