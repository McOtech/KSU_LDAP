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

  private function ldapConnection()
  {
    try {
      $server = "ldap://{env('LDAP_SERVER_NAME}.{env('LDAP_DOMAIN_NAME}";
      return ldap_connect($server, env('LDAP_DOMAIN_PORT'));
    } catch (\Throwable $th) {
      return $this->alert(env('ERROR_MESSAGE'), $th->getMessage());
    }
  }
}