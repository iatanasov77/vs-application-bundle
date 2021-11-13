<?php namespace VS\CmsBundle\Component\Uploader;

use Gaufrette\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Webmozart\Assert\Assert;

use VS\CmsBundle\Component\Generator\FilePathGeneratorInterface;
use VS\CmsBundle\Component\Generator\UploadedFilePathGenerator;
use VS\CmsBundle\Model\FileInterface;

class FilemanagerUploader implements FileUploaderInterface
{
    /** @var Filesystem */
    protected $filesystem;
    
    /** @var FilePathGeneratorInterface */
    protected $filePathGenerator;
    
    public function __construct(
        Filesystem $filesystem,
        ?FilePathGeneratorInterface $filePathGenerator = null
    ) {
            $this->filesystem = $filesystem;
            
            if ( $filePathGenerator === null ) {
                @trigger_error( sprintf(
                    'Not passing an $filePathGenerator to %s constructor is deprecated since Sylius 1.6 and will be not possible in Sylius 2.0.',
                    self::class
                ), \E_USER_DEPRECATED );
            }
            
            $this->filePathGenerator = $filePathGenerator ?? new UploadedFilePathGenerator();
    }
    
    public function upload( FileInterface $filemanagerFile ): void
    {
        if ( ! $filemanagerFile->hasFile() ) {
            return;
        }
        
        $file = $filemanagerFile->getFile();
        
        /** @var File $file */
        Assert::isInstanceOf( $file, File::class );
        
        if ( null !== $filemanagerFile->getPath() && $this->has( $filemanagerFile->getPath() ) ) {
            $this->remove( $filemanagerFile->getPath() );
        }
        
        do {
            $path = $this->filePathGenerator->generate( $filemanagerFile );
        } while ( $this->isAdBlockingProne( $path ) || $this->filesystem->has( $path ) );
        
        $filemanagerFile->setPath( $path );
        
        $this->filesystem->write(
            $filemanagerFile->getPath(),
            file_get_contents( $filemanagerFile->getFile()->getPathname() )
        );
    }
    
    public function remove( string $path ): bool
    {
        if ( $this->filesystem->has( $path ) ) {
            return $this->filesystem->delete( $path );
        }
        
        return false;
    }
    
    private function has( string $path ): bool
    {
        return $this->filesystem->has( $path );
    }
    
    /**
     * Will return true if the path is prone to be blocked by ad blockers
     */
    private function isAdBlockingProne( string $path ): bool
    {
        return strpos( $path, 'ad' ) !== false;
    }
}
