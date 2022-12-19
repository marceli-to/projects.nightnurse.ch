<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
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
      'name' => 'required',
      'user_id' => 'required|exists:App\Models\User,id',
      'company.id' => 'required|exists:App\Models\Company,id',
      'project_state_id' => 'required|exists:App\Models\ProjectState,id',
      'users' => 'nullable',
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
