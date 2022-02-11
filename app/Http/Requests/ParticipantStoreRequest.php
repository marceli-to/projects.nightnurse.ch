<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ParticipantStoreRequest extends FormRequest
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
      'firstname' => 'required',
      'name' => 'required',
      'email' => 'required|email'
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
      'firstname.required' => [
        'field' => 'firstname',
        'error' => 'Vorname wird benötigt!'
      ],
      'name.required' => [
        'field' => 'name',
        'error' => 'Name wird benötigt!'
      ],
      'email.required' => [
        'field' => 'email',
        'error' => 'Email wird benötigt!'
      ],
      'email.email' => [
        'field' => 'email',
        'error' => 'Email ungültig!'
      ],
    ];
  }
}
