<?php 

namespace App\Enums;

enum PaymentMethodType:string{

   case Online="online";
   case Offline="offline";

   case CARD="Credit Card";
   case PAYPAL= "paypal";

   case BANK_TRANSFER="bank_transfer";
  
}