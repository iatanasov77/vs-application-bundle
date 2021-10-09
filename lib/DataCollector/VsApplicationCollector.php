<?php namespace VS\ApplicationBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Tutorial: https://symfony.com/doc/current/profiler/data_collector.html
 * Example: Sylius\Bundle\CoreBundle\Collector\SyliusCollector
 */
final class VsApplicationCollector extends DataCollector
{
    public function __construct(
        string $projectDir,
        array $bundles,
        string $defaultLocaleCode
    ) {
        $this->data = [
            //'version'               => Kernel::VERSION,
            'version'               => file_get_contents( $projectDir . '/VSAPP_VERSION' ),
            'default_locale_code'   => $defaultLocaleCode,
            'locale_code'           => null,
            'extensions'            => [
                'VSUsersSubscriptionsBundle'    => ['name' => 'Subscription', 'enabled' => false],
                'VSPaymentBundle'               => ['name' => 'Payment', 'enabled' => false],
            ],
        ];
        
        foreach ( array_keys( $this->data['extensions'] ) as $bundleName ) {
            if ( isset( $bundles[$bundleName] ) ) {
                $this->data['extensions'][$bundleName]['enabled']   = true;
            }
        }
    }
    
    public function getVersion()
    {
        return $this->data['version'];
    }
    
    public function getExtensions(): array
    {
        return $this->data['extensions'];
    }
    
    /**
     * @return string
     */
    public function getLocaleCode(): ?string
    {
        return $this->data['locale_code'];
    }
    
    /**
     * @return string
     */
    public function getDefaultLocaleCode(): ?string
    {
        return $this->data['default_locale_code'];
    }
    
    public function collect( Request $request, Response $response, \Throwable $exception = null )
    {
        
    }
    
    public function reset(): void
    {
        $this->data['locale_code'] = null;
    }
    
    public function getName(): string
    {
        return 'vs_application.core_collector';
    }
}
