<?php

namespace ACS\ACSPanelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use ACS\ACSPanelBundle\Entity\DnsDomain;
use ACS\ACSPanelBundle\Entity\DnsRecord;
use ACS\ACSPanelBundle\Form\DnsDomainType;

use ACS\ACSPanelBundle\Event\FilterDnsEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

use ACS\ACSPanelBundle\Event\DnsEvents;

/**
 * DnsDomain controller.
 *
 * @Rest\RouteResource("DnsDomain")
 */
class DnsDomainController extends FOSRestController
{
    /**
     * Lists all DnsDomain entities.
     *
     * @Rest\View()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // IF is admin can see all the dnsdomains, if is user only their ones...
        $entities = $this->get('dnsdomain_repository')->getUserViewable($this->get('security.context')->getToken()->getUser());

        $view = $this->view($entities, 200)
            ->setTemplate("ACSACSPanelBundle:DnsDomain:index.html.twig")
            ->setTemplateVar("entities")
            ->setTemplateData(array('search_action' => 'dnsdomain_search'))
            ;

        return $this->handleView($view);
    }

    /**
     * Finds and displays a DnsDomain entity.
     *
     * @Rest\Get("/dnsdomains/{id}/show")
     */
    public function showAction($id)
    {
        $entity = $this->getEntity($id);

        $deleteForm = $this->createDeleteForm($id);

        $template_data = array(
            'search_action' => 'dnsdomain_search',
            'delete_form' => $deleteForm->createView()
        );

        $view = $this->view($entity, 200)
            ->setTemplate("ACSACSPanelBundle:DnsDomain:show.html.twig")
            ->setTemplateVar("entity")
            ->setTemplateData($template_data)
            ;

        return $this->handleView($view);
    }

    /**
     * Displays a form to create a new DnsDomain entity.
     *
     */
    public function newAction()
    {

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user->canUseResource('DnsDomain',$em)) {
            return $this->render('ACSACSPanelBundle:Error:resources.html.twig', array(
                'entity' => 'Dns Domain'
            ));
        }

        $entity = new DnsDomain();
        $form   = $this->createForm(new DnsDomainType($this->container), $entity);

        return $this->render('ACSACSPanelBundle:DnsDomain:new.html.twig', array(
            'entity' => $entity,
            'search_action' => 'dnsdomain_search',
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a new DnsDomain entity.
     *
     * @Rest\Post("/dnsdomains/create")
     * @Rest\View("ACSACSPanelBundle:DnsDomain:new.html.twig", templateVar="entity")
     */
    public function createAction(Request $request)
    {
        $entity  = new DnsDomain();
        $form = $this->createForm(new DnsDomainType($this->container), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setEnabled(true);
            $em->persist($entity);
            $em->flush();

            $this->container->get('event_dispatcher')->dispatch(DnsEvents::DNS_AFTER_DOMAIN_ADD, new FilterDnsEvent($entity,$em));

            $view = $this->routeRedirectView('dnsdomain_show', array('id' => $entity->getId()), 201);
            return $this->handleView($view);
        }

        return $form;
    }

    /**
     * Displays a form to edit an existing DnsDomain entity.
     *
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);

        $editForm = $this->createForm(new DnsDomainType($this->container), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('ACSACSPanelBundle:DnsDomain:edit.html.twig', array(
            'entity'      => $entity,
            'search_action' => 'dnsdomain_search',
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Edits an existing DnsDomain entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $this->getEntity($id);

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DnsDomainType($this->container), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();
            $this->container->get('event_dispatcher')->dispatch(DnsEvents::DNS_AFTER_DOMAIN_UPDATE, new FilterDnsEvent($entity,$em));

            return $this->redirect($this->generateUrl('dnsdomain_edit', array('id' => $id)));
        }

        return $this->render('ACSACSPanelBundle:DnsDomain:edit.html.twig', array(
            'search_action' => 'dnsdomain_search',
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                ));
        }

    /**
     * Deletes a DnsDomain entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entity = $this->getEntity($id);

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('dnsdomain'));
    }

    public function setenabledAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ACSACSPanelBundle:DnsDomain')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dns Domain entity.');
        }

        $entity->setEnabled(!$entity->getEnabled());
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('dnsdomain'));
    }

    /**
     * Finds and displays a DnsDomain search results.
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('ACSACSPanelBundle:DnsDomain');

        $term = $request->request->get('term');

        $query = $rep->createQueryBuilder('d')
            ->where('d.id = ?1')
            ->innerJoin('d.domain','dom')
            ->orWhere('dom.domain LIKE ?2')
            ->orWhere('d.type LIKE ?2')
            ->orWhere('d.account LIKE ?2')
            ->setParameter('1',$term)
            ->setParameter('2','%'.$term.'%')
            ->getQuery()
        ;

        $entities = $query->execute();

        return $this->render('ACSACSPanelBundle:DnsDomain:index.html.twig', array(
            'entities' => $entities,
            'term' => $term,
            'search_action' => 'dnsdomain_search',
        ));

    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
            ;
    }

    private function getEntity($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ACSACSPanelBundle:DnsDomain')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DnsDomain entity.');
        }

        if (!$entity->userCanSee($this->get('security.context'))) {
            throw new \Exception('You cannot edit this entity!');
        }

        return $entity;
    }
}
