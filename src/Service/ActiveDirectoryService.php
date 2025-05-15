<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Service;

use App\Entity\Default\Computer;
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
class ActiveDirectoryService
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
      $this->bindToLdap();
   }

   private function bindToLdap() {
      if ( strpos($this->ldapUser,$this->internetDomain) > 0 ) {
         $this->ldapUser = substr($this->ldapUser, 0, strpos($this->ldapUser,$this->internetDomain) ); 
      }
      try {
         $this->ldap->bind($this->ldapUser, $this->ldapPassword);
         $this->bindSuccessfull = true;
      } catch (ConnectionException $e) {
         if (trim($e->getMessage()) === "Can't contact LDAP server") {
            throw new CustomUserMessageAuthenticationException("connection_error");
         } else {
            throw new \Exception($e->getMessage());
         }
         $this->bindSuccessfull = false;
      }
      return $this->bindSuccessfull;
   }

   public function getComputers() {
      $computers = [];
      $query = $this->ldap->query($this->ldapComputerDn, $this->ldapComputerFilter);
      $results = $query->execute()->toArray();
      foreach ($results as $entry) {
         $computer = Computer::createFromLdapEntry($entry);
         $computers[] = $computer;
      }
      return $computers;
   }
}

