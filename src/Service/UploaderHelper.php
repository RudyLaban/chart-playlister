<?php


namespace App\Service;


use App\Entity\Chart;
use Gedmo\Sluggable\Util\Urlizer;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
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

    private string|FilesystemOperator $publicUploadFilesystem;

    private RequestStackContext $requestStackContext;

    private LoggerInterface $logger;


    public function __construct(FilesystemOperator  $publicUploadFilesystem, RequestStackContext $requestStackContext, LoggerInterface $logger)
    {

        $this->publicUploadFilesystem = $publicUploadFilesystem;
        $this->requestStackContext = $requestStackContext;
        $this->logger = $logger;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param Chart $chart
     * @param string|null $existingFilename
     * @return string
     * @throws FilesystemException
     */
    public function uploadChartImage(UploadedFile $uploadedFile, Chart $chart, ?string $existingFilename): string
    {
        $originalFilename = $chart->getName();
        $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();

        $stream = fopen($uploadedFile->getPathname(), 'r');

        try {
            $this->publicUploadFilesystem->writeStream(self::CHART_IMAGE.'/'.$newFilename, $stream);
        } catch (\Exception)
        {
            $this->logger->alert(sprintf('La creation du fichier uploadé "%s" à échoué', $newFilename));
        }

        if (is_resource($stream))
        {
            fclose($stream);
        }

        if ($existingFilename)
        {
            try {
                $this->publicUploadFilesystem->delete($existingFilename);
            } catch (\Exception)
            {
                $this->logger->alert(sprintf('La suppression de l\'ancien fichier uploadé "%s" à échoué.', $existingFilename));
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