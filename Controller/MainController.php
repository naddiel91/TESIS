<?php

namespace App\Controller;

use App\Repository\UserRepository;
use FOS\UserBundle\FOSUserBundle;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
//Token
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
//FOS
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
//TEST
use Symfony\Component\Security\Core\Security;


/**
 * @Route("/")
 */
class MainController extends AbstractController
{
    private $tokenManager;
    private $manager;
    private $factory;
    private $tokenStorage;
    private $security;


    public function __construct(CsrfTokenManagerInterface $tokenManager = null, UserManagerInterface $manager, EncoderFactoryInterface $factory,TokenStorageInterface $tokenStorage, Security $security)
    {
        $this->tokenManager = $tokenManager;
        $this->manager = $manager;
        $this->factory = $factory;
        $this->tokenStorage = $tokenStorage;
        $this->security = $security;
    }

    /**
     * @Route("/home", name="home")
     */
    public function homeAction()
    {
        if($this->hasPermission('ROLE_USER'))
        {
            return $this->render('home.html.twig',
                array(
                    'username' => $_SESSION['fos_user_logged_name'],
                    'role'=>'ROLE_USER',
                    'roles' => $_SESSION['fos_user_roles']
                )
            );
        }

        return $this->redirectToRoute('main');
    }

    /**
     * @Route("/", name="main")
     */
    public function renderLogin()
    {
        $csrfToken = $this->tokenManager
            ? $this->tokenManager->getToken('authenticate')->getValue()
            : null;

        $data = array(
            'last_username' => '',
            'error' => null,
            'csrf_token' => $csrfToken
        );

        return $this->render('@FOSUser/Security/login.html.twig', $data);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request)
    {
        $username = $request->request->get("_username");
        $password = $request->request->get("_password");

        //trying to use fos_user.user_manager
        $user_manager = $this->manager;
        $factory = $this->factory;

        $user = $user_manager->findUserByUsername($username);

        if (!$user)
            return $this->redirectToRoute('main');

        $encoder = $factory->getEncoder($user);
        $salt = $user->getSalt();

        if ($encoder->isPasswordValid($user->getPassword(), $password, $salt)) {
            /*user logged + roles*/
            $_SESSION['fos_user_logged_name'] = $username;
            $_SESSION['fos_user_logged'] = $user;
            $_SESSION['fos_user_roles'] = $user->getRoles();
            $this->user = $user;

            return $this->redirectToRoute('home', array('username'=>$username));
        } else {
            return $this->redirectToRoute('main');
        }
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        return new Response('logout');
    }

    public function hasPermission(string $role)
    {
        if(isset($_SESSION['fos_user_logged'])){
            $user = $_SESSION['fos_user_logged'];

            if($user){
                $roles = $user->getRoles();

                foreach ($roles as $value)
                    if($value === $role){
                        return true;
                    }
            }
        }

        return false;
    }

    /**
     * @Route("/show", name="show")
     */
    public function showUsersAction()
    {
        //checking permission
        if(!isset($_SESSION['fos_user_roles']))
            return $this->redirectToRoute('main');

        else{
            $roles = $_SESSION['fos_user_roles'];
            $result = '';

            foreach ($roles as $value)
                if($value == 'ROLE_ADMIN')
                    $result = 'ROLE_ADMIN';

            if($result == '')
                return $this->redirectToRoute('main');
        }//checking permission

        $users = $this->getDoctrine()->getRepository('App:User')->findAll();

        return $this->render('security/index.html.twig', array('users' => $users, 'roles' => array()));
    }

    /**
     * @Route("/showOne/{id}", name="showOne")
     */
    public function showOneUserAction($id)
    {
        //checking permission
        if(!isset($_SESSION['fos_user_roles']))
            return $this->redirectToRoute('main');

        else{
            $roles = $_SESSION['fos_user_roles'];
            $result = '';

            foreach ($roles as $value)
                if($value == 'ROLE_ADMIN')
                    $result = 'ROLE_ADMIN';

            if($result == '')
                return $this->redirectToRoute('main');
        }//checking permission

        $user = $this->getDoctrine()->getRepository('App:User')->find(intval($id));

        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * @Route("/editUser/{id}", name="editUser")
     */
    public function editUserAction(Request $request, $id)
    {
        //checking permission
        if(!isset($_SESSION['fos_user_roles']))
            return $this->redirectToRoute('main');

        else{
            $roles = $_SESSION['fos_user_roles'];
            $result = '';

            foreach ($roles as $value)
                if($value == 'ROLE_ADMIN')
                    $result = 'ROLE_ADMIN';

            if($result == '')
                return $this->redirectToRoute('main');
        }//checking permission

//        var_dump($request->request); die();

        $user = $this->getDoctrine()->getRepository('App:User')->find(intval($id));
        $username = $request->request->get('fos_user_profile_form')['username'];
        $useremail = $request->request->get('fos_user_profile_form')['email'];
        $role_user = $request->request->get('role_user_checkbox');
        $role_admin = $request->request->get('role_admin_checkbox');

        $user->setUsername($username);
        $user->setEmail($useremail);


        if(isset($role_user))
            $user->addRole($role_user);
        if($role_user == null)
            $user->removeRole('ROLE_USER');
        if(isset($role_admin))
            $user->addRole($role_admin);
        if($role_admin == null)
            $user->removeRole('ROLE_ADMIN');

        $manager = $this->getDoctrine()->getManager();
        $manager->persist($user);
        $manager->flush();

        return $this->render('@FOSUser/Profile/show.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * @Route("/asd", name="asd")
     */
    public function asdAction()
    {
//        $user = $this->container->get('security.context')->getToken()->getUser();
//        $repo = $this->getDoctrine()->getRepository(UserRepository::class);
//        $asd = $repo->find($_SESSION['fos_user_logged_name']);
//        var_dump($_SESSION['fos_user_roles']);
//        die();
    }
}
