<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class ParticipantSubscriptionStoreRequest extends FormRequest
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
      'year' => 'required',
      'participant_id' => 'required'
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
      'year.required' => [
        'field' => 'year',
        'error' => 'Jahr wird benötigt!'
      ],
      'participant_id.required' => [
        'field' => 'participant_id',
        'error' => 'Mitglied wird benötigt!'
      ],
    ];
  }
}
