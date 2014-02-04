<?php

namespace Display\PushBundle\Form;

use Display\PushBundle\Entity\DeviceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ExecutionContextInterface;

class PushType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('locale', 'locale', array(
                'label' => 'Locale',
                'attr' => array('class' => 'chosen'),
                'required' => false
            ))
            ->add('os', 'choice', array(
                'label' => 'Operating System',
                'choices' => DeviceRepository::getOperatingSystems(),
                'required' => false
            ))
            ->add('uid', 'textarea', array(
                'label' => 'Uid (separator ";")',
                'attr' => array('rows' => 2),
                'required' => false
            ))
            ->add('text', 'textarea', array(
                'label' => 'Texte',
                'attr' => array('rows' => 5),
                'constraints' => array(
                    new NotBlank(),
                    new Callback(function($text, ExecutionContextInterface $context) {
                        $data = array(array('aps' => array('alert' => $text)));
                        if (strlen(json_encode($data)) > 225) {
                            $context->addViolation('Le message risque d\'être trop long et de ne jamais partir.');
                        }

                        return $context;
                    })
                ),
            ))
        ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'display_push';
    }
}