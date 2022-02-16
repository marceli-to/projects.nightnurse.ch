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
      'project_state_id' => 'required|exists:App\Models\ProjectState,id',
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
      'number.required' => 'Nummer wird benötigt!',
      'name.required' => 'Name wird benötigt!',
      'user_id.required' => 'Projektmanager wird benötigt!',
      'project_state_id.required' => 'Projektstatus wird benötigt!',
    ];
  }
}
