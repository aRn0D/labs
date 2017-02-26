<?php

namespace Al\AppBundle\Form;

use Al\Component\Employee\Command\PromoteEmployee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class EmployeeType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('position')
            ->add('salaryScale')
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $employee = $event->getData();

            if ($employee instanceof PromoteEmployee) {
                $form = $event->getForm();
                $form->remove('name');
            }
        });
    }
}