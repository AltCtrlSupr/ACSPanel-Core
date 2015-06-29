<?php

namespace ACS\ACSPanelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use ACS\ACSPanelBundle\Entity\Domain;
use ACS\ACSPanelBundle\Entity\DnsDomain;
use ACS\ACSPanelBundle\Form\DomainType;
use ACS\ACSPanelBundle\Event\DnsEvents;
use ACS\ACSPanelBundle\Event\FilterDnsEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Domain controller.
 *
 * @Rest\RouteResource("Domain")
 */
class DomainController extends FOSRestController
{
    /**
     * Lists all Domain entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // IF is admin can see all the hosts, if is user only their ones...
        $entities = $this->get('domain_repository')->getUserViewable($this->get('security.context')->getToken()->getUser());

        $view = $this->view($entities, 200)
            ->setTemplate("ACSACSPanelBundle:Domain:index.html.twig")
            ->setTemplateVar("entities")
            ->setTemplateData(array('search_action' => 'domain_search'))
        ;

        return $this->handleView($view);
    }

    /**
     * Finds and displays a Domain search results.
     *
     * @Rest\View("ACSACSPanelBundle:Domain:index.html.twig")
     * @Rest\Get("/domains/{term}/search")
     */
    public function searchAction($term)
    {
        $em = $this->getDoctrine()->getManager();
        $rep = $em->getRepository('ACSACSPanelBundle:Domain');

        $query = $rep->createQueryBuilder('d')
            ->where('d.id = ?1')
            ->orWhere('d.domain LIKE ?2')
            ->setParameter('1', $term)
            ->setParameter('2', '%'.$term.'%')
            ->getQuery()
        ;

        $template_vars = array(
            'search_action' => 'domain_search',
            'term' => $term,
        );

        $entities = $query->execute();

        $view = $this->view($entities, 200)
            ->setTemplate("ACSACSPanelBundle:Domain:index.html.twig")
            ->setTemplateVar("entities")
            ->setTemplateData($template_vars)
        ;

        return $this->handleView($view);
    }

    /**
     * Finds and displays a Domain entity.
     *
     * @Rest\Get("/domains/{id}/show")
     */
    public function showAction($id)
    {
        $entity = $this->getEntity($id);

        $em = $this->getDoctrine()->getManager();
        $dnsdomains = $em->getRepository('ACSACSPanelBundle:DnsDomain')->findByDomain($entity);
        $maildomains = $em->getRepository('ACSACSPanelBundle:MailDomain')->findByDomain($entity);
        $delete_form = $this->createDeleteForm($id);

        $template_data = array(
            'dnsdomains' => $dnsdomains,
            'maildomains' => $maildomains,
            'delete_form' => $delete_form->createView()
        );

        $view = $this->view($entity, 200)
            ->setTemplate("ACSACSPanelBundle:Domain:show.html.twig")
            ->setTemplateVar("entity")
            ->setTemplateData($template_data)
        ;

        return $this->handleView($view);
    }

    /**
     * Displays a form to create a new Domain entity.
     *
     * @Rest\View()
     */
    public function newAction()
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        if (!$user->canUseResource('Domain', $em)) {
            return $this->render('ACSACSPanelBundle:Error:resources.html.twig', array(
                'entity' => 'Domain'
            ));
        }

        $entity = new Domain();
        $form   = $this->createForm(new DomainType($this->container), $entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new Domain entity.
     *
     * @Rest\Post("/domains/create")
     * @Rest\View("ACSACSPanelBundle:Domain:new.html.twig", templateVar="entity")
     */
    public function createAction(Request $request)
    {
        $entity  = new Domain();
        $form = $this->createForm(new DomainType($this->container), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setEnabled(true);

            $this->container->get('event_dispatcher')->dispatch(DnsEvents::DOMAIN_BEFORE_ADD, new FilterDnsEvent($entity, $em));

            $em->persist($entity);

            $em->flush();

            if($form['add_dns_domain']->getData()){
                $dnsdomain = new DnsDomain();
                $dnsdomain->setDomain($entity);
                $dnsdomain->setType('master');
                $dnsdomain->setEnabled(true);
                $dnstypes = $em->getRepository('ACSACSPanelBundle:ServiceType')->getDNSServiceTypesIds();
                // TODO: Change somehow to get a default DNS server
                $dnsservice = $em->getRepository('ACSACSPanelBundle:Service')->findByType($dnstypes);

                if (count($dnsservice)) {
                    $dnsdomain->setService($dnsservice[0]);
                }

                $this->container->get('event_dispatcher')->dispatch(DnsEvents::DNS_AFTER_DOMAIN_ADD, new FilterDnsEvent($dnsdomain, $em));

                $em->persist($dnsdomain);
                $em->flush();
            }

            // It only works with 201 code
            $view = $this->routeRedirectView('domain_show', array('id' => $entity->getId()), 201);
            return $this->handleView($view);
        }

        return $form;
    }

    /**
     * Displays a form to edit an existing Domain entity.
     *
     * @Rest\View()
     */
    public function editAction($id)
    {
        $entity = $this->getEntity($id);

        $editForm = $this->createForm(new DomainType($this->container), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Domain entity.
     *
     * @Rest\View()
     */
    public function updateAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new DomainType($this->container), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('domain_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Domain entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $entity = $this->getEntity($id);

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('domain'));
    }

    public function setaliasAction(Request $request, $id, $type)
    {
        $entity = $this->getEntity($id);

       switch($type){
           case 'dns':
             $entity->setIsDnsAlias(!$entity->getIsDnsAlias());
             break;
           case 'httpd':
             $entity->setIsHttpdAlias(!$entity->getIsHttpdAlias());
             break;
           case 'mail':
             $entity->setIsMailAlias(!$entity->getIsMailAlias());
             break;
           default:
             throw $this->createException('Type not valid');
             break;
       }

       $em->persist($entity);
       $em->flush();

       return $this->redirect($this->generateUrl('domain'));
    }

    public function setenabledAction(Request $request, $id)
    {
        $entity = $this->getEntity($id);

        $entity->setEnabled(!$entity->getEnabled());
        $em->persist($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('domain'));
    }

    private function getEntity($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('ACSACSPanelBundle:Domain')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Domain entity.');
        }

        return $entity;
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
