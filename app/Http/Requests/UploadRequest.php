<?php
namespace App\Http\Requests;
use App\Http\Requests\Api\FormRequest;

class UploadRequest extends FormRequest
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
      'file' => 'required|max:524288000',
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
      'file.required' => 'image_required',
      'file.max' => 'image_exceeds_max_size',
      // 'file.mimes' => 'image_type_not_allowed',
    ];
  }
}
