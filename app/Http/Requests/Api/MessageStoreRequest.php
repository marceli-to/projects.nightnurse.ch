<?php
namespace App\Http\Requests\Api;
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
      'subject' => 'nullable',
      'body' => 'nullable',
      'private' => 'nullable',
      'project_id' => 'required|exists:App\Models\Project,id',
      'sender_id' => 'required|exists:App\Models\User,id',
      'users' => 'required',
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
