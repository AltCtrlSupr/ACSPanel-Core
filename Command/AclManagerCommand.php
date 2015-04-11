<?php

namespace ACS\ACSPanelBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class AclManagerCommand extends ContainerAwareCommand
{

    private $domain_related_user_classes = array(
        'ACS\ACSPanelBundle\Entity\HttpdHost',
        'ACS\ACSPanelBundle\Entity\DnsDomain',
        'ACS\ACSPanelBundle\Entity\MailAlias'
    );

    private $first_level_user_classes = array(
        'ACS\ACSPanelBundle\Entity\DB',
        'ACS\ACSPanelBundle\Entity\DatabaseUser',
        'ACS\ACSPanelBundle\Entity\Domain',
        'ACS\ACSPanelBundle\Entity\FtpdUser',
        'ACS\ACSPanelBundle\Entity\IpAddress',
        'ACS\ACSPanelBundle\Entity\LogItem',
        'ACS\ACSPanelBundle\Entity\MailDomain',
        'ACS\ACSPanelBundle\Entity\MailWBList',
        'ACS\ACSPanelBundle\Entity\PanelSetting',
        'ACS\ACSPanelBundle\Entity\Server',
        'ACS\ACSPanelBundle\Entity\Service'
    );

    protected function configure()
    {
        $this
            ->setName('acl-manager:update-entity')
            ->setDescription('Update ACL entries based on current entity permissions')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Entity name to update based permissions'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $aclManager = $this->getContainer()->get('problematic.acl_manager');
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        // Adding master permissions to superadmins
        $superadmins = $em->getRepository('ACS\ACSPanelUsersBundle\Entity\FosUser')->getSuperadminUsers();

        $entities = $em->getRepository($name)->findAll();

        foreach ($entities as $entity) {

            foreach ($this->domain_related_user_classes as $class) {
                if ($entity instanceof $class) {
                    $user = $entity->getDomain()->getUser();
                    if ($user)
                        $output->writeln($this->addUserOwnerPermission($user, $entity));
                }
            }

            foreach ($this->first_level_user_classes as $class) {
                if ($entity instanceof $class) {
                    $user = $entity->getUser();
                    if ($user)
                        $output->writeln($this->addUserOwnerPermission($user, $entity));
                }
            }

            if ($entity instanceof ACS\ACSPanelUsersBundle\Entity\DnsRecord) {
                $user = $entity->getDnsDomain()->getDomain()->getUser();
                if($user)
                    $output->writeln($this->addUserOwnerPermission($user, $entity));
            }

            if ($entity instanceof ACS\ACSPanelUsersBundle\Entity\HttpdUser) {
                $user = $entity->getHttpdHost()->getDomain()->getUser();
                if($user)
                    $output->writeln($this->addUserOwnerPermission($user, $entity));
            }

            if ($entity instanceof ACS\ACSPanelUsersBundle\Entity\MailLogrcvd) {
                $user = $entity->getMailDomain()->getUser();
                if ($user)
                    $output->writeln($this->addUserOwnerPermission($user, $entity));
            }

            if ($entity instanceof ACS\ACSPanelUsersBundle\Entity\MailMailbox) {
                $user = $entity->getMailDomain()->getUser();
                if ($user)
                    $output->writeln($this->addUserOwnerPermission($user, $entity));
            }

            if ($entity instanceof ACS\ACSPanelUsersBundle\Entity\UserPlan) {
                $user = $entity->getPuser();
                if ($user)
                    $output->writeln($this->addUserOwnerPermission($user, $entity));
            }

            foreach ($superadmins as $superadmin) {
                $output->writeln($aclManager->addObjectPermission($entity, MaskBuilder::MASK_MASTER, $superadmin));
            }

        }
    }

	public function addUserOwnerPermission($user, $entity)
	{
		$aclManager = $this->getContainer()->get('problematic.acl_manager');

		if ($parent = $user->getParentUser())
			$aclManager->addObjectPermission($entity, MaskBuilder::MASK_MASTER, $parent);

		$aclManager->addObjectPermission($entity, MaskBuilder::MASK_OWNER, $user);

        return "Added ". get_class($entity) . " Acls for ".$user;

	}
}
