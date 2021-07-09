<?php

namespace App\Http\Traits;

trait Utils
{
  private function alert($type, $message)
  {
    return [
      env('MESSAGE_LITERAL') => [
        "type" => $type,
        "description" => $message
      ]
    ];
  }

  private function preparedStringLiteral($string)
  {
    return ucfirst(strtolower($string));
  }

  private function preparedInitial($string)
  {
    return substr(strtoupper($string), 0, 1);
  }

  private function ldapConnection()
  {
    try {
      $server = env('LDAP_SERVER_IP_ADDRESS'); // "ldap://{env('LDAP_SERVER_NAME}.{env('LDAP_DOMAIN_NAME}";
      return ldap_connect($server, env('LDAP_DOMAIN_PORT'));
    } catch (\Throwable $th) {
      return $this->alert(env('ERROR_MESSAGE'), $th->getMessage());
    }
  }

  private function bindToServer($domain)
  {
    $ldapServerConnection = $this->ldapConnection();

    $admin = env('LDAP_ADMIN_USERNAME');
    $secret = env('LDAP_ADMIN_PASSWORD');
    $ldaprdn = explode(".", $domain)[0] . "\\" . $admin;
    ldap_set_option($ldapServerConnection, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldapServerConnection, LDAP_OPT_REFERRALS, 0);
    return ldap_bind($ldapServerConnection, $ldaprdn, $secret);
  }
}