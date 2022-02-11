<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
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
      'number' => 'required',
      'date' => 'required',
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
      'number.required' => [
        'field' => 'name',
        'error' => 'Nummer wird benötigt!'
      ],
      'date.required' => [
        'field' => 'date',
        'error' => 'Datum wird benötigt!'
      ],
    ];
  }
}
