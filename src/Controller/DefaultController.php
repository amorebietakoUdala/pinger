<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Controller;

use App\Entity\Computer;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Ldap\Exception\ConnectionException;
use Symfony\Component\Ldap\LdapInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

/**
 * Description of DefaultController.
 *
 * @author ibilbao
 **/
class DefaultController extends BaseController
{
    public function __construct(
        private LdapInterface $ldap,
        private string $internetDomain,
        private string $domain,
        private string $ldapUser, 
        private string $ldapPassword,
        private string $ldapComputerDn,
        private string $ldapComputerFilter,
        private bool $bindSuccessfull = false) 
    {        
    }

    #[Route(path: '/', name: 'app_home', options: ['expose' => true])]
    public function home()
    {
        return $this->redirectToRoute('computer_index');
    }
}
