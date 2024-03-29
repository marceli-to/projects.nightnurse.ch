<?php
namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Http\Requests\CompanyStoreRequest;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
  /**
   * Get a list of companies
   * 
   * @return \Illuminate\Http\Response
   */
  public function get()
  {
    return response()->json(Company::orderBy('owner', 'DESC')->orderBy('name')->get());
  }

  /**
   * Get a list users for a given company
   * 
   * @param Company $company
   * @return \Illuminate\Http\Response
   */
  public function getUsersByCompany(Company $company)
  {
    $company = Company::with('users')->findOrFail($company->id);
    return response()->json($company->users);
  }

  /**
   * Get a single companies for a given companies
   * 
   * @param Company $company
   * @return \Illuminate\Http\Response
   */
  public function find(Company $company)
  {
    return response()->json(Company::findOrFail($company->id));
  }

  /**
   * Store a newly created company
   *
   * @param  \Illuminate\Http\CompanyStoreRequest $request
   * @return \Illuminate\Http\Response
   */
  public function store(CompanyStoreRequest $request)
  {
    $company = Company::create([
      'uuid' => \Str::uuid(),
      'name' => $request->input('name'),
      'city' => $request->input('city') ? $request->input('city') : NULL,
      'owner' => 0,
      'publish' => 1,
      'vertec_id' => $request->input('vertec_id') ? $request->input('vertec_id') : NULL,
    ]);
    return response()->json(['companyId' => $company->id]);
  }

  /**
   * Update a company for a given company
   *
   * @param Company $company
   * @param  \Illuminate\Http\Request $request
   * @return \Illuminate\Http\Response
   */
  public function update(Company $company, Request $request)
  {
    $company = Company::findOrFail($company->id);
    $company->update($request->all());
    $company->save();
    return response()->json('successfully updated');
  }

  /**
   * Toggle the status a given company
   *
   * @param  Company $company
   * @return \Illuminate\Http\Response
   */
  public function toggle(Company $company)
  {
    $company->publish = $company->publish == 0 ? 1 : 0;
    $company->save();
    return response()->json($company->publish);
  }

  /**
   * Remove a company
   *
   * @param  Company $company
   * @return \Illuminate\Http\Response
   */
  public function destroy(Company $company)
  {
    $company->users()->delete();
    $company->delete();
    return response()->json('successfully deleted');
  }
}
