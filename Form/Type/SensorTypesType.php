<?php
namespace Ydle\NodesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class SensorTypesType extends AbstractType
{    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', 'text', array('required' => true))
                ->add('unit', 'text', array('required' => true))
                ->add('description', 'textarea', array('required' => false))
                ->add('is_active', 'checkbox', array('label' => 'Actif ?', 'required' => false))
        ;
    }

    public function getName()
    {
        return 'sensor_types';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ydle\NodesBundle\Entity\SensorType',
        ));
    }
}
?>
