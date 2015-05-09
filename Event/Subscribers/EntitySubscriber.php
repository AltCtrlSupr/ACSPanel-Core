<?php
namespace ACS\ACSPanelBundle\Event\Subscribers;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

use ACS\ACSPanelBundle\Entity\DB;
use ACS\ACSPanelBundle\Entity\DatabaseUser;
use ACS\ACSPanelBundle\Entity\Domain;
use ACS\ACSPanelBundle\Entity\HttpdHost;
use ACS\ACSPanelBundle\Entity\HttpdUser;
use ACS\ACSPanelBundle\Entity\FtpdUser;
use ACS\ACSPanelBundle\Entity\IpAddress;
use ACS\ACSPanelBundle\Entity\MailDomain;
use ACS\ACSPanelBundle\Entity\MailWBList;
use ACS\ACSPanelBundle\Entity\PanelSetting;
use ACS\ACSPanelBundle\Entity\Server;
use ACS\ACSPanelBundle\Entity\Service;

use Symfony\Component\Security\Acl\Permission\MaskBuilder;

class EntitySubscriber implements EventSubscriber
{

    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getSubscribedEvents()
    {
        return array(
            'prePersist',
            'postPersist',
            'postUpdate',
            'preUpdate',
            'preRemove',
            'postRemove',
        );
    }

