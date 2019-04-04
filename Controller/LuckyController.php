<?php

/**
 * Created by PhpStorm.
 * User: naddi
 * Date: 2018-11-29
 * Time: 8:09 PM
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * @Route("/lucky")
 */
class LuckyController extends AbstractController
{
//    use TargetPathTrait;

    /**
     * @Route("/number/{max}", name="app_lucky_number")
     */
    public function numberAction($max)
    {
        //check if the user is login


        $number = random_int(0, $max);

        return new Response(
            '<html><body>Lucky number: '.$number.'</body></html>'
        );
    }

    /**
     * @Route("/template", name="app_lucky_template")
     */
    public function templateAction(Request $request)
    {
        $session = $request->getSession();
        $user = new User();

        if(is_null($session->getName()) || $user->getUsername() !== $session->getName())
            return $this->redirect('/login');


        return $this->render('mybase.html.twig', array(
            'id'=>$session->getName()
        ));
    }
}