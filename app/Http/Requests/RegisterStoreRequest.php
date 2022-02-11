<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class RegisterStoreRequest extends FormRequest
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
      'participants.*.firstname' => 'required',
      'participants.*.name' => 'required',
      'participants.*.email' => 'required|email',
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
      'participants.*.firstname.required' => 'Vorname wird benötigt!',
      'participants.*.name.required' => 'Name wird benötigt!',
      'participants.*.email.required' => 'E-Mail wird benötigt!',
    ];
  }
}
