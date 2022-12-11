<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ReactionStoreRequest extends FormRequest
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
      'message_uuid' => 'required|exists:App\Models\Message,uuid',
      'type_uuid' => 'required|exists:App\Models\ReactionType,uuid',
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
