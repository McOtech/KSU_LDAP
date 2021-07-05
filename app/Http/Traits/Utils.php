<?php

  namespace App\Http\Traits;

  trait Utils {
    private function alert($type, $message) {
      return [
        "message" => [
          "type" => $type,
          "description" => $message
        ]
        ];
    }
  }