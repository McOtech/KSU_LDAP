<?php

namespace App\Http\Traits;

trait ADUser
{
  use Utils;

  /**
   * @param object $connection: LDAP connection object
   * @param string $domain: The LDAP server domain.
   * @param string $userCN: User Common Name.
   * @param array $user: user object containing all required details.
   * @param array $groups: group object containing user groups in an hirrachical order
   * @return string, the message of the operations.
   */
  private function addUser($userCN, $user = [], $groups = [])
  {
    $domain = env('LDAP_DOMAIN_NAME');
    $ldapServerConnection = $this->ldapConnection();
    $bind = $this->bindToServer($domain);

    if ($bind) {
      $organizationUnit = function ($name) {
        return $this->organizationUnit($name);
      };

      $domainController = function ($domain) {
        return $this->domainController($domain);
      };

      if (!empty($user) && !empty($groups)) {
        try {
          $DC = explode(".", $domain);
          $DC = array_map($domainController, $DC);
          $DC = implode(",", $DC);

          $CN = "CN=" . $userCN;

          $OUs = array_map($organizationUnit, $groups);
          $OUs = implode(",", $OUs);

          $userDN = "{$CN},{$OUs},{$DC}";

          if (!isset($ldapServerConnection[env('MESSAGE_LITERAL')])) {
            $isAdded = ldap_add($ldapServerConnection, $userDN, $user);
            if ($isAdded) {
              return $this->alert(env('SUCCESS_MESSAGE'), "User created successfully.");
            } else {
              return $this->alert(env('ERROR_MESSAGE'), "Error creating user!");
            }
          } else {
            return $ldapServerConnection;
          }
        } catch (\Throwable $th) {
          return $this->alert(env('ERROR_MESSAGE'), $th->getMessage());
        }
      } else {
        return $this->alert(env('WARNING_MESSAGE'), "User or Group Details empty!");
      }
    } else {
      $this->alert(env('ERROR_MESSAGE'), "System Error! Account not created.");
    }
  }

  /**
   * @param object $connection: LDAP connection object
   * @param string $domain: The LDAP server domain.
   * @param string $userCN: User Common Name.
   * @param array $user: user object containing all required details.
   * @param array $groups: group object containing user groups in an hirrachical order
   * @return string, the message of the operations.
   */
  private function updateUser($userCN, $user = [], $groups = [])
  {
    $domain = env('LDAP_DOMAIN_NAME');
    $ldapServerConnection = $this->ldapConnection();
    $bind = $this->bindToServer($domain);

    if ($bind) {
      $organizationUnit = function ($name) {
        return $this->organizationUnit($name);
      };

      $domainController = function ($domain) {
        return $this->domainController($domain);
      };

      if (!empty($user) && !empty($groups)) {
        try {
          $DC = explode(".", $domain);
          $DC = array_map($domainController, $DC);
          $DC = implode(",", $DC);

          $CN = "CN=" . $userCN;

          $OUs = array_map($organizationUnit, $groups);
          $OUs = implode(",", $OUs);

          $userDN = "{$CN},{$OUs},{$DC}";

          if (!isset($ldapServerConnection[env('MESSAGE_LITERAL')])) {
            $isUpdated = ldap_mod_replace($ldapServerConnection, $userDN, $user);
            if ($isUpdated) {
              return $this->alert(env('SUCCESS_MESSAGE'), "User details updated successfully.");
            } else {
              return $this->alert(env('ERROR_MESSAGE'), "Error updating user details!");
            }
          } else {
            return $ldapServerConnection;
          }
        } catch (\Throwable $th) {
          return $this->alert(env('ERROR_MESSAGE'), $th->getMessage());
        }
      } else {
        return $this->alert(env('WARNING_MESSAGE'), "User or Group Details empty!");
      }
    } else {
      $this->alert(env('ERROR_MESSAGE'), "System Error! Account not updated.");
    }
  }

