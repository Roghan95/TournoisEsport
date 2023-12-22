<?php
 
namespace App\Field;
 
use App\Form\MetaDataType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Vich\UploaderBundle\Form\Type\VichImageType;
 
final class VichImageField implements FieldInterface
{
    use FieldTrait;
    // public const OPTION_IMAGINE_FILTER = 'imagineFilter';
    public static function new(string $propertyName, ?string $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            // this template is used in 'index' and 'detail' pages
            // ->setTemplatePath('vich_image.html.twig')
            // this is used in 'edit' and 'new' pages to edit the field contents
            // you can use your own form types too
            ->setFormType(VichImageType::class)
            // ->addCssClass('field-vich-image')
            // ->setCustomOption(self::OPTION_IMAGINE_FILTER, 'admin_image')
        ;
    }
}