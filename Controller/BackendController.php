<?php

namespace Display\PushBundle\Controller;

use Display\PushBundle\Form\PushType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Backend controller.
 */
class BackendController extends Controller
{
    /**
     * @Route("/", name="backend")
     * @Template()
     */
    public function indexAction()
    {
        $form = $this->createPushForm();

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/send", name="backend_send")
     * @Template("DisplayPushBundle:Backend:index.html.twig")
     */
    public function sendAction(Request $request)
    {
        $form = $this->createPushForm();
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->get('session')->getFlashBag()->set('alert', array('alert-success' => 'Push has been send.'));

            $pm = $this->get('display.push.manager');
            $data = $form->getData();
            $uids = array_filter(array_map('trim', explode(';', $data['uid'])));
            $pm->sendMessage($data['text'], $data['os'], $data['locale'], $uids);

            return $this->redirect($this->generateUrl('backend'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @param array $data
     * @return \Symfony\Component\Form\Form
     */
    private function createPushForm($data = array())
    {
        $form = $this->createForm(new PushType(), $data, array(
            'action' => $this->generateUrl('backend_send'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array(
            'label' => 'Envoyer',
            'attr' => array('class' => 'btn btn-default')
        ));

        return $form;
    }
} 