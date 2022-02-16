<?php
namespace App\Http\Controllers\Api\Settings;
use App\Http\Controllers\Controller;
use App\Http\Resources\DataCollection;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
  /**
   * Get a list of languages
   * 
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    return new DataCollection(Language::get());
  }

  /**
   * Get a single language
   * 
   * @param Language $language
   * @return \Illuminate\Http\Response
   */
  public function find(Language $language)
  {
    return response()->json(Language::findOrFail($language->id));
  }
}
