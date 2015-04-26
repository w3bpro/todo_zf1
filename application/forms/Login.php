<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
		$this->clearDecorators();
        $this->addDecorator('FormElements', array('tag' => 'div') )
             ->addDecorator('Form');

        $this->setMethod('post');

        $this->addElement('text', 'email', array(
            'ignore'   => true,
            'placeholder'=> 'Your email:',
            'label' => 'E-mail:',
            'class' => 'form-control',
            'required'   => true,
            'id'       => 'login-email',
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            )
        ));

        $this->addElement('password', 'pass', array(
            'ignore'   => true,
            'placeholder'=> 'Your password:',
            'label' => 'Password:',
            'class' => 'form-control',
            'id'       => 'login-pass',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array('min' => 5, 'max' => 50))
            )
        ));

        $this->addElement('hidden', 'submitClear', array(
            'ignore'   => true,
            'required' => false,
            'class' => 'submitClear',
            'decorators' => array(
                array('HtmlTag', array(
                    'tag' => 'div',
                    'class' => 'clearfix cfx20'
                ))
            )
        ));

        $this->submitClear->clearValidators();

        $this->addElement('button', 'submit', array(
            'ignore'   => true,
            'type'     => 'submit',
            'id'       => 'log-in-btn',
            'label'    => 'Log in',
            'class'    => 'btn btn-primary btn-lg btn-center'
        ));
        /*
        $this->addElement('hash', 'csrf', array(
            'ignore' => true,
            'required' => false,
            'id' => 'sign-csrf'
        ));
        */

        foreach ($this->getElements() as $element)
        {

            if(in_array($element->helper, array('formText', 'formPassword') ) ) {
                $element->addDecorator('Label', array('tag' => 'span'))
                        ->removeDecorator('HtmlTag');
            }
            else if(in_array($element->helper, array('formSubmit','formButton','formHidden') ) && $element->class != 'submitClear' ) {
                $element->setDecorators(array('ViewHelper'));
                        //->addDecorator('HtmlTag', array('tag' => 'div'));
            }
        } 
    }


}

