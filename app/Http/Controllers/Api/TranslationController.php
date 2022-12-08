<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
  /**
   * Get the translation for a message
   * 
   * @param Request $request
   * @return \Illuminate\Http\Response
   */

  public function get(Request $request)
  {
    $translator = new \DeepL\Translator(env('DEEPL_API_KEY'));
    $result = $translator->translateText(
      $request->input('message'), 
      null, 
      $request->input('language'), 
    );
    return response()->json(['message' => $result->text]);
  }
}