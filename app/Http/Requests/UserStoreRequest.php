<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class UserStoreRequest extends FormRequest
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
      'email' => 'unique:App\Models\User,email|required|email',
      'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
      'password_confirmation' => 'min:8',
      'language_id' => 'required|exists:App\Models\Language,id',
      'company_id' => 'required|exists:App\Models\Company,id',
      'gender_id' => 'required|exists:App\Models\Gender,id',
      'role_id' => 'required|exists:App\Models\Role,id'
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
      'name.required' => 'Name wird benötigt!',
      'firstname.required' => 'Vorname wird benötigt!',
    ];
  }
}
