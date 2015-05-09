<?php

namespace ACS\ACSPanelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class DnsDomainType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO: Do the addition of fields with suscriber
        global $kernel;

        if ('AppCache' == get_class($kernel)) {
            $kernel = $kernel->getKernel();
        }
        $security = $kernel->getContainer()->get('security.context');
        $user = $security->getToken()->getUser();
        $user_domains = $this->container->get('domain_repository')->getUserViewable($user);

        $builder
            ->add('domain','entity',array(
                    'class' => 'ACS\ACSPanelBundle\Entity\Domain',
                    'choices' => $user_domains,
                )
            )
            ->add('type', 'choice', array(
                'choices' => array(
                    'MASTER' => 'master',
                    'SLAVE' => 'slave'
                )
            ))
            ->add('master')
            ->add('service')
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ACS\ACSPanelBundle\Entity\DnsDomain'
        ));
    }

    public function getName()
    {
        return 'acs_acspanelbundle_pdnsdomaintype';
    }
}
