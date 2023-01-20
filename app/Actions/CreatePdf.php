<?php
namespace App\Actions;
use PDF as DomPDF;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CreatePdf
{
  protected $storageFolder;
  protected $storageFolderUuid;

  public function __construct()
  {
    $this->storageFolderUuid = \Str::uuid();
    $this->storageFolder = '/storage/quotes/' . $this->storageFolderUuid . '/';
    if (!File::isDirectory(storage_path('app/public/quotes/' . $this->storageFolderUuid)))
    {
      File::makeDirectory(storage_path('app/public/quotes/' . $this->storageFolderUuid), 0775, true, true);
    }
  }

  public function execute($data = [])
  {
    $this->viewData['data'] = $data;
    $file = 'nightnurse-images-offerte-' . \Carbon\Carbon::parse($data['quote']['LastModifiedDate'])->toDateString() . '-' . $data['quote']['QuoteNumber'] . '.pdf';
    $pdf = Pdf::loadView('quote.pdf.index', $this->viewData);
    $pdf->save(storage_path('app/public/quotes') . '/' . $this->storageFolderUuid .'/'. $file);
    return [
      'download_uri' => $this->storageFolder . $file,
      'name' => $file
    ];
  }


}