  /**
   * @param object $connection: LDAP connection object
   * @param string $domain: The LDAP server domain.
   * @param string $userCN: User Common Name.
   * @param array $groups: group object containing user groups in an hirrachical order
   * @return string, the message of the operations.
   */
  private function destroyUser($userCN, $groups = [])
  {
    $domain = env('LDAP_DOMAIN_NAME');
    $ldapServerConnection = $this->ldapConnection();
    $bind = $this->bindToServer($domain);

    if ($bind) {
      $organizationUnit = function ($name) {
        return $this->organizationUnit($name);
      };

      $domainController = function ($domain) {
        return $this->domainController($domain);
      };

      if (!empty($groups)) {
        try {
          $DC = explode(".", $domain);
          $DC = array_map($domainController, $DC);
          $DC = implode(",", $DC);

          $CN = "CN=" . $userCN;

          $OUs = array_map($organizationUnit, $groups);
          $OUs = implode(",", $OUs);

          $userDN = "{$CN},{$OUs},{$DC}";

          if (!isset($ldapServerConnection[env('MESSAGE_LITERAL')])) {
            $isDeleted = ldap_delete($ldapServerConnection, $userDN);
            if ($isDeleted) {
              return $this->alert(env('SUCCESS_MESSAGE'), "User deleted successfully.");
            } else {
              return $this->alert(env('ERROR_MESSAGE'), "Error deleting user!");
            }
          } else {
            return $ldapServerConnection;
          }
        } catch (\Throwable $th) {
          return $this->alert(env('ERROR_MESSAGE'), $th->getMessage());
        }
      } else {
        return $this->alert(env('WARNING_MESSAGE'), "Group Details empty!");
      }
    } else {
      $this->alert(env('ERROR_MESSAGE'), "System Error! Account not destroyed.");
    }
  }

  /**
   * Prepares user object ready for submission to the LDAP server.
   */
  private function prepareUserAccountDetails($fname, $initials, $lname, $username, $password, $email, $mobile, $groups = [], $existingUser = [])
  {
    $domain = env('LDAP_DOMAIN_NAME');

    try {
      $user = [];

      $user['givenname'][0] = $fname;
      $user['initials'][0] = $initials;
      $user['sn'][0] = $lname;

      if (!isset($existingUser['cn'])) {
        $organizationUnit = function ($name) {
          return $this->organizationUnit($name);
        };

        $domainController = function ($domain) {
          return $this->domainController($domain);
        };

        $DC = explode(".", $domain);
        $DC = array_map($domainController, $DC);
        $DC = implode(",", $DC);

        $OUs = array_map($organizationUnit, $groups);
        $OUs = implode(",", $OUs);

        $user['objectclass'][0] = "top";
        $user['objectclass'][1] = "person";
        $user['objectclass'][2] = "organizationalPerson";
        $user['objectclass'][3] = "user";
        $user['cn'][0] = $user['givenname'][0] . " " . $user['sn'][0];
        $user['samaccountname'][0] = $username;
        $user['userprincipalname'][0] = $user['samaccountname'][0] . "@{$domain}";
        $user['distinguishedname'][0] = "CN={$user['cn'][0]},{$OUs},{$DC}";
      }

      $user['mail'][0] = $email;
      $user['mobile'][0] = $mobile;
      $user['instancetype'][0] = 4;

      // adds only over secure connection
      if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        $user['useraccountcontrol'][0] = 512;
        $user['unicodepwd'][0] = $this->preparedPassword($password);
      }
      return $user;
    } catch (\Throwable $th) {
      return $this->alert(env('ERROR_MESSAGE'), $th->getMessage());
    }
  }

  /**
   * Prepares the user object containing organizational information ready for LDAP server.
   */
  private function prepareUserOrgannizationalDetails($company, $department, $jobTitle, $description, $office, $postalAddress, $postalCode, $city, $county, $country, $subcounty, $street, $portfolio, $telephone)
  {

    try {
      $user = [];
      $user['company'][0] = $company;
      $user['department'][0] = $department;
      $user['title'][0] = $jobTitle;
      $user['description'][0] = $description;
      $user['physicaldeliveryofficename'][0] = $office;
      $user['postofficebox'][0] = $postalAddress;
      $user['postalcode'][0] = $postalCode;
      $user['l'][0] = $city;
      $user['st'][0] = $county;
      $user['co'][0] = $country;
      $user['homephone'][0] = $subcounty;
      $user['streetaddress'][0] = $street;
      $user['wwwhomepage'][0] = $portfolio;
      $user['telephonenumber'][0] = $telephone;
      return $user;
    } catch (\Throwable $th) {
      return $this->alert(env('ERROR_MESSAGE'), $th->getMessage());
    }
  }

  /**
   * @param string $domain parameter (organization unit)
   * Adds a valid organizational unit string
   * @return string string.
   */
  private function domainController($domain_name_section)
  {
    return "DC=" . $domain_name_section;
  }

  /**
   * @param string $name parameter (organization unit)
   * Adds a valid organizational unit string
   * @return string string.
   */
  private function organizationUnit($value)
  {
    return "OU=" . $value;
  }

  /**
   * @param string $passwordString
   * The function accepts a password and puts it in the right format to be used by LDAP  servers.
   * @return string
   */
  private function preparedPassword($passwordString)
  {
    $newPassword = '"' . $passwordString . '"';
    $newPass = iconv('UTF-8', 'UTF-16LE', $newPassword);
    return "{SHA}" . base64_encode(pack("H*", sha1($newPass)));
  }
}