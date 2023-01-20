<?php
namespace App\Http\Controllers;
use App\Services\Quote;
use App\Actions\CreatePdf;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
  protected $viewPath = 'quote.';

  public function __construct(Quote $quote)
  {
    $this->quote = $quote;
  }

  /**
   * Page: 'Quote'
   * 
   * @param String $slug
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */

  public function index($slug = NULL, Request $request, CreatePdf $createPdf)
  { 
    // @todo: get language param and set locale
    // \App::setLocale('en');
    // Get quote
    $data = $this->quote->get($request->key);

    // Abort if no records found
    if (isset($data['quoteItems']) && count($data['quoteItems']) < 1)
    {
      abort(404);
    }

    // Create PDF
    $pdf = $createPdf->execute($data);

    return view($this->viewPath . 'index', ['data' => $data, 'pdf' => $pdf]);
  }
}
