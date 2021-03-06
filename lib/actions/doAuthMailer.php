<?php
/**
 * Listener for doAuthActions, recives notifies and sends emails
 *
 * @author Davert
 */
class doAuthMailer {

  public static function sendActivation(sfEvent $event) {

//    $controller = new sfComponents();

    $controller = $event->getSubject();
    $user = $controller->user;

    // already activated
    if ($user->getIsActive()) return;

    $activation = new UserActivationCode();
    $activation->setUserId($user->getId());
    $activation->setCode(doAuthTools::activationCode($user));
    $activation->save();

    $subject = sfConfig::get('sf_i18n') ? $controller->getContext()->getI18N()->__('Your activation code') :'Your activation code';

    // message should be sent immediately 
    $controller->getMailer()->composeAndSend(
      sfConfig::get('app_doAuth_email_from','mailer@'.$controller->getRequest()->getHost()),
      array($user->getEmail() => $user->getUsername()),
      $subject,
      $controller->getPartial('mail_activation', array('code'=> $activation->getCode())),'text/plain');

    $controller->getUser()->setAttribute('activation_code',$activation->getCode(),'doUser');
  }

  public static function sendRegistration(sfEvent $event) {

    $controller = $event->getSubject();
    $user = $controller->user;

    $subject = sfConfig::get('sf_i18n') ? $controller->getContext()->getI18N()->__('Thank you for registering') :'Thank you for registering';

    // message should be sent immediately
    $controller->getMailer()->composeAndSend(
      sfConfig::get('app_doAuth_email_from','mailer@'.$controller->getRequest()->getHost()),
      array($user->getEmail() => $user->getUsername()),
      $subject,
      $controller->getPartial('mail_registration', array('user'=> $controller->user, 'password'=> $controller->form->getValue('password'))),'text/plain');
  }

  

}
?>
