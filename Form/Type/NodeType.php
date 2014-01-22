<?php
namespace Ydle\NodesBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class NodeType extends AbstractType
{    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder                                                   
                ->add('name', 'text', array('required' => true))
                ->add('code', 'integer', array('required' => true))
                ->add('description', 'textarea', array('required' => false))
                ->add('room', 'entity', array(
                    'class' => 'YdleRoomBundle:Room',
                    'property' => 'name',
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('t')
                                ->where('t.isActive = 1')
                                ->orderBy('t.name', 'ASC');
                    },
                ))
                ->add('types', 'entity', array(
                    'class' => 'YdleNodesBundle:SensorType',
                    'property' => 'name',
                    'multiple' => true,
                    'query_builder' => function(EntityRepository $er) {
                        return $er->createQueryBuilder('t')
                                ->where('t.isActive = 1')
                                ->orderBy('t.name', 'ASC');
                    },
                ))
                ->add('is_active', 'checkbox', array('label' => 'Actif ?', 'required' => false))
        ;
    }

    public function getName()
    {
        return 'node_form';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Ydle\NodesBundle\Entity\Node',
        ));
    }
}
?>
