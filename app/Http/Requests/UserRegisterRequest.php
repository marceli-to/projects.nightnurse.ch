<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
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
      'firstname' => 'nullable',
      'name' => 'nullable',
      'phone' => 'nullable',
      'email' => 'unique:App\Models\User,email|required|email',
      'company_id' => 'required|exists:App\Models\Company,id',
      'gender_id' => 'required|exists:App\Models\Gender,id',
      'language_id' => 'required|exists:App\Models\Language,id'
    ];
  }

  /**
   * Custom message for validation
   *
   * @return array
   */
  public function messages()
  {
    return [];
  }
}
