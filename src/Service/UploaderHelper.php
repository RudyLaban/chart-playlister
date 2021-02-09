<?php


namespace App\Service;


use App\Entity\Chart;
use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Context\RequestStackContext;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Service chargé du traitement des upload des pochettes de Chart
 *
 * Class UploaderHelper
 * @package App\Service
 */
class UploaderHelper
{
    const CHART_IMAGE = 'chart_image';

    /**
     * @var string
     */
    private $publicUploadFilesystem;
    /**
     * @var RequestStackContext
     */
    private $requestStackContext;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(FilesystemInterface $publicUploadFilesystem, RequestStackContext $requestStackContext, LoggerInterface $logger)
    {

        $this->publicUploadFilesystem = $publicUploadFilesystem;
        $this->requestStackContext = $requestStackContext;
        $this->logger = $logger;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param Chart $chart
     * @return string
     */
    public function uploadChartImage(UploadedFile $uploadedFile, Chart $chart, ?string $existingFilename): string
    {
        //$destination = $this->publicUploadFilesystem.'/'.self::CHART_IMAGE;

        $originalFilename = $chart->getName();
        $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();

        $stream = fopen($uploadedFile->getPathname(), 'r');
        $result = $this->publicUploadFilesystem->writeStream(
            self::CHART_IMAGE.'/'.$newFilename,
            $stream
        );

        if ($result === false)
        {
            throw new \Exception(sprintf('La creation du fichier uploadé "%s" à échoué', $newFilename));
        }

        if (is_resource($stream))
        {
            fclose($stream);
        }

        if ($existingFilename)
        {
            try {
                $result = $this->publicUploadFilesystem->delete($existingFilename);

                if ($result === false)
                {
                    throw new \Exception(sprintf('La suppression de l\'ancien fichier uploadé "%s" à échoué', $existingFilename));
                }
            } catch (FileNotFoundException $e) {
                $this->logger->alert(sprintf('L\'ancien fichier uploadé "%s" n\'a pas été trouvé lors de sa suppression.', $existingFilename));
            }
        }

        return $newFilename;
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
                ->getBasePath().'/uploads/'.$path;
    }
}