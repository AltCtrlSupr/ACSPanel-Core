<?php

namespace ACS\ACSPanelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ServerType extends AbstractType
{
    private $_security;

    public function __construct($security)
    {
        $this->_security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hostname')
            ->add('ip', new IpAddressType(),array('label' => false))
            ->add('description')
        ;
        if($this->_security->isGranted('ROLE_ADMIN'))
            $builder->add('user');
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ACS\ACSPanelBundle\Entity\Server'
        ));
    }

    public function getName()
    {
        return 'acs_acspanelbundle_servertype';
    }
}
