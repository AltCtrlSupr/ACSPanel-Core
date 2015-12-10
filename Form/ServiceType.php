<?php

namespace ACS\ACSPanelBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use ACS\ACSPanelSettingsBundle\Form\EntitySettingType;
use ACS\ACSPanelSettingsBundle\Event\SettingsEvents;
use ACS\ACSPanelSettingsBundle\Event\FilterUserFieldsEvent;

class ServiceType extends AbstractType
{
    public $container;

    public function __construct($container){
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $service = $this->container->get('security.context');

        $user_fields = array();

        $this->container->get('event_dispatcher')->dispatch(SettingsEvents::BEFORE_LOAD_USERFIELDS, new FilterUserFieldsEvent($user_fields, $this->container));

        array_merge($user_fields, $user_fields = $this->container->getParameter("acs_settings.user_fields"));

        $builder
            ->add('name')
            ->add('ip')
            ->add('server', null, array('required' => true))
            ->add('type')
            ->add('settings', 'collection', array(
                'type' =>  new EntitySettingType($this->container, $user_fields)
            ))
        ;

        if ($service->isGranted('ROLE_ADMIN')) {
            $builder->add('user');
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'ACS\ACSPanelBundle\Entity\Service'
        ));
    }

    public function getName()
    {
        return 'acs_acspanelbundle_servicetype';
    }
}
