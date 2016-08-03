<?php
/**
 * @file
 * Contains \Drupal\course_booking\Form\PageExampleForm
 */
namespace Drupal\course_booking\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\basic_booking\Form\BasicBookingForm;

class CourseBookingForm extends BasicBookingForm {

  /**
   * Send confirmation email
   */
  public function course_booking_confirmation_mail_send($form_values){

    $mailManager = \Drupal::service('plugin.manager.mail');

    //construct email to send to form submitter for confirmation
    $module = 'course_booking';
    $key = 'user_confirmation';
    $to = $form_values['email'];
    $from = \Drupal::config('system.site')->get('mail');
    $params['message'] = $form_values;
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, $from, $send);

    if ($result['result'] !== true) {
      drupal_set_message(t('There was a problem sending your email confirmation but your submission was successful.'), 'error');
    }
  }

  /**
   * Send notification email
   */
  public function course_booking_notification_mail_send($form_values){
    
    $mailManager = \Drupal::service('plugin.manager.mail');

    //construct email to send to form submitter for notification
    $module = 'course_booking';
    $key = 'internal_notification';
    $to = 'rob.hooper@blackpool.ac.uk';
    $from = \Drupal::config('system.site')->get('mail');
    $params['message'] = $form_values;
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, $from, $send);
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'course_booking_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // 
    $form = parent::buildForm($form, $form_state);

       //minimum required course information for tracking in admissions
    $form['course'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Course Details'),
      '#weight' => 0
    ];

    $form['course']['course_code'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Course Code'),
      '#default_value' => NULL,
      '#required' => TRUE,
      '#disabled' => TRUE
    ];

    $form['course']['course_id'] = [      
      '#type' => 'hidden',
      '#title' => $this->t('Course ID'),
      '#default_value' => '',
      '#required' => TRUE,
      '#disabled' => TRUE
    ];

    $form['course']['course_title'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Course Title'),      
      '#default_value' => ' ',
      '#maxlength' => 255,
      '#required' => TRUE,
      '#disabled' => TRUE
    ];

    $form['course']['course_date'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Course Date'),      
      '#default_value' => '',
      '#maxlength' => 255,
      '#required' => TRUE,
      '#disabled' => TRUE
    ];

    //inject course details from URL variable
    $form['course']['course_title']['#default_value'] = $this->t(\Drupal::request()->get('course_title'));
    $form['course']['course_code']['#default_value'] = $this->t(\Drupal::request()->get('course_code'));
    $form['course']['course_id']['#default_value'] = $this->t(\Drupal::request()->get('course_id'));
    $form['course']['course_date']['#default_value'] = $this->t(\Drupal::request()->get('course_date'));        

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    //perform parent validation
    parent::validateForm($form, $form_state);
    //additional validation if required    
    $uio_id = $form_state->getValue('course_id');

    if(strlen(trim($uio_id)) == 0){
      $form_state->setErrorByName('course_code', $this->t('No course ID value is being submitted, you should only navigate to this form directly from the course which you wish to apply for'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    
    //perform parent submission
    parent::submitForm($form, $form_state);
    
    //send confirmation message to user
    $this->course_booking_confirmation_mail_send($form_state->getValues());
    //send notification email to ITS
    $this->course_booking_notification_mail_send($form_state->getValues());
        
    //log submission in watchdog
    $message = "Commercial Booking Form Submitted";
    foreach($form_state->getValues() as $value){
      $message = $message . ' ' . $value;
    }
    \Drupal::logger('course_booking')->info($message);
  }
}