    public function preRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof DatabaseUser){
            $this->removeDatabase($entity);
        }

		$superadmins = $entityManager->getRepository('ACS\ACSPanelUsersBundle\Entity\FosUser')->getSuperadminUsers();
		$aclManager = $this->container->get('problematic.acl_manager');

		$domain_related_user_classes = array('HttpdHost', 'DnsDomain', 'MailAlias');
		foreach($domain_related_user_classes as $class){
			if($entity instanceof $class){
				$user = $entity->getDomain()->getUser();
				$this->removeUserOwnerPermission($user, $entity);
			}
		}

		$first_level_user_classes = array('DB', 'DatabaseUser', 'Domain', 'FtpdUser', 'IpAddress', 'LogItem', 'MailDomain', 'MailWBList', 'PanelSetting', 'Server', 'Service');
		foreach($first_level_user_classes as $class){
			if($entity instanceof $class){
				$user = $entity->getUser();
				$this->removeUserOwnerPermission($user, $entity);
			}
		}

		if($entity instanceof DnsRecord){
			$user = $entity->getDnsDomain()->getDomain()->getUser();
			$this->removeUserOwnerPermission($user, $entity);
		}

		if($entity instanceof HttpdUser){
			$user = $entity->getHttpdHost()->getDomain()->getUser();
			$this->removeUserOwnerPermission($user, $entity);
		}

		if($entity instanceof MailLogrcvd){
			$user = $entity->getMailDomain()->getUser();
			$this->removeUserOwnerPermission($user, $entity);
		}

		if($entity instanceof MailMailBox){
			$user = $entity->getMailDomain()->getUser();
			$this->removeUserOwnerPermission($user, $entity);
		}

		if($entity instanceof UserPlan){
			$user = $entity->getPuser();
			$this->removeUserOwnerPermission($user, $entity);
		}


		foreach($superadmins as $superadmin){
			$aclManager->removeObjectPermission($entity, MaskBuilder::MASK_MASTER, $superadmin);
		}


    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof DB){
            $this->createDatabase($entity);
            $this->setCreatedAtValue($entity);
            $this->setUserValue($entity);
        }
        if ($entity instanceof DatabaseUser){
            $this->setCreatedAtValue($entity);
            $this->setUserValue($entity);
        }
        if ($entity instanceof Domain){
            $this->setUserValue($entity);
        }
        if ($entity instanceof FtpdUser){
            $this->setUserValue($entity);
            $usertools = $this->container->get('acs.user.tools');
            $this->setUid($usertools->getAvailableUid());
        }
        if ($entity instanceof HttpdUser){
            $this->setProtectedDir($entity);
        }
        if ($entity instanceof IpAddress){
            $this->setUserValue($entity);
        }
        if ($entity instanceof MailDomain){
            $this->setUserValue($entity);
            $settings_manager = $this->continer->get('acs.setting_manager');
            $mail_domain_transport = $settings_manager->getSystemSetting('mail_domain_transport');
            if($mail_domain_transport){
                $entity->setTransport($mail_domain_transport);
            }
        }
        if ($entity instanceof MailWBList){
            $this->setUserValue($entity);
        }
        if ($entity instanceof PanelSetting){
            $this->setUserValue($entity);
        }
        if ($entity instanceof Server){
            $this->setUserValue($entity);
        }
        if ($entity instanceof Service){
            $this->setUserValue($entity);
        }

    }

    public function postRemove(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof DatabaseUser){
            $this->setUpdatedAtValue($entity);
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

		$superadmins = $entityManager->getRepository('ACS\ACSPanelUsersBundle\Entity\FosUser')->getSuperadminUsers();
		$aclManager = $this->container->get('problematic.acl_manager');



        if ($entity instanceof DatabaseUser){
            $this->createUserInDatabase($entity);
            $this->setUserValue($entity);
        }

        if ($entity instanceof FtpdUser){
            $setting_manager = $this->container->get('acs.setting_manager');
            $setting_manager->setInternalSetting('last_used_uid',$entity->getUid());
        }

		// ACL Related code

		$domain_related_user_classes = array('HttpdHost', 'DnsDomain', 'MailAlias');
		foreach($domain_related_user_classes as $class){
			if($entity instanceof $class){
				$user = $entity->getDomain()->getUser();
				$this->addUserOwnerPermission($user, $entity);
			}
		}

		$first_level_user_classes = array('DB', 'DatabaseUser', 'Domain', 'FtpdUser', 'IpAddress', 'LogItem', 'MailDomain', 'MailWBList', 'PanelSetting', 'Server', 'Service');
		foreach($first_level_user_classes as $class){
			if($entity instanceof $class){
				$user = $entity->getUser();
				$this->addUserOwnerPermission($user, $entity);
			}
		}

		if($entity instanceof DnsRecord){
			$user = $entity->getDnsDomain()->getDomain()->getUser();
			$this->addUserOwnerPermission($user, $entity);
		}

		if($entity instanceof HttpdUser){
			$user = $entity->getHttpdHost()->getDomain()->getUser();
			$this->addUserOwnerPermission($user, $entity);
		}

		if($entity instanceof MailLogrcvd){
			$user = $entity->getMailDomain()->getUser();
			$this->addUserOwnerPermission($user, $entity);
		}

		if($entity instanceof MailMailBox){
			$user = $entity->getMailDomain()->getUser();
			$this->addUserOwnerPermission($user, $entity);
		}

		if($entity instanceof UserPlan){
			$user = $entity->getPuser();
			$this->addUserOwnerPermission($user, $entity);
		}


		foreach($superadmins as $superadmin){
			$aclManager->addObjectPermission($entity, MaskBuilder::MASK_MASTER, $superadmin);
		}

    }

    public function preUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof DatabaseUser){
            $this->removeUserInDatabase($entity);
            $this->createUserInDatabase($entity);
            $this->setUpdatedAtValue($entity);
        }
        if ($entity instanceof Domain){
            $this->setUpdatedAtValue($entity);
        }
        if ($entity instanceof HttpdHost){
            $this->setUpdatedAtValue($entity);
		}
    }

    public function postUpdate(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        $entityManager = $args->getEntityManager();

        if ($entity instanceof DatabaseUser){
            $this->removeUserInDatabase();
        }
    }

    private function createUserInDatabase($entity)
    {
        $admin_user = '';
        $admin_password = '';
        if(!$entity->getDb()->getService())
            return;
        $settings = $entity->getDb()->getService()->getSettings();
        foreach ($settings as $setting){
            if($setting->getSettingKey() == 'admin_user')
                $admin_user = $setting->getValue();
            if($setting->getSettingKey() == 'admin_password')
                $admin_password = $setting->getValue();
        }
        $server_ip = $entity->getDb()->getService()->getIp();


        $config = new \Doctrine\DBAL\Configuration();

        $connectionParams = array(
            'user' => $admin_user,
            'password' => $admin_password,
            'host' => $server_ip,
            'driver' => 'pdo_mysql',
        );

        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $sql = "CREATE USER '".$entity->getUsername()."'@'%' IDENTIFIED BY '".$entity->getPassword()."'";
        $conn->executeQuery($sql);
        $sql = "GRANT ALL PRIVILEGES ON `".$entity->getDb()."`.* TO '".$entity->getUsername()."'@'%'";
        $conn->executeQuery($sql);
        $sql = "FLUSH PRIVILEGES";
        $conn->executeQuery($sql);
    }

    public function removeUserInDatabase($entity)
    {
        $admin_user = '';
        $admin_password = '';
        $settings = $entity->getDb()->getService()->getSettings();
        foreach ($settings as $setting){
            if($setting->getSettingKey() == 'admin_user')
                $admin_user = $setting->getValue();
            if($setting->getSettingKey() == 'admin_password')
                $admin_password = $setting->getValue();
        }
        $server_ip = $entity->getDb()->getService()->getIp();

        $config = new \Doctrine\DBAL\Configuration();
        //..
        $connectionParams = array(
            'user' => $admin_user,
            'password' => $admin_password,
            'host' => $server_ip,
            'driver' => 'pdo_mysql',
        );

        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $sql = "DROP USER '".$entity->getUsername()."'@'%';";

        $conn->executeQuery($sql);
    }

    private function setCreatedAtValue($entity)
    {
        if(!$entity->getCreatedAt())
        {
            $entity->setCreatedAt( new \DateTime());
        }
    }

    private function setUpdatedAtValue($entity)
    {
        $entity->setUpdatedAt(new \DateTime());
    }

    public function createDatabase($entity)
    {
        $admin_user = '';
        $admin_password = '';

        $settings = array();
        if(!$entity->getService())
            return;

        $settings = $entity->getService()->getSettings();

        foreach ($settings as $setting){
            if($setting->getSettingKey() == 'admin_user')
                $admin_user = $setting->getValue();
            if($setting->getSettingKey() == 'admin_password')
                $admin_password = $setting->getValue();
        }

        $server_ip = $entity->getService()->getIp();

        $config = new \Doctrine\DBAL\Configuration();
        //..
        $connectionParams = array(
            'user' => $admin_user,
            'password' => $admin_password,
            'host' => $server_ip,
            'driver' => 'pdo_mysql',
        );
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $sql = "CREATE DATABASE IF NOT EXISTS ".$entity->getName()." DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
        $conn->executeQuery($sql);

        return $this;

    }

    public function setProtectedDir($entity)
    {
        $settings = $this->container->get('acs.setting_manager');
        $service = $this->container->get('security.context');

        $user = $service->getToken()->getUser();
	if(!$entity->getProtectedDir())
		return $entity->setProtectedDir($settings->getSystemSetting('home_base').$user->getUsername().'/web/'.$entity->getHttpdHost()->getDomain()->getDomain().'/httpdocs');
	else
		return $entity;
    }

    public function removeDatabase($entity)
    {
        $admin_user = '';
        $admin_password = '';
        $settings = $entity->getService()->getSettings();
        foreach ($settings as $setting){
            if($setting->getSettingKey() == 'admin_user')
                $admin_user = $setting->getValue();
            if($setting->getSettingKey() == 'admin_password')
                $admin_password = $setting->getValue();
        }
        $server_ip = $entity->getService()->getIp();

        $config = new \Doctrine\DBAL\Configuration();

        $connectionParams = array(
            'user' => $admin_user,
            'password' => $admin_password,
            'host' => $server_ip,
            'driver' => 'pdo_mysql',
        );
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        $users = $entity->getDatabaseUsers();
        if(count($users)){
            foreach($users as $usr){
                $sql = "GRANT ALL PRIVILEGES ON `".$entity->getName()."` . * TO '".$usr->getUsername()."'@'%'";
                $conn->executeQuery($sql);
                $sql = "DROP USER '".$usr->getUsername()."'@'%'";
                $conn->executeQuery($sql);
            }
        }

        $sql = "DROP DATABASE IF EXISTS ".$entity->getName();
        $conn->executeQuery($sql);

        return $entity;

    }

    public function setUserValue($entity)
    {
        if($entity->getUser())
            return;
        $service = $this->container->get('security.context');
        if(!$service->getToken())
            return;
        $user = $service->getToken()->getUser();
        return $entity->setUser($user);
    }

	public function addUserOwnerPermission($user, $entity)
	{
		$aclManager = $this->container->get('problematic.acl_manager');

		if($parent = $user->getParentUser())
			$aclManager->addObjectPermission($entity, MaskBuilder::MASK_MASTER, $parent);

		$aclManager->addObjectPermission($entity, MaskBuilder::MASK_OWNER, $user);
	}

	public function removeUserOwnerPermission($user, $entity)
	{
		$aclManager = $this->container->get('problematic.acl_manager');

		if($parent = $user->getParentUser())
			$aclManager->revokePermission($entity, MaskBuilder::MASK_MASTER, $parent);

		$aclManager->revokePermission($entity, MaskBuilder::MASK_OWNER, $user);
	}


}
