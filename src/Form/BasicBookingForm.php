<?php
/**
 * @file
 * Contains \Drupal\basic_booking\Form\BasicBookingForm
 */
namespace Drupal\basic_booking\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class BasicBookingForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'basic_booking_form';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    //personal details needed for all applications, contained within fieldset set to be first elemenet
    $form['personal'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Personal Details'),
      '#weight' => -10
    ];

    $form['personal']['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#maxlength' => 128,
      '#required' => TRUE
    ];

    $form['personal']['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email'),
      '#maxlength' => 255,
      '#required' => TRUE
    ];

    $form['personal']['confirm_email'] = [
      '#type' => 'email',
      '#title' => $this->t('Confirm Email'),
      '#maxlength' => 255,
      '#required' => TRUE
    ];

    $form['personal']['phone_number'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone Number'),
      '#maxlength' => 15,
      '#required' => TRUE
    ];

    $form['personal']['postcode'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Postcode'),
      '#maxlength' => 128,
      '#required' => TRUE
    ];

    $form['personal']['house'] = [
      '#type' => 'textfield',
      '#title' => $this->t('House Name/Number'),
      '#maxlength' => 128,
      '#required' => TRUE
    ];

    $form['personal']['street'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Street Address'),
      '#maxlength' => 128,
      '#required' => TRUE
    ];

    $form['personal']['city'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Town/City'),
      '#maxlength' => 128,
      '#required' => TRUE
    ];

    $form['personal']['county'] = [
      '#type' => 'textfield',
      '#title' => $this->t('County'),
      '#maxlength' => 128,
      '#required' => TRUE
    ];

    //always place the submission button at the end of the form with the heaviest weight (10)
    $form['show'] = [
      '#type' => 'submit',
      '#value' => $this->t('Book'),
      '#weight' => 10
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // retreive values from form state object
    $name = $form_state->getValue('name');
    $email = $form_state->getValue('email');
    $confirm_email = $form_state->getValue('confirm_email');
    $phone_number = $form_state->getValue('phone_number');


    if (strlen(trim($name)) > 0 && !preg_match('/^[a-z A-Z\'\-]+$/', $name)) {
      $form_state->setErrorByName('_name', $this->t('Name contains invalid characters. Only letters are allowed!'));
    }

    // check emails address are in valid format and match
    if(!valid_email_address($email)){
      $form_state->setErrorByName('email', $this->t('Sorry the email address you entered is not valid'));
    }

    if(!valid_email_address($confirm_email)){
      $form_state->setErrorByName('confirm_email', $this->t('Sorry the email address you entered is not valid'));
    }

    if(strcmp($email,$confirm_email) != 0){
      $form_state->setErrorByName('confirm_email', $this->t('Sorry the email addresses you entered do not match'));
    }

    if (strlen($phone_number) > 0) {
      $chars = array(' ','-','(',')','[',']','{','}');
      $phone_number = str_replace($chars,'',$phone_number);

      if (preg_match('/^(\+)[\s]*(.*)$/',$phone_number)){
        $form_state->setErrorByName('phone_number', $this->t('UK telephone number without the country code, please'));
      }

      if (!preg_match('/^[0-9]{10,11}$/',$phone_number)) {
        $form_state->setErrorByName('phone_number', $this->t('UK telephone numbers should contain 10 or 11 digits'));
      }

      if (!preg_match('/^0[0-9]{9,10}$/',$phone_number)) {
        $form_state->setErrorByName('phone_number', $this->t('The telephone number should start with a 0'));
      }
    }

  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message($this->t('Thank you for you\'re booking. You will receive an email confirming your booking shortly'));
  }
}