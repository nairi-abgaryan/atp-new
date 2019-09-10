<?php

namespace App\Entity\Base;

use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Trait PdfEntity
 * @package App\Entity\Base
 * @Vich\Uploadable
 */
trait PdfEntity
{
    /**
     * @ORM\Column(type="string", length=256, nullable=false)
     * @var string
     */
    private $pdf;

    /**
     * @Vich\UploadableField(mapping="news_pdf", fileNameProperty="pdf")
     * @var File
     */
    private $pdfFile;

    public function getPdf()
    {
        return $this->pdf;
    }

    public function setPdf($pdf)
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function setPdfFile(File $pdf = null)
    {
        $this->pdfFile = $pdf;

        if ($pdf) {
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getPdfFile()
    {
        return $this->pdfFile;
    }
}