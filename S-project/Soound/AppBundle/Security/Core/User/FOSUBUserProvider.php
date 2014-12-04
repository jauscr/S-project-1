<?php
namespace Soound\AppBundle\Security\Core\User;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider as BaseClass;
use Symfony\Component\Security\Core\User\UserInterface;

class FOSUBUserProvider extends BaseClass
{

    /**
     * {@inheritDoc}
     */
    public function connect(\Symfony\Component\Security\Core\User\UserInterface $user, UserResponseInterface $response)
    {
        $property = $this->getProperty($response);
        $username = $response->getUsername();

//on connect - get the access token and the user ID
        $service = $response->getResourceOwner()->getName();
        $setter = 'set'.ucfirst($service);
        $setter_id = $setter.'Id';
        $setter_token = $setter.'AccessToken';

//we "disconnect" previously connected users
        if( $service == 'facebook'){
            if (null !== $previousUser = $this->userManager->findUserBy(array('facebook_id' => $username))) {
                $previousUser->$setter_id(null);
                $previousUser->$setter_token(null);
                $this->userManager->updateUser($previousUser);
            }
        }

        if( $service == 'google'){
            if (null !== $previousUser = $this->userManager->findUserBy(array('email' => $response->getEmail()))) {
                $previousUser->$setter_id(null);
                $previousUser->$setter_token(null);
                $this->userManager->updateUser($previousUser);
            }
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
        //when the user is registrating
        //print_r(get_class_methods($response->getResponse())); die();
        $service = $response->getResourceOwner()->getName();
        if( $service == 'facebook'){
            $user_service_id = $response->getUsername();
            $user = $this->userManager->findUserBy(array('facebook_id' => $user_service_id));
            if (null === $user) {
                $setter = 'set'.ucfirst($service);
                $setter_id = $setter.'Id';
                $setter_token = $setter.'AccessToken';
                // create new user here
                $user = $this->userManager->createUser();
                $user->$setter_id($user_service_id);
                $user->$setter_token($response->getAccessToken());

                //I have set all requested data with the user's username
                //modify here with relevant data
                $user->setUsername($response->getNickname());
                $user->setEmail($response->getEmail());
                $user->setPassword($response->getNickname());
                $user->setUserPic('https://graph.facebook.com/'.$user_service_id.'/picture');
                $user->setEnabled(true);
                $this->userManager->updateUser($user);
                return $user;
            }else{
                //if user exists - go with the HWIOAuth way
                $user = parent::loadUserByOAuthUserResponse($response);

                $serviceName = $response->getResourceOwner()->getName();
                $setter = 'set' . ucfirst($serviceName) . 'AccessToken';
            }
        }

        if( $service == 'google'){
            $user_service_id = $response->getUsername();
            $user = $this->userManager->findUserBy(array('email' => $response->getEmail()));



            if (null === $user) {
                $setter = 'set'.ucfirst($service);
                $setter_id = $setter.'Id';
                $setter_token = $setter.'AccessToken';
                // create new user here
                $user = $this->userManager->createUser();
                $user->$setter_id($user_service_id);
                $user->$setter_token($response->getAccessToken());
                //I have set all requested data with the user's username
                //modify here with relevant data
                $user->setUsername($response->getNickname());
                $user->setEmail($response->getEmail());
                $user->setPassword($response->getNickname());
                $user->setUserPic($response->getProfilePicture());
                $user->setEnabled(true);
                $this->userManager->updateUser($user);
                return $user;
            }

            //if user exists - go with the HWIOAuth way
            $user = parent::loadUserByOAuthUserResponse($response);

            $serviceName = $response->getResourceOwner()->getName();
            $setter = 'set' . ucfirst($serviceName) . 'AccessToken';

        }

//update access token
        $user->$setter($response->getAccessToken());
        return $user;
    }

}