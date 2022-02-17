<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class MessageStoreRequest extends FormRequest
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
      'user_id' => 'required|exists:App\Models\User,id',
      'project_id' => 'required|exists:App\Models\Project,id',
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
      'user_id.required' => 'Absender wird benötigt!',
      'project_id.required' => 'Projekt wird benötigt!',
    ];
  }
}
