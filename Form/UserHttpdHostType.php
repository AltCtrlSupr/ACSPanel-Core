<?php

namespace ACS\ACSPanelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class UserHttpdHostType extends HttpdHostType
{
    public $container;

    public function __construct($container, $em){
        $this->container = $container;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $service = $this->container->get('security.context');
        $container = $this->container;

        $security = $container->get('security.context');

        $user = $security->getToken()->getUser();
        $child_ids = $user->getIdChildIds();
        $superadmin = false;
        if($security->isGranted('ROLE_SUPER_ADMIN'))
            $superadmin = true;

        $user_domains = $this->container->get('domain_repository')->getUserViewable($this->container->get('security.context')->getToken()->getUser());

        $web_services = $this->em->getRepository('ACS\ACSPanelBundle\Entity\ServiceType')->getWebServiceTypes();
        $webproxy_services = $this->em->getRepository('ACS\ACSPanelBundle\Entity\ServiceType')->getWebproxyServiceTypes();

        $builder
            ->add('domain','entity',array(
                'class' => 'ACS\ACSPanelBundle\Entity\Domain',
                'label' => 'httpdhost.form.domain',
                'choices' => $user_domains
            ))
            ->add('configuration', null, array('label' => 'httpdhost.form.configuration'))
            ->add('cgi', null, array('label' => 'httpdhost.form.cgi'))
            ->add('ssi', null, array('label' => 'httpdhost.form.ssi'))
            ->add('php', null, array('label' => 'httpdhost.form.php'))
            ->add('ssl', null, array('label' => 'httpdhost.form.ssl'))
            ->add('service', null, array(
                'label' => 'httpdhost.form.service',
				'choices' => $web_services
                )
            )
			->add('proxy_service', null, array(
				'label' => 'httpdhost.form.proxy_service',
				'choices' => $webproxy_services
			))
            ->add('add_www_alias','checkbox',array(
                'mapped' => false,
                'required' => false,
                'label' => 'httpdhost.form.addwwwalias'
            ))
            ->add('add_dns_record','checkbox',array(
                'mapped' => false,
                'required' => false,
                'label' => 'httpdhost.form.adddnsrecord'
            ))
            ->add('certificate', null, array('label' => 'httpdhost.form.certificate'))
            ->add('certificate_key', null, array('label' => 'httpdhost.form.certificate_key'))
            ->add('certificate_chain', null, array('label' => 'httpdhost.form.certificate_chain'))
            ->add('certificate_authority', null, array('label' => 'httpdhost.form.certificate_authority'))
        ;
    }

}
