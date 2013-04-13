<?php

namespace ACS\ACSPanelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DomainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // TODO: Do the addition of fields with suscriber
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $service = $kernel->getContainer()->get('security.context');

        $builder
            ->add('domain')
            ->add('parent_domain')
            ->add('is_httpd_alias')
            ->add('is_dns_alias')
            ->add('is_mail_alias')
        ;
        if($service->isGranted('ROLE_ADMIN'))
            $builder->add('user');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ACS\ACSPanelBundle\Entity\Domain'
        ));
    }

    public function getName()
    {
        return 'acs_acspanelbundle_domaintype';
    }
}
