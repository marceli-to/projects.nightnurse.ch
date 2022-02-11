<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class FileStoreRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'file' => 'required',
      'name'  => 'required',
    ];
  }

  /**
   * Custom message for validation
   *
   * @return array
   */
  public function messages()
  {
    return [
      'file.required' => [
        'field' => 'file',
        'error' => 'Datei wird benötigt!'
      ],
      'name.required' => [
        'field' => 'name',
        'error' => 'Name wird benötigt!'
      ],
    ];
  }
}
