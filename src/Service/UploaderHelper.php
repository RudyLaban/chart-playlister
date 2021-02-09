<?php


namespace App\Service;


use App\Entity\Chart;
use Gedmo\Sluggable\Util\Urlizer;
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
    private $uploadsPath;
    /**
     * @var RequestStackContext
     */
    private $requestStackContext;

    public function __construct(string $uploadsPath, RequestStackContext $requestStackContext)
    {

        $this->uploadsPath = $uploadsPath;
        $this->requestStackContext = $requestStackContext;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param Chart $chart
     * @return string
     */
    public function uploadChartImage(UploadedFile $uploadedFile, Chart $chart): string
    {
        $destination = $this->uploadsPath.'/'.self::CHART_IMAGE;

        $originalFilename = $chart->getName();
        $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();

        $uploadedFile->move(
            $destination,
            $newFilename
        );

        return $newFilename;
    }

    public function getPublicPath(string $path): string
    {
        return $this->requestStackContext
                ->getBasePath().'/uploads/'.$path;
    }
}