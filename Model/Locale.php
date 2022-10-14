<?php namespace Vankosoft\ApplicationBundle\Model;

use Sylius\Component\Locale\Model\Locale as BaseLocale;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;
use Vankosoft\ApplicationBundle\Model\Interfaces\LocaleInterface;

class Locale extends BaseLocale implements LocaleInterface
{
    use TranslatableTrait;
    
    /**
     * @var string|null
     */
    protected $title;
    
    /** @var string */
    protected $translatableLocale;
    
    /**
     * There is getName() Method at \Sylius\Component\Locale\Model\Locale
     * 
     * {@inheritDoc}
     * @see \Vankosoft\ApplicationBundle\Model\Interfaces\LocaleInterface::getTitle()
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
    
    public function setTitle($title): LocaleInterface
    {
        $this->title = $title;
        
        return $this;
    }
    
    public function getTranslatableLocale(): ?string
    {
        return $this->translatableLocale;
    }
    
    public function setTranslatableLocale($translatableLocale): LocaleInterface
    {
        $this->translatableLocale   = $translatableLocale;
        
        return $this;
    }
    
    /*
     * @NOTE: Decalared abstract in TranslatableTrait
     */
    protected function createTranslation(): TranslationInterface
    {
        
    }
}
