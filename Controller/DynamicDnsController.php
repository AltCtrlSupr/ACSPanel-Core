<?php

namespace ACS\ACSPanelBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;

use ACS\ACSPanelBundle\Entity\DnsRecord;
use ACS\ACSPanelBundle\Entity\DnsDomain;
use ACS\ACSPanelBundle\Form\DnsRecordType;

use ACS\ACSPanelBundle\Event\FilterDnsEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

use ACS\ACSPanelBundle\Event\DnsEvents;

/**
 * DynamicDNS controller.
 */
class DynamicDnsController extends FOSRestController
{
    /**
     * Updates DNSRecord IP
     */
    public function updateAction(Request $request)
    {
        $new_ip = $request->get('myip');
        $hostname = $request->get('hostname');

        if (!$new_ip) {
            $new_ip = $request->server->get('REMOTE_ADDR');
        }

        $record = $this->__getRecordToUpdate($hostname);

        if ($record && $new_ip && $hostname) {
            $record->setContent($new_ip);

            $em = $this->getDoctrine()->getManager();
            $em->persist($record);
            $em->flush();

            $this->container->get('event_dispatcher')->dispatch(DnsEvents::DNS_AFTER_RECORD_UPDATE, new FilterDnsEvent($record, $em));

            $view = $this->view([], 200);

            return $view;
        }

        throw $this->createNotFoundException('You need to provide the required parameters');
    }

    private function __getRecordToUpdate($hostname)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('ACSACSPanelBundle:DnsRecord')->findOneBy(array(
            'type' => 'A',
            'name' => $hostname
        ));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find DnsRecord entity.');
        }

        return $entity;
    }

}